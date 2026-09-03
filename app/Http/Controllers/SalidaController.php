<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Salida;
use App\Models\SalidaDetalle;
use App\Models\SolicitudMaterial;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalidaController extends Controller
{
    /**
     * Estatus que permiten generar salidas de almacén.
     */
    private const ESTATUS_SOLICITUD_PERMITIDOS = [
        'aprobado',
        'pendiente',
    ];

    /**
     * Mostrar el listado de salidas.
     */
    public function index()
    {
        $salidas = Salida::with([
            /*
             * Cliente se conserva para salidas históricas
             * registradas antes de vincularlas con solicitudes.
             */
            'cliente',
            'user',
            'solicitudMaterial.user',
            'detalles.inventario',
        ])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view(
            'salidas.index',
            compact('salidas')
        );
    }

    /**
     * Mostrar el formulario para registrar una salida.
     */
    public function create()
    {
        return view('salidas.create');
    }

    public function buscarSolicitudes(Request $request)
    {
        $validated = $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $termino = trim(
            (string) ($validated['q'] ?? '')
        );

        $solicitudId = $this->obtenerIdBusqueda(
            $termino
        );

        $fechaBusqueda =
            $this->normalizarFechaBusqueda(
                $termino
            );

        if (
            $termino === '' ||
            (
                $solicitudId === null &&
                $fechaBusqueda === null &&
                mb_strlen($termino) < 2
            )
        ) {
            return response()->json([]);
        }

        $solicitudes = SolicitudMaterial::query()
            ->select([
                'id',
                'user_id',
                'personal_id',
                'destino',
                'estatus',
                'created_at',
            ])
            ->with([
                'user:id,name,email,num_empleado',
                'operadorPersonal:id,nombre_completo,employee_id',
                'detalles:id,solicitud_material_id,inventario_id,cantidad_solicitada',
                'salidas:id,solicitud_material_id',
                'salidas.detalles:id,salida_id,inventario_id,cantidad',
            ])
            ->whereIn(
                'estatus',
                self::ESTATUS_SOLICITUD_PERMITIDOS
            )
            ->where(function ($query) use (
                $termino,
                $solicitudId,
                $fechaBusqueda
            ) {
                /*
                 * Se inicia con destino para construir correctamente
                 * el grupo de condiciones OR.
                 */
                $query->where(
                    'destino',
                    'LIKE',
                    "%{$termino}%"
                );

                if ($solicitudId !== null) {
                    $query->orWhere(
                        'id',
                        $solicitudId
                    );
                }

                if ($fechaBusqueda !== null) {
                    $query->orWhereDate(
                        'created_at',
                        $fechaBusqueda
                    );
                }

                $query->orWhereHas(
                    'user',
                    function ($userQuery) use (
                        $termino
                    ) {
                        $userQuery
                            ->where(
                                'name',
                                'LIKE',
                                "%{$termino}%"
                            )
                            ->orWhere(
                                'email',
                                'LIKE',
                                "%{$termino}%"
                            )
                            ->orWhere(
                                'num_empleado',
                                'LIKE',
                                "%{$termino}%"
                            );
                    }
                );

                $query->orWhereHas(
                    'operadorPersonal',
                    function ($personalQuery) use (
                        $termino
                    ) {
                        $personalQuery
                            ->where(
                                'nombre_completo',
                                'LIKE',
                                "%{$termino}%"
                            )
                            ->orWhere(
                                'employee_id',
                                'LIKE',
                                "%{$termino}%"
                            );
                    }
                );
            })
            ->when(
                $solicitudId !== null,
                function ($query) use (
                    $solicitudId
                ) {
                    /*
                     * Una coincidencia exacta por ID
                     * se coloca al principio.
                     */
                    $query->orderByRaw(
                        'CASE ' .
                        'WHEN solicitud_materiales.id = ? ' .
                        'THEN 0 ELSE 1 END',
                        [$solicitudId]
                    );
                }
            )
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($solicitud) {
                $pendientes =
                    $this
                        ->calcularCantidadesPendientes(
                            $solicitud
                        )
                        ->filter(
                            function ($cantidad) {
                                return $cantidad > 0;
                            }
                        );

                /*
                 * Una solicitud completamente entregada
                 * ya no debe aparecer en el buscador.
                 */
                if ($pendientes->isEmpty()) {
                    return null;
                }

                return [
                    'id' => $solicitud->id,

                    'folio' => str_pad(
                        (string) $solicitud->id,
                        4,
                        '0',
                        STR_PAD_LEFT
                    ),

                    'estatus' =>
                        $solicitud->estatus,

                    'solicitante' =>
                        $solicitud->user?->name ??
                        'Usuario no disponible',

                    'numero_empleado' =>
                        $solicitud
                            ->user
                            ?->num_empleado ??
                        'N/A',

                    'destino' =>
                        $solicitud->destino ??
                        'Sin destino',

                    'fecha_solicitud' =>
                        $solicitud
                            ->created_at
                            ?->format('d/m/Y'),

                    'hora_solicitud' =>
                        $solicitud
                            ->created_at
                            ?->format('H:i'),

                    'operador' =>
                        $solicitud
                            ->operadorPersonal
                            ?->nombre_completo ??
                        'Sin operador',

                    'productos_pendientes' =>
                        $pendientes->count(),

                    'unidades_pendientes' =>
                        (int) $pendientes->sum(),
                ];
            })
            ->filter()
            ->take(10)
            ->values();

        return response()->json(
            $solicitudes
        );
    }

    /**
     * Obtener los datos y materiales pendientes de una solicitud.
     */
    public function obtenerSolicitud(
        SolicitudMaterial $solicitud
    ) {
        if (
            !$this->solicitudPermiteSalida(
                $solicitud
            )
        ) {
            abort(
                404,
                'La solicitud fue rechazada o no está disponible.'
            );
        }

        $solicitud->load([
            'user:id,name,email,num_empleado,role',
            'operadorPersonal:id,nombre_completo,employee_id,area,grado',
            'detalles.inventario:id,nombre_producto,economico,categoria,medida,existencia,precio_total',
            'salidas.detalles',
        ]);

        $cantidadesPendientes =
            $this->calcularCantidadesPendientes(
                $solicitud
            );

        $productos = $solicitud
            ->detalles
            ->groupBy('inventario_id')
            ->map(
                function (
                    $detalles,
                    $inventarioId
                ) use (
                    $cantidadesPendientes
                ) {
                    $detalle =
                        $detalles->first();

                    $inventario =
                        $detalle->inventario;

                    if (!$inventario) {
                        return null;
                    }

                    $cantidadSolicitada =
                        (int) $detalles->sum(
                            'cantidad_solicitada'
                        );

                    $cantidadPendiente =
                        (int) $cantidadesPendientes
                            ->get(
                                $inventarioId,
                                0
                            );

                    $cantidadEntregada = max(
                        0,
                        $cantidadSolicitada -
                            $cantidadPendiente
                    );

                    if (
                        $cantidadPendiente === 0
                    ) {
                        return null;
                    }

                    return [
                        'inventario_id' =>
                            $inventario->id,

                        'codigo' =>
                            $inventario->economico,

                        'nombre_producto' =>
                            $inventario
                                ->nombre_producto,

                        'categoria' =>
                            $inventario->categoria,

                        'medida' =>
                            $inventario->medida,

                        'existencia' =>
                            (int) $inventario
                                ->existencia,

                        'cantidad_solicitada' =>
                            $cantidadSolicitada,

                        'cantidad_entregada' =>
                            $cantidadEntregada,

                        'cantidad_pendiente' =>
                            $cantidadPendiente,

                        'precio_unitario' =>
                            (float) $inventario
                                ->getPrecioPromedio(),
                    ];
                }
            )
            ->filter()
            ->values();

        return response()->json([
            'id' => $solicitud->id,

            'estatus' =>
                $solicitud->estatus,

            'destino' =>
                $solicitud->destino,

            'comentario' =>
                $solicitud->comentario,

            'fecha_solicitud' =>
                $solicitud
                    ->created_at
                    ?->format('d/m/Y'),

            'solicitante' => [
                'nombre' =>
                    $solicitud->user?->name ??
                    'N/A',

                'numero_empleado' =>
                    $solicitud
                        ->user
                        ?->num_empleado ??
                    'N/A',

                'email' =>
                    $solicitud->user?->email ??
                    'N/A',

                'area' =>
                    $solicitud->user?->role ??
                    'N/A',
            ],

            'operador' => [
                'nombre' =>
                    $solicitud
                        ->operadorPersonal
                        ?->nombre_completo ??
                    'N/A',

                'numero_empleado' =>
                    $solicitud
                        ->operadorPersonal
                        ?->employee_id ??
                    'N/A',

                'area' =>
                    $solicitud
                        ->operadorPersonal
                        ?->area ??
                    'N/A',

                'puesto' =>
                    $solicitud
                        ->operadorPersonal
                        ?->grado ??
                    'N/A',
            ],

            'productos' =>
                $productos,

            'completada' =>
                $productos->isEmpty(),
        ]);
    }

    /**
     * Registrar una salida vinculada con una solicitud.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'solicitud_material_id' => [
                'required',
                'integer',
                'exists:solicitud_materiales,id',
            ],

            'fecha_salida' => [
                'required',
                'date',
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:500',
            ],

            'productos' => [
                'required',
                'array',
                'min:1',
            ],

            'productos.*.inventario_id' => [
                'required',
                'integer',
                'exists:inventarios,id',
            ],

            'productos.*.cantidad' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        try {
            $salida = DB::transaction(
                function () use ($validated) {
                    /*
                     * El bloqueo impide que dos procesos
                     * registren simultáneamente la misma entrega.
                     */
                    $solicitud =
                        SolicitudMaterial::query()
                            ->with([
                                'user',
                                'detalles',
                                'salidas.detalles',
                            ])
                            ->whereKey(
                                $validated[
                                    'solicitud_material_id'
                                ]
                            )
                            ->lockForUpdate()
                            ->first();

                    if (
                        !$solicitud ||
                        !$this
                            ->solicitudPermiteSalida(
                                $solicitud
                            )
                    ) {
                        throw ValidationException::withMessages([
                            'solicitud_material_id' =>
                                'La solicitud seleccionada fue rechazada o no está disponible.',
                        ]);
                    }

                    $cantidadesPendientes =
                        $this
                            ->calcularCantidadesPendientes(
                                $solicitud
                            );

                    /*
                     * Agrupar materiales repetidos para evitar
                     * dos líneas del mismo artículo.
                     */
                    $productos = collect(
                        $validated['productos']
                    )
                        ->groupBy(
                            function ($producto) {
                                return (int) $producto[
                                    'inventario_id'
                                ];
                            }
                        )
                        ->map(
                            function (
                                $productosAgrupados,
                                $inventarioId
                            ) {
                                return [
                                    'inventario_id' =>
                                        (int) $inventarioId,

                                    'cantidad' =>
                                        (int) $productosAgrupados
                                            ->sum('cantidad'),
                                ];
                            }
                        )
                        ->values();

                    /*
                     * Comprobar que cada material pertenezca
                     * a la solicitud y no exceda lo pendiente.
                     */
                    foreach (
                        $productos as $producto
                    ) {
                        $inventarioId =
                            $producto[
                                'inventario_id'
                            ];

                        $cantidadPendiente =
                            $cantidadesPendientes
                                ->get($inventarioId);

                        if (
                            $cantidadPendiente === null
                        ) {
                            throw ValidationException::withMessages([
                                'productos' =>
                                    'Uno de los productos no pertenece a la solicitud.',
                            ]);
                        }

                        if (
                            $producto['cantidad'] >
                            $cantidadPendiente
                        ) {
                            throw ValidationException::withMessages([
                                'productos' =>
                                    "La cantidad del producto {$inventarioId} " .
                                    "supera lo pendiente por entregar " .
                                    "({$cantidadPendiente}).",
                            ]);
                        }
                    }

                    /*
                     * Bloquear los materiales mientras se valida
                     * y registra el movimiento de inventario.
                     */
                    $inventarios =
                        Inventario::query()
                            ->whereIn(
                                'id',
                                $productos->pluck(
                                    'inventario_id'
                                )
                            )
                            ->lockForUpdate()
                            ->get()
                            ->keyBy('id');

                    foreach (
                        $productos as $producto
                    ) {
                        $inventario =
                            $inventarios->get(
                                $producto[
                                    'inventario_id'
                                ]
                            );

                        if (!$inventario) {
                            throw ValidationException::withMessages([
                                'productos' =>
                                    'Uno de los productos ya no existe.',
                            ]);
                        }

                        if (
                            $inventario->existencia <
                            $producto['cantidad']
                        ) {
                            throw ValidationException::withMessages([
                                'productos' =>
                                    'Stock insuficiente para ' .
                                    "{$inventario->nombre_producto}. " .
                                    "Disponible: {$inventario->existencia}.",
                            ]);
                        }
                    }

                    $salida = Salida::create([
                        'solicitud_material_id' =>
                            $solicitud->id,

                        /*
                         * Las nuevas salidas ya no utilizan
                         * el catálogo de clientes.
                         */
                        'cliente_id' => null,

                        'fecha_salida' =>
                            $validated['fecha_salida'],

                        'observaciones' =>
                            $validated[
                                'observaciones'
                            ] ?? null,

                        'user_id' =>
                            auth()->id(),
                    ]);

                    foreach (
                        $productos as $producto
                    ) {
                        $inventario =
                            $inventarios->get(
                                $producto[
                                    'inventario_id'
                                ]
                            );

                        SalidaDetalle::create([
                            'salida_id' =>
                                $salida->id,

                            'inventario_id' =>
                                $inventario->id,

                            'cantidad' =>
                                $producto['cantidad'],

                            'precio_unitario' =>
                                $inventario
                                    ->getPrecioPromedio(),
                        ]);
                    }

                    return $salida
                        ->refresh()
                        ->load([
                            'solicitudMaterial.user',
                            'detalles.inventario',
                        ]);
                }
            );

            $nombreSolicitante =
                $salida
                    ->solicitudMaterial
                    ?->user
                    ?->name ??
                'N/A';

            $destino =
                $salida
                    ->solicitudMaterial
                    ?->destino ??
                'N/A';

            /*
             * Solo se almacenan los campos utilizados
             * por el resumen de create.blade.php.
             */
            session()->flash(
                'salida_reciente',
                [
                    'fecha' =>
                        $salida
                            ->created_at
                            ->format(
                                'd/m/Y H:i'
                            ),

                    'cliente_nombre' =>
                        $nombreSolicitante,

                    'cliente_area' =>
                        $destino,

                    'cantidad_productos' =>
                        $salida
                            ->cantidad_productos,

                    'subtotal' =>
                        $salida->precio_total,

                    'iva' =>
                        $salida->iva,

                    'total' =>
                        $salida
                            ->total_con_iva,
                ]
            );

            return redirect()
                ->route(
                    'salidas.show',
                    $salida
                )
                ->with(
                    'success',
                    'Salida registrada correctamente'
                );
        } catch (
            ValidationException $exception
        ) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withErrors([
                    'error' =>
                        'No se pudo registrar la salida. Inténtalo nuevamente.',
                ])
                ->withInput();
        }
    }

    /**
     * Mostrar el detalle de una salida.
     */
    public function show(Salida $salida)
    {
        $salida->load([
            /*
             * Cliente se conserva para mostrar
             * correctamente registros históricos.
             */
            'cliente',
            'user',
            'solicitudMaterial.user',
            'solicitudMaterial.operadorPersonal',
            'detalles.inventario',
        ]);

        return view(
            'salidas.show',
            compact('salida')
        );
    }

    /**
     * Descargar el PDF de la salida.
     */
    public function generatePDF(Salida $salida)
    {
        $salida->load(
            $this->relacionesParaPdf()
        );

        $datosFirmas =
            $this->obtenerDatosFirmas(
                $salida
            );

        $pdf = Pdf::loadView(
            'salidas.pdf',
            array_merge(
                ['salida' => $salida],
                $datosFirmas
            )
        )->setPaper(
            'letter',
            'portrait'
        );

        $folio =
            $salida->numero_factura ??
            'SAL-' . str_pad(
                (string) $salida->id,
                6,
                '0',
                STR_PAD_LEFT
            );

        $nombreArchivo = preg_replace(
            '/[^A-Za-z0-9_-]/',
            '-',
            $folio
        );

        return $pdf->download(
            'vale-salida-' .
            $nombreArchivo .
            '.pdf'
        );
    }

    /**
     * Visualizar el PDF de la salida.
     */
    public function viewPDF(Salida $salida)
    {
        $salida->load(
            $this->relacionesParaPdf()
        );

        $datosFirmas =
            $this->obtenerDatosFirmas(
                $salida
            );

        $pdf = Pdf::loadView(
            'salidas.pdf',
            array_merge(
                ['salida' => $salida],
                $datosFirmas
            )
        )->setPaper(
            'letter',
            'portrait'
        );

        return $pdf->stream(
            'vale-salida.pdf'
        );
    }

    /**
     * Interpretar el término como ID de solicitud.
     */
    private function obtenerIdBusqueda(
        string $termino
    ): ?int {
        if (
            !preg_match(
                '/^#?\s*0*(\d+)$/',
                $termino,
                $coincidencia
            )
        ) {
            return null;
        }

        return (int) $coincidencia[1];
    }

    /**
     * Convertir una fecha del buscador al formato de MySQL.
     */
    private function normalizarFechaBusqueda(
        string $termino
    ): ?string {
        /*
         * Formatos:
         * 02/09/2026
         * 2/9/2026
         * 02-09-2026
         */
        if (
            preg_match(
                '/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/',
                $termino,
                $coincidencia
            )
        ) {
            $dia =
                (int) $coincidencia[1];

            $mes =
                (int) $coincidencia[2];

            $anio =
                (int) $coincidencia[3];

            if (
                checkdate(
                    $mes,
                    $dia,
                    $anio
                )
            ) {
                return sprintf(
                    '%04d-%02d-%02d',
                    $anio,
                    $mes,
                    $dia
                );
            }

            return null;
        }

        /*
         * Formato:
         * 2026-09-02
         */
        if (
            preg_match(
                '/^(\d{4})-(\d{1,2})-(\d{1,2})$/',
                $termino,
                $coincidencia
            )
        ) {
            $anio =
                (int) $coincidencia[1];

            $mes =
                (int) $coincidencia[2];

            $dia =
                (int) $coincidencia[3];

            if (
                checkdate(
                    $mes,
                    $dia,
                    $anio
                )
            ) {
                return sprintf(
                    '%04d-%02d-%02d',
                    $anio,
                    $mes,
                    $dia
                );
            }
        }

        return null;
    }

    /**
     * Indicar si una solicitud puede generar una salida.
     */
    private function solicitudPermiteSalida(
        SolicitudMaterial $solicitud
    ): bool {
        return in_array(
            $solicitud->estatus,
            self::ESTATUS_SOLICITUD_PERMITIDOS,
            true
        );
    }

    /**
     * Calcular cantidades pendientes por material.
     */
    private function calcularCantidadesPendientes(
        SolicitudMaterial $solicitud
    ) {
        $cantidadesSolicitadas =
            $solicitud
                ->detalles
                ->groupBy('inventario_id')
                ->map(
                    function ($detalles) {
                        return (int) $detalles
                            ->sum(
                                'cantidad_solicitada'
                            );
                    }
                );

        $cantidadesEntregadas =
            $solicitud
                ->salidas
                ->flatMap(
                    function ($salida) {
                        return $salida
                            ->detalles;
                    }
                )
                ->groupBy('inventario_id')
                ->map(
                    function ($detalles) {
                        return (int) $detalles
                            ->sum('cantidad');
                    }
                );

        return $cantidadesSolicitadas
            ->map(
                function (
                    $cantidadSolicitada,
                    $inventarioId
                ) use (
                    $cantidadesEntregadas
                ) {
                    return max(
                        0,
                        $cantidadSolicitada -
                            $cantidadesEntregadas
                                ->get(
                                    $inventarioId,
                                    0
                                )
                    );
                }
            );
    }

    /**
     * Relaciones necesarias para generar el PDF.
     */
    private function relacionesParaPdf(): array
    {
        return [
            /*
             * Cliente se conserva para PDFs
             * de salidas históricas.
             */
            'cliente',
            'user',
            'detalles.inventario',
            'solicitudMaterial.user',
            'solicitudMaterial.operadorPersonal',
        ];
    }

    /**
     * Convertir una firma en Base64 compatible con DomPDF.
     */
    private function obtenerFirmaDataUri(
        ?User $usuario
    ): ?string {
        if (
            !$usuario ||
            !$usuario->signature
        ) {
            return null;
        }

        $rutaFirma = storage_path(
            'app/public/' .
            ltrim(
                $usuario->signature,
                '/\\'
            )
        );

        if (
            !is_file($rutaFirma) ||
            !is_readable($rutaFirma)
        ) {
            return null;
        }

        $mimeType =
            mime_content_type(
                $rutaFirma
            ) ?: 'image/png';

        $formatosPermitidos = [
            'image/png',
            'image/jpeg',
            'image/gif',
        ];

        if (
            !in_array(
                $mimeType,
                $formatosPermitidos,
                true
            )
        ) {
            return null;
        }

        $contenidoFirma =
            file_get_contents(
                $rutaFirma
            );

        if ($contenidoFirma === false) {
            return null;
        }

        return 'data:' .
            $mimeType .
            ';base64,' .
            base64_encode(
                $contenidoFirma
            );
    }

    /**
     * Obtener los firmantes del vale de salida.
     */
    private function obtenerDatosFirmas(
        Salida $salida
    ): array {
        $firmanteAutorizador =
            User::query()
                ->where(
                    'email',
                    'direccion@cman.com'
                )
                ->first();

        $firmanteAlmacen =
            User::query()
                ->where(
                    'email',
                    'almacen@cman.com'
                )
                ->first();

        $firmanteSolicitante =
            $salida
                ->solicitudMaterial
                ?->user;

        return [
            'firmanteAutorizador' =>
                $firmanteAutorizador,

            'firmanteAlmacen' =>
                $firmanteAlmacen,

            'firmaAutorizador' =>
                $this->obtenerFirmaDataUri(
                    $firmanteAutorizador
                ),

            'firmaAlmacen' =>
                $this->obtenerFirmaDataUri(
                    $firmanteAlmacen
                ),

            'firmaSolicitante' =>
                $this->obtenerFirmaDataUri(
                    $firmanteSolicitante
                ),
        ];
    }
}