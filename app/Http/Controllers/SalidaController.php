<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Salida;
use App\Models\User;
use App\Models\SalidaDetalle;
use App\Models\Inventario;
use App\Models\SolicitudMaterial;
use Barryvdh\DomPDF\Facade\Pdf;

class SalidaController extends Controller
{
    public function index()
    {
        $salidas = Salida::with(['cliente', 'user', 'detalles.inventario'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('salidas.index', compact('salidas'));
    }

    /**
     * Mostrar el formulario para registrar una salida.
     */
    public function create()
    {
        $solicitudesAprobadas = SolicitudMaterial::query()
            ->with([
                'user:id,name,email,num_empleado,role',
                'operadorPersonal:id,nombre_completo,employee_id,area,grado',
                'detalles.inventario:id,nombre_producto,economico,categoria,medida,existencia,precio_total',
                'salidas.detalles',
            ])
            ->where('estatus', 'aprobado')
            ->orderByDesc('created_at')
            ->get()
            ->filter(function ($solicitud) {
                $cantidadesSolicitadas = $solicitud->detalles
                    ->groupBy('inventario_id')
                    ->map(function ($detalles) {
                        return (int) $detalles->sum(
                            'cantidad_solicitada'
                        );
                    });

                $cantidadesEntregadas = $solicitud->salidas
                    ->flatMap(function ($salida) {
                        return $salida->detalles;
                    })
                    ->groupBy('inventario_id')
                    ->map(function ($detalles) {
                        return (int) $detalles->sum('cantidad');
                    });

                return $cantidadesSolicitadas->contains(
                    function ($cantidadSolicitada, $inventarioId) use (
                        $cantidadesEntregadas
                    ) {
                        return $cantidadSolicitada >
                            $cantidadesEntregadas->get(
                                $inventarioId,
                                0
                            );
                    }
                );
            })
            ->values();

        $salidaReciente = session('salida_reciente');

        return view('salidas.create', compact(
            'solicitudesAprobadas',
            'salidaReciente'
        ));
    }

    /**
     * Obtener una solicitud aprobada y sus cantidades pendientes de entrega.
     */
    public function obtenerSolicitud(SolicitudMaterial $solicitud)
    {
        if ($solicitud->estatus !== 'aprobado') {
            abort(404, 'La solicitud no está aprobada.');
        }

        $solicitud->load([
            'user:id,name,email,num_empleado,role',
            'operadorPersonal:id,nombre_completo,employee_id,area,grado',
            'detalles.inventario:id,nombre_producto,economico,categoria,medida,existencia,precio_total',
            'salidas.detalles',
        ]);

        $cantidadesEntregadas = $solicitud->salidas
            ->flatMap(function ($salida) {
                return $salida->detalles;
            })
            ->groupBy('inventario_id')
            ->map(function ($detalles) {
                return (int) $detalles->sum('cantidad');
            });

        $productos = $solicitud->detalles
            ->map(function ($detalle) use ($cantidadesEntregadas) {
                $inventario = $detalle->inventario;

                if (!$inventario) {
                    return null;
                }

                $cantidadEntregada = (int) $cantidadesEntregadas
                    ->get($detalle->inventario_id, 0);

                $cantidadPendiente = max(
                    0,
                    (int) $detalle->cantidad_solicitada - $cantidadEntregada
                );

                if ($cantidadPendiente === 0) {
                    return null;
                }

                return [
                    'inventario_id' => $inventario->id,
                    'codigo' => $inventario->economico,
                    'nombre_producto' => $inventario->nombre_producto,
                    'categoria' => $inventario->categoria,
                    'medida' => $inventario->medida,
                    'existencia' => (int) $inventario->existencia,
                    'cantidad_solicitada' => (int) $detalle->cantidad_solicitada,
                    'cantidad_entregada' => $cantidadEntregada,
                    'cantidad_pendiente' => $cantidadPendiente,
                    'precio_unitario' => (float) $inventario->getPrecioPromedio(),
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'id' => $solicitud->id,
            'destino' => $solicitud->destino,
            'comentario' => $solicitud->comentario,
            'fecha_solicitud' => $solicitud->created_at?->format('d/m/Y'),
            'solicitante' => [
                'nombre' => $solicitud->user?->name ?? 'N/A',
                'numero_empleado' => $solicitud->user?->num_empleado ?? 'N/A',
                'email' => $solicitud->user?->email ?? 'N/A',
                'area' => $solicitud->user?->role ?? 'N/A',
            ],
            'operador' => [
                'nombre' => $solicitud->operadorPersonal?->nombre_completo ?? 'N/A',
                'numero_empleado' => $solicitud->operadorPersonal?->employee_id ?? 'N/A',
                'area' => $solicitud->operadorPersonal?->area ?? 'N/A',
                'puesto' => $solicitud->operadorPersonal?->grado ?? 'N/A',
            ],
            'productos' => $productos,
            'completada' => $productos->isEmpty(),
        ]);
    }

    public function buscarProductos(Request $request)
    {
        $termino = trim((string) $request->query('q', ''));

        if (mb_strlen($termino) < 2) {
            return response()->json([]);
        }

        $productos = Inventario::query()
            ->where('existencia', '>', 0)
            ->where(function ($query) use ($termino) {
                $query->where('nombre_producto', 'LIKE', "%{$termino}%")
                    ->orWhere('economico', 'LIKE', "%{$termino}%")
                    ->orWhere('categoria', 'LIKE', "%{$termino}%")
                    ->orWhere('medida', 'LIKE', "%{$termino}%");
            })
            ->orderByRaw(
                'CASE WHEN nombre_producto = ? OR economico = ? THEN 0 ELSE 1 END',
                [$termino, $termino]
            )
            ->orderBy('nombre_producto')
            ->limit(10)
            ->get([
                'id',
                'nombre_producto',
                'economico',
                'categoria',
                'medida',
                'existencia',
                'precio_total',
            ]);

        $resultados = $productos->map(function ($producto) {
            return [
                'id' => $producto->id,
                'nombre_producto' => $producto->nombre_producto,
                'economico' => $producto->economico,
                'categoria' => $producto->categoria,
                'medida' => $producto->medida,
                'existencia' => (int) $producto->existencia,
                'precio_promedio' => (float) $producto->getPrecioPromedio(),
            ];
        });

        return response()->json($resultados);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'solicitud_material_id' => [
                'nullable',
                'integer',
                'exists:solicitud_materiales,id',
            ],
            'cliente_id' => [
                'nullable',
                'required_without:solicitud_material_id',
                'exists:clientes,id',
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
            $salida = DB::transaction(function () use ($validated) {
                $solicitud = null;
                $cantidadesPendientes = collect();

                if (!empty($validated['solicitud_material_id'])) {
                    $solicitud = SolicitudMaterial::query()
                        ->with([
                            'user',
                            'detalles',
                            'salidas.detalles',
                        ])
                        ->whereKey($validated['solicitud_material_id'])
                        ->lockForUpdate()
                        ->first();

                    if (!$solicitud || $solicitud->estatus !== 'aprobado') {
                        throw ValidationException::withMessages([
                            'solicitud_material_id' =>
                                'La solicitud seleccionada no está aprobada.',
                        ]);
                    }

                    $cantidadesSolicitadas = $solicitud->detalles
                        ->groupBy('inventario_id')
                        ->map(function ($detalles) {
                            return (int) $detalles->sum(
                                'cantidad_solicitada'
                            );
                        });

                    $cantidadesEntregadas = $solicitud->salidas
                        ->flatMap(function ($salidaRegistrada) {
                            return $salidaRegistrada->detalles;
                        })
                        ->groupBy('inventario_id')
                        ->map(function ($detalles) {
                            return (int) $detalles->sum('cantidad');
                        });

                    $cantidadesPendientes = $cantidadesSolicitadas
                        ->map(function ($cantidad, $inventarioId) use (
                            $cantidadesEntregadas
                        ) {
                            return max(
                                0,
                                $cantidad - $cantidadesEntregadas->get(
                                    $inventarioId,
                                    0
                                )
                            );
                        });
                }

                /*
                * Agrupar productos repetidos para evitar que una misma salida
                * contenga dos líneas del mismo artículo.
                */
                $productos = collect($validated['productos'])
                    ->groupBy(function ($producto) {
                        return (int) $producto['inventario_id'];
                    })
                    ->map(function ($productosAgrupados, $inventarioId) {
                        return [
                            'inventario_id' => (int) $inventarioId,
                            'cantidad' => (int) $productosAgrupados->sum(
                                'cantidad'
                            ),
                        ];
                    })
                    ->values();

                if ($solicitud) {
                    foreach ($productos as $producto) {
                        $inventarioId = $producto['inventario_id'];
                        $cantidadPendiente = $cantidadesPendientes->get(
                            $inventarioId
                        );

                        if ($cantidadPendiente === null) {
                            throw ValidationException::withMessages([
                                'productos' =>
                                    'Uno de los productos no pertenece a la solicitud.',
                            ]);
                        }

                        if ($producto['cantidad'] > $cantidadPendiente) {
                            throw ValidationException::withMessages([
                                'productos' =>
                                    "La cantidad del producto {$inventarioId} " .
                                    "supera lo pendiente por entregar " .
                                    "({$cantidadPendiente}).",
                            ]);
                        }
                    }
                }

                $inventarios = Inventario::query()
                    ->whereIn(
                        'id',
                        $productos->pluck('inventario_id')
                    )
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($productos as $producto) {
                    $inventario = $inventarios->get(
                        $producto['inventario_id']
                    );

                    if (!$inventario) {
                        throw ValidationException::withMessages([
                            'productos' =>
                                'Uno de los productos ya no existe.',
                        ]);
                    }

                    if ($inventario->existencia < $producto['cantidad']) {
                        throw ValidationException::withMessages([
                            'productos' =>
                                "Stock insuficiente para " .
                                "{$inventario->nombre_producto}. " .
                                "Disponible: {$inventario->existencia}.",
                        ]);
                    }
                }

                $salida = Salida::create([
                    'solicitud_material_id' =>
                        $solicitud?->id,
                    'cliente_id' =>
                        $solicitud
                            ? null
                            : ($validated['cliente_id'] ?? null),
                    'fecha_salida' =>
                        $validated['fecha_salida'],
                    'observaciones' =>
                        $validated['observaciones'] ?? null,
                    'user_id' =>
                        auth()->id(),
                ]);

                foreach ($productos as $producto) {
                    $inventario = $inventarios->get(
                        $producto['inventario_id']
                    );

                    SalidaDetalle::create([
                        'salida_id' => $salida->id,
                        'inventario_id' => $inventario->id,
                        'cantidad' => $producto['cantidad'],
                        'precio_unitario' =>
                            $inventario->getPrecioPromedio(),
                    ]);
                }

                return $salida->refresh()->load([
                    'cliente',
                    'solicitudMaterial.user',
                    'detalles.inventario',
                ]);
            });

            $nombreSolicitante =
                $salida->solicitudMaterial?->user?->name
                ?? $salida->cliente?->nombre
                ?? 'N/A';

            $destino =
                $salida->solicitudMaterial?->destino
                ?? $salida->cliente?->area
                ?? 'N/A';

            session()->flash('salida_reciente', [
                'id' => $salida->id,
                'numero_factura' => $salida->numero_factura,
                'fecha' => $salida->created_at->format('d/m/Y H:i'),
                'cliente_nombre' => $nombreSolicitante,
                'cliente_area' => $destino,
                'cantidad_productos' => $salida->cantidad_productos,
                'cantidad_total' => $salida->cantidad_total,
                'subtotal' => $salida->precio_total,
                'iva' => $salida->iva,
                'total' => $salida->total_con_iva,
            ]);

            return redirect()
                ->route('salidas.show', $salida)
                ->with('success', 'Salida registrada correctamente');
        } catch (ValidationException $exception) {
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

    public function show(Salida $salida)
    {
        $salida->load(['cliente', 'user', 'detalles.inventario']);

        return view('salidas.show', compact('salida'));
    }

    public function destroy(Salida $salida)
    {
        if ($salida->created_at->diffInHours(now()) > 24) {
            return redirect()->route('salidas.show', $salida)
                ->with('error', 'No se puede eliminar una salida después de 24 horas');
        }

        // Eliminar detalles primero (esto revertirá el inventario automáticamente)
        $salida->detalles()->delete();
        $salida->delete();

        return redirect()->route('salidas.index')
            ->with('success', 'Salida eliminada correctamente');
    }

    public function generatePDF(Salida $salida)
    {
        $salida->load([
            'cliente',
            'user',
            'detalles.inventario',
            'solicitudMaterial.user',
            'solicitudMaterial.operadorPersonal',
        ]);

        $datosFirmas = $this->obtenerDatosFirmas($salida);

        $pdf = Pdf::loadView(
            'salidas.pdf',
            array_merge(
                ['salida' => $salida],
                $datosFirmas
            )
        )->setPaper('letter', 'portrait');

        $folio = $salida->numero_factura
            ?? 'SAL-' . str_pad(
                $salida->id,
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
            'vale-salida-' . $nombreArchivo . '.pdf'
        );
    }

    public function viewPDF(Salida $salida)
    {
        $salida->load([
            'cliente',
            'user',
            'detalles.inventario',
            'solicitudMaterial.user',
            'solicitudMaterial.operadorPersonal',
        ]);

        $datosFirmas = $this->obtenerDatosFirmas($salida);

        $pdf = Pdf::loadView(
            'salidas.pdf',
            array_merge(
                ['salida' => $salida],
                $datosFirmas
            )
        )->setPaper('letter', 'portrait');

        return $pdf->stream('vale-salida.pdf');
    }
    /**
     * Convierte la firma almacenada en una imagen Base64 compatible con DomPDF.
     */
    private function obtenerFirmaDataUri(?User $usuario): ?string
    {
        if (!$usuario || !$usuario->signature) {
            return null;
        }

        $rutaFirma = storage_path(
            'app/public/' . ltrim($usuario->signature, '/\\')
        );

        if (!is_file($rutaFirma) || !is_readable($rutaFirma)) {
            return null;
        }

        $mimeType = mime_content_type($rutaFirma) ?: 'image/png';

        $formatosPermitidos = [
            'image/png',
            'image/jpeg',
            'image/gif',
        ];

        if (!in_array($mimeType, $formatosPermitidos, true)) {
            return null;
        }

        return 'data:' . $mimeType . ';base64,' .
            base64_encode(file_get_contents($rutaFirma));
    }

    /**
     * Obtiene los firmantes correspondientes al vale de salida.
     */
    private function obtenerDatosFirmas(Salida $salida): array
    {
        $firmanteAutorizador = User::where(
            'email',
            'direccion@cman.com'
        )->first();

        $firmanteAlmacen = User::where(
            'email',
            'almacen@cman.com'
        )->first();

        $firmanteSolicitante =
            $salida->solicitudMaterial?->user;

        return [
            'firmanteAutorizador' => $firmanteAutorizador,
            'firmanteAlmacen' => $firmanteAlmacen,
            'firmaAutorizador' => $this->obtenerFirmaDataUri(
                $firmanteAutorizador
            ),
            'firmaAlmacen' => $this->obtenerFirmaDataUri(
                $firmanteAlmacen
            ),
            'firmaSolicitante' => $this->obtenerFirmaDataUri(
                $firmanteSolicitante
            ),
        ];
    }
}