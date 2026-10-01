<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\SolicitudMaterial;
use App\Models\SolicitudMaterialDetalle;
use App\Models\User;
use App\Models\Valepp;
use App\Models\ValeppDetalle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class ValeppController extends Controller
{
    private const FIRMANTE_HSE_EMAIL = 'wverra@cman.com.mx';

    /**
     * Mostrar los vales EPP registrados.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'fecha' => ['nullable', 'date'],
        ]);

        $search = trim($validated['search'] ?? '');
        $fecha = $validated['fecha'] ?? null;
        $folioBuscado = ltrim($search, '#');

        $query = Valepp::query()
            ->with([
                'personal:id,nombre_completo,employee_id,area,grado',
                'user:id,name,email',
                'solicitudMaterial:id,user_id,destino_id,estatus,tipo_solicitud,created_at',
                'solicitudMaterial.destino',
            ])
            ->withCount('detalles')
            ->withSum(
                'detalles as unidades_asignadas',
                'cantidad'
            )
            ->when($search !== '', function ($query) use (
                $search,
                $folioBuscado
            ) {
                $query->where(function ($subquery) use (
                    $search,
                    $folioBuscado
                ) {
                    $subquery
                        ->where('numero_vale', 'like', "%{$search}%")
                        ->orWhereHas('personal', function ($personalQuery) use ($search) {
                            $personalQuery->where(function ($query) use ($search) {
                                $query
                                    ->where('nombre_completo', 'like', "%{$search}%")
                                    ->orWhere('employee_id', 'like', "%{$search}%")
                                    ->orWhere('area', 'like', "%{$search}%");
                            });
                        })
                        ->orWhereHas('solicitudMaterial', function ($solicitudQuery) use ($search) {
                            $solicitudQuery->where(function ($query) use ($search) {
                                $query
                                    ->whereHas('destino', function ($destinoQuery) use ($search) {
                                        $destinoQuery->where('nombre', 'like', "%{$search}%");
                                    })
                                    ->orWhereHas('user', function ($userQuery) use ($search) {
                                        $userQuery->where('name', 'like', "%{$search}%");
                                    });
                            });
                        });

                    if (ctype_digit($folioBuscado)) {
                        $subquery
                            ->orWhere('id', (int) $folioBuscado)
                            ->orWhere('solicitud_material_id', (int) $folioBuscado);
                    }
                });
            })
            ->when($fecha, function ($query) use ($fecha) {
                $query->whereDate('fecha_solicitud', $fecha);
            })
            ->orderByDesc('fecha_solicitud')
            ->orderByDesc('id');

        $valepp = $query
            ->paginate(15)
            ->withQueryString();

        $totalVales = Valepp::query()->count();

        return view('valepp.index', compact(
            'valepp',
            'totalVales',
            'search',
            'fecha'
        ));
    }

    /**
     * Mostrar el formulario para crear un vale EPP.
     */
    public function create(Request $request)
    {
        abort_unless(
            $request->user()->canManageValeEPP(),
            403
        );

        $personalActivo = Personal::activo()
            ->orderBy('nombre_completo')
            ->get([
                'id',
                'nombre_completo',
                'employee_id',
                'area',
                'grado',
            ]);

        $numeroVale = Valepp::generarNumeroVale();

        return view('valepp.create', compact(
            'personalActivo',
            'numeroVale'
        ));
    }

    /**
     * Buscar solicitudes EPP que todavía tengan cantidades disponibles.
     */
    public function buscarSolicitudesEpp(Request $request)
    {
        abort_unless(
            $request->user()->canManageValeEPP(),
            403
        );

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'fecha' => ['nullable', 'date'],
        ]);

        $search = trim($validated['q'] ?? '');
        $fecha = $validated['fecha'] ?? null;
        $folioBuscado = ltrim($search, '#');

        $solicitudes = SolicitudMaterial::query()
            ->epp()
            ->whereIn('estatus', [
                'pendiente',
                'aprobado',
            ])
            ->with([
                'user:id,name,email',
                'destino',
                'detalles' => function ($query) {
                    $query
                        ->with([
                            'inventario:id,nombre_producto,economico,categoria,medida,existencia',
                        ])
                        ->withSum(
                            'asignacionesValeEpp',
                            'cantidad'
                        );
                },
            ])
            ->when($search !== '', function ($query) use (
                $search,
                $folioBuscado
            ) {
                $query->where(function ($subquery) use (
                    $search,
                    $folioBuscado
                ) {
                    $subquery
                        ->whereHas('destino', function ($destinoQuery) use ($search) {
                            $destinoQuery->where('nombre', 'like', "%{$search}%");
                        })
                        ->orWhere('comentario', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where(function ($query) use ($search) {
                                $query
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                        });

                    if (ctype_digit($folioBuscado)) {
                        $subquery->orWhere('id', (int) $folioBuscado);
                    }
                });
                
            })
            ->when($fecha, function ($query) use ($fecha) {
                $query->whereDate('created_at', $fecha);
            })
            ->orderByDesc('created_at')
            ->limit(30)
            ->get()
            ->filter(function (SolicitudMaterial $solicitud) {
                return $solicitud->detalles->contains(
                    fn (SolicitudMaterialDetalle $detalle) =>
                        $detalle->cantidad_disponible_epp > 0
                );
            })
            ->values()
            ->map(function (SolicitudMaterial $solicitud) {
                $cantidadSolicitada = (int) $solicitud->detalles
                    ->sum('cantidad_solicitada');

                $cantidadAsignada = (int) $solicitud->detalles
                    ->sum('cantidad_asignada_epp');

                return [
                    'id' => $solicitud->id,
                    'folio' => sprintf('#%04d', $solicitud->id),
                    'fecha' => $solicitud->created_at?->format('d/m/Y H:i'),
                    'destino' => $solicitud->destino?->nombre,
                    'estatus' => $solicitud->estatus,
                    'solicitante' => $solicitud->user?->name,
                    'productos' => $solicitud->detalles->count(),
                    'cantidad_solicitada' => $cantidadSolicitada,
                    'cantidad_asignada' => $cantidadAsignada,
                    'cantidad_disponible' => max(
                        0,
                        $cantidadSolicitada - $cantidadAsignada
                    ),
                ];
            });

        return response()->json([
            'solicitudes' => $solicitudes,
        ]);
    }

    /**
     * Obtener los productos pendientes de una solicitud EPP.
     */
    public function obtenerSolicitudEpp(
        Request $request,
        SolicitudMaterial $solicitud
    ) {
        abort_unless(
            $request->user()->canManageValeEPP(),
            403
        );

        $this->validarSolicitudEpp($solicitud);

        $solicitud->load([
            'user:id,name,email',
            'destino',
            'detalles' => function ($query) {
                $query
                    ->with([
                        'inventario:id,nombre_producto,economico,categoria,medida,existencia',
                    ])
                    ->withSum(
                        'asignacionesValeEpp',
                        'cantidad'
                    );
            },
        ]);

        $detalles = $solicitud->detalles
            ->filter(
                fn (SolicitudMaterialDetalle $detalle) =>
                    $detalle->cantidad_disponible_epp > 0
            )
            ->values()
            ->map(function (SolicitudMaterialDetalle $detalle) {
                return [
                    'id' => $detalle->id,
                    'inventario_id' => $detalle->inventario_id,
                    'nombre_producto' => $detalle->inventario?->nombre_producto,
                    'economico' => $detalle->inventario?->economico,
                    'categoria' => $detalle->inventario?->categoria,
                    'medida' => $detalle->inventario?->medida,
                    'cantidad_solicitada' => $detalle->cantidad_solicitada,
                    'cantidad_asignada' => $detalle->cantidad_asignada_epp,
                    'cantidad_disponible' => $detalle->cantidad_disponible_epp,
                ];
            });

        return response()->json([
            'solicitud' => [
                'id' => $solicitud->id,
                'folio' => sprintf('#%04d', $solicitud->id),
                'fecha' => $solicitud->created_at?->format('d/m/Y H:i'),
                'destino' => $solicitud->destino?->nombre,
                'estatus' => $solicitud->estatus,
                'solicitante' => $solicitud->user?->name,
                'detalles' => $detalles,
            ],
        ]);
    }

    /**
     * Registrar un vale vinculado a una solicitud EPP.
     */
    public function store(Request $request)
    {
        abort_unless(
            $request->user()->canManageValeEPP(),
            403
        );

        $validated = $request->validate([
            'solicitud_material_id' => [
                'required',
                'integer',
                'exists:solicitud_materiales,id',
            ],
            'personal_id' => [
                'required',
                'integer',
                'exists:personal,id',
            ],
            'fecha_solicitud' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'detalles' => [
                'required',
                'array',
                'min:1',
            ],
            'detalles.*.solicitud_material_detalle_id' => [
                'required',
                'integer',
                'distinct',
                'exists:solicitud_material_detalles,id',
            ],
            'detalles.*.cantidad' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'solicitud_material_id.required' => 'Debe seleccionar una solicitud EPP.',
            'personal_id.required' => 'Debe seleccionar al trabajador que recibirá el EPP.',
            'fecha_solicitud.required' => 'La fecha del vale es obligatoria.',
            'fecha_solicitud.before_or_equal' => 'La fecha del vale no puede ser posterior a hoy.',
            'detalles.required' => 'Debe asignar al menos un equipo.',
            'detalles.min' => 'Debe asignar al menos un equipo.',
            'detalles.*.solicitud_material_detalle_id.distinct' => 'No puede repetir el mismo equipo en el vale.',
            'detalles.*.cantidad.min' => 'Las cantidades deben ser mayores a cero.',
        ]);

        try {
            $valepp = DB::transaction(function () use (
                $validated,
                $request
            ) {
                $solicitud = SolicitudMaterial::query()
                    ->whereKey($validated['solicitud_material_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->validarSolicitudEpp($solicitud, true);

                $detalleIds = collect($validated['detalles'])
                    ->pluck('solicitud_material_detalle_id')
                    ->map(fn ($id) => (int) $id)
                    ->values();

                $detallesSolicitud = SolicitudMaterialDetalle::query()
                    ->where('solicitud_material_id', $solicitud->id)
                    ->whereIn('id', $detalleIds)
                    ->with('inventario')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($detallesSolicitud->count() !== $detalleIds->count()) {
                    throw ValidationException::withMessages([
                        'detalles' => 'Uno de los equipos no pertenece a la solicitud EPP seleccionada.',
                    ]);
                }

                foreach ($validated['detalles'] as $indice => $asignacion) {
                    $detalleId = (int) $asignacion['solicitud_material_detalle_id'];
                    $cantidad = (int) $asignacion['cantidad'];
                    $detalleSolicitud = $detallesSolicitud->get($detalleId);
                    $inventario = $detalleSolicitud->inventario;

                    if (
                        !$inventario
                        || strtoupper(trim((string) $inventario->categoria)) !== 'SEGURIDAD'
                    ) {
                        throw ValidationException::withMessages([
                            "detalles.{$indice}.solicitud_material_detalle_id"
                                => 'Todos los productos del vale deben pertenecer a la categoría SEGURIDAD.',
                        ]);
                    }

                    $cantidadAsignada = (int) ValeppDetalle::query()
                        ->where(
                            'solicitud_material_detalle_id',
                            $detalleSolicitud->id
                        )
                        ->sum('cantidad');

                    $cantidadDisponible = max(
                        0,
                        (int) $detalleSolicitud->cantidad_solicitada
                            - $cantidadAsignada
                    );

                    if ($cantidad > $cantidadDisponible) {
                        throw ValidationException::withMessages([
                            "detalles.{$indice}.cantidad"
                                => "La cantidad de '{$inventario->nombre_producto}' excede las {$cantidadDisponible} unidades disponibles para asignar.",
                        ]);
                    }
                }

                $valepp = Valepp::create([
                    'solicitud_material_id' => $solicitud->id,
                    'numero_vale' => Valepp::generarNumeroVale(),
                    'personal_id' => $validated['personal_id'],
                    'fecha_solicitud' => $validated['fecha_solicitud'],
                    'estatus' => 'aprobado',
                    'observaciones' => $validated['observaciones'] ?? null,
                    'user_id' => $request->user()->id,
                ]);

                foreach ($validated['detalles'] as $asignacion) {
                    $detalleId = (int) $asignacion['solicitud_material_detalle_id'];
                    $detalleSolicitud = $detallesSolicitud->get($detalleId);

                    ValeppDetalle::create([
                        'valepp_id' => $valepp->id,
                        'solicitud_material_detalle_id' => $detalleSolicitud->id,
                        'inventario_id' => $detalleSolicitud->inventario_id,
                        'cantidad' => (int) $asignacion['cantidad'],
                        'fecha_entrega' => $validated['fecha_solicitud'],
                        'observaciones' => null,
                    ]);
                }

                return $valepp;
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors([
                    'error' => 'No fue posible registrar el vale EPP. Inténtelo nuevamente.',
                ])
                ->withInput();
        }

        return redirect()
            ->route('valepp.show', $valepp)
            ->with('success', 'Vale EPP registrado correctamente.');
    }

    /**
     * Mostrar un vale EPP.
     */
    public function show(Valepp $valepp)
    {
        $valepp->load([
            'personal',
            'user',
            'solicitudMaterial.user',
            'detalles.inventario',
            'detalles.solicitudMaterialDetalle',
        ]);

        return view('valepp.show', compact('valepp'));
    }

    /**
     * Descargar el PDF del vale EPP.
     */
    public function exportPDF(Valepp $valepp)
    {
        $valepp->load([
            'personal',
            'user',
            'solicitudMaterial.user',
            'detalles.inventario',
            'detalles.solicitudMaterialDetalle',
        ]);

        $firmanteHse = User::query()
            ->where('email', self::FIRMANTE_HSE_EMAIL)
            ->first();

        $firmaHse = $this->obtenerFirmaDataUri(
            $firmanteHse?->signature
        );

        $pdf = Pdf::loadView('valepp.pdf', [
            'valepp' => $valepp,
            'firmanteHse' => $firmanteHse,
            'firmaHse' => $firmaHse,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
        ])->setPaper('A4', 'portrait');

        return $pdf->download(
            "vale_epp_{$valepp->numero_vale}.pdf"
        );
    }

    /**
     * Verificar que la solicitud sea EPP y no esté denegada.
     */
    private function validarSolicitudEpp(
        SolicitudMaterial $solicitud,
        bool $comoValidacion = false
    ): void {
        $esValida = $solicitud->esEpp()
            && in_array(
                $solicitud->estatus,
                ['pendiente', 'aprobado'],
                true
            );

        if ($esValida) {
            return;
        }

        if ($comoValidacion) {
            throw ValidationException::withMessages([
                'solicitud_material_id'
                    => 'La solicitud seleccionada no es de tipo EPP o fue denegada.',
            ]);
        }

        abort(404);
    }

    /**
     * Convertir una firma almacenada a una URI de datos.
     */
    private function obtenerFirmaDataUri(?string $ruta): ?string
    {
        if (!$ruta) {
            return null;
        }

        $archivo = storage_path(
            'app/public/' . ltrim($ruta, '/')
        );

        if (!is_file($archivo)) {
            return null;
        }

        $mime = mime_content_type($archivo)
            ?: 'image/png';

        return 'data:' . $mime . ';base64,'
            . base64_encode(file_get_contents($archivo));
    }
}
