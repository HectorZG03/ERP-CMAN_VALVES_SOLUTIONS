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
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50'],
        ]);

        $search = trim($validated['search'] ?? '');
        $fecha = $validated['fecha'] ?? null;
        $perPage = (int) ($validated['per_page'] ?? 15);
        $folioBuscado = ltrim($search, '#');

        $query = Valepp::query()
            ->with([
                'personal:id,nombre,ape_pat,ape_mat',
                'personal.datosLaborales:personal_id,employee_id,puesto_id',
                'personal.datosLaborales.puesto:id,nombre,departamento_id',
                'personal.datosLaborales.puesto.departamento:id,nombre,area_id',
                'personal.datosLaborales.puesto.departamento.area:id,nombre',
                'user:id,name,email',
                'solicitudMaterial:id,user_id,destino,estatus,tipo_solicitud,created_at',
            ])
            ->withCount('detalles')
            ->withSum('detalles as unidades_asignadas', 'cantidad')
            ->when($search !== '', function ($query) use ($search, $folioBuscado) {
                $query->where(function ($subquery) use ($search, $folioBuscado) {
                    $subquery
                        ->where('numero_vale', 'like', "%{$search}%")
                        ->orWhereHas('personal', function ($personalQuery) use ($search) {
                            $personalQuery->where(function ($query) use ($search) {
                                $query
                                    ->whereRaw(
                                        "CONCAT_WS(' ', nombre, ape_pat, ape_mat) LIKE ?",
                                        ["%{$search}%"]
                                    )
                                    ->orWhereHas('datosLaborales', function ($laboralQuery) use ($search) {
                                        $laboralQuery
                                            ->where('employee_id', 'like', "%{$search}%")
                                            ->orWhereHas(
                                                'puesto.departamento.area',
                                                function ($areaQuery) use ($search) {
                                                    $areaQuery->where(
                                                        'nombre',
                                                        'like',
                                                        "%{$search}%"
                                                    );
                                                }
                                            );
                                    });
                            });
                        })
                        ->orWhereHas('solicitudMaterial', function ($solicitudQuery) use ($search) {
                            $solicitudQuery->where(function ($query) use ($search) {
                                $query
                                    ->where('destino', 'like', "%{$search}%")
                                    ->orWhereHas('user', function ($userQuery) use ($search) {
                                        $userQuery->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
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
            ->paginate($perPage)
            ->withQueryString();

        $totalVales = Valepp::query()->count();

        return view('valepp.index', compact(
            'valepp',
            'totalVales',
            'search',
            'fecha',
            'perPage'
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

        $personalActivo = Personal::query()
            ->select(['id', 'nombre', 'ape_pat', 'ape_mat'])
            ->with([
                'datosLaborales:personal_id,employee_id,puesto_id',
                'datosLaborales.puesto:id,nombre,departamento_id',
                'datosLaborales.puesto.departamento:id,nombre,area_id',
                'datosLaborales.puesto.departamento.area:id,nombre',
            ])
            ->whereHas('datosLaborales.estadoLaboral', function ($query) {
                $query->where('clave', 'activo');
            })
            ->orderBy('ape_pat')
            ->orderBy('ape_mat')
            ->orderBy('nombre')
            ->get();

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
        $terminosBusqueda = preg_split(
            '/\s+/u',
            str_replace('#', '', $search),
            -1,
            PREG_SPLIT_NO_EMPTY
        ) ?: [];

        $solicitudes = SolicitudMaterial::query()
            ->epp()
            ->whereIn('estatus', [
                'pendiente',
                'aprobado',
            ])
            ->with([
                'user:id,name,email',
                'operadorPersonal:id,nombre,ape_pat,ape_mat',
                'operadorPersonal.datosLaborales:personal_id,employee_id',
                'detalles' => function ($query) {
                    $query
                        ->with([
                            'inventario:id,nombre_producto,economico,categoria,medida,existencia',
                        ])
                        ->withSum('asignacionesValeEpp', 'cantidad');
                },
            ])
            // Filtra la disponibilidad en SQL antes del límite para que las
            // solicitudes ya agotadas no oculten otras que aún tienen saldo.
            ->whereHas('detalles', function ($detalleQuery) {
                $detalleQuery->whereRaw(
                    'solicitud_material_detalles.cantidad_solicitada > (
                        SELECT COALESCE(SUM(valepp_detalles.cantidad), 0)
                        FROM valepp_detalles
                        WHERE valepp_detalles.solicitud_material_detalle_id = solicitud_material_detalles.id
                    )'
                );
            })
            ->when($terminosBusqueda !== [], function ($query) use ($terminosBusqueda) {
                $query->where(function ($coincidencias) use ($terminosBusqueda) {
                    foreach ($terminosBusqueda as $termino) {
                        $like = "%{$termino}%";

                        $coincidencias->where(function ($campoQuery) use ($termino, $like) {
                            $campoQuery
                                ->where('destino', 'like', $like)
                                ->orWhere('comentario', 'like', $like)
                                ->orWhere('operador', 'like', $like)
                                ->orWhere('categoria', 'like', $like)
                                ->orWhereHas('user', function ($userQuery) use ($like) {
                                    $userQuery->where(function ($userFields) use ($like) {
                                        $userFields
                                            ->where('name', 'like', $like)
                                            ->orWhere('email', 'like', $like);
                                    });
                                })
                                ->orWhereHas('operadorPersonal', function ($personalQuery) use ($like) {
                                    $personalQuery->where(function ($personalFields) use ($like) {
                                        $personalFields
                                            ->where('nombre', 'like', $like)
                                            ->orWhere('ape_pat', 'like', $like)
                                            ->orWhere('ape_mat', 'like', $like)
                                            ->orWhereHas('datosLaborales', function ($laboralQuery) use ($like) {
                                                $laboralQuery->where('employee_id', 'like', $like);
                                            });
                                    });
                                })
                                ->orWhereHas('detalles.inventario', function ($inventarioQuery) use ($like) {
                                    $inventarioQuery->where(function ($productoFields) use ($like) {
                                        $productoFields
                                            ->where('nombre_producto', 'like', $like)
                                            ->orWhere('economico', 'like', $like)
                                            ->orWhere('categoria', 'like', $like)
                                            ->orWhere('medida', 'like', $like);
                                    });
                                });

                            if (ctype_digit($termino)) {
                                $campoQuery->orWhere('id', (int) $termino);
                            }
                        });
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
                    'destino' => $solicitud->destino,
                    'estatus' => $solicitud->estatus,
                    'solicitante' => $solicitud->user?->name,
                    'personal' => $solicitud->operadorPersonal?->nombre_mostrar
                        ?? $solicitud->operador,
                    'numero_empleado' => $solicitud->operadorPersonal
                        ?->datosLaborales
                        ?->employee_id,
                    'productos_preview' => $solicitud->detalles
                        ->map(fn (SolicitudMaterialDetalle $detalle) => $detalle->inventario?->nombre_producto)
                        ->filter()
                        ->unique()
                        ->take(3)
                        ->values(),
                    'cantidad_productos' => $solicitud->detalles->count(),
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

    public function obtenerSolicitudEpp(
        Request $request,
        SolicitudMaterial $solicitud
    ) {
        abort_unless(
            $request->user()->canManageValeEPP(),
            403
        );

        $validated = $request->validate([
            'valepp_id' => ['nullable', 'integer', 'exists:valepp,id'],
        ]);

        $valeppId = isset($validated['valepp_id'])
            ? (int) $validated['valepp_id']
            : null;

        if ($valeppId) {
            $valepp = Valepp::findOrFail($valeppId);

            abort_unless(
                (int) $valepp->solicitud_material_id === (int) $solicitud->id,
                404
            );

            abort_unless(
                in_array($valepp->estatus, ['pendiente', 'aprobado'], true),
                403
            );
        }

        $this->validarSolicitudEpp($solicitud);

        $solicitud->load([
            'user:id,name,email',
            'detalles' => function ($query) use ($valeppId) {
                $query
                    ->with([
                        'inventario:id,nombre_producto,economico,categoria,medida,existencia',
                    ])
                    ->withSum([
                        'asignacionesValeEpp as cantidad_asignada_para_vale' =>
                            function ($asignacionesQuery) use ($valeppId) {
                                if ($valeppId) {
                                    $asignacionesQuery->where(
                                        'valepp_id',
                                        '<>',
                                        $valeppId
                                    );
                                }
                            },
                    ], 'cantidad');
            },
        ]);

        $detalles = $solicitud->detalles
            ->map(function (SolicitudMaterialDetalle $detalle) {
                $cantidadAsignada = (int) (
                    $detalle->cantidad_asignada_para_vale ?? 0
                );

                $cantidadDisponible = max(
                    0,
                    (int) $detalle->cantidad_solicitada - $cantidadAsignada
                );

                return [
                    'id' => $detalle->id,
                    'inventario_id' => $detalle->inventario_id,
                    'nombre_producto' => $detalle->inventario?->nombre_producto,
                    'economico' => $detalle->inventario?->economico,
                    'categoria' => $detalle->inventario?->categoria,
                    'medida' => $detalle->inventario?->medida,
                    'cantidad_solicitada' => (int) $detalle->cantidad_solicitada,
                    'cantidad_asignada' => $cantidadAsignada,
                    'cantidad_disponible' => $cantidadDisponible,
                ];
            })
            ->filter(fn (array $detalle) => $detalle['cantidad_disponible'] > 0)
            ->values();

        return response()->json([
            'solicitud' => [
                'id' => $solicitud->id,
                'folio' => sprintf('#%04d', $solicitud->id),
                'fecha' => $solicitud->created_at?->format('d/m/Y H:i'),
                'destino' => $solicitud->destino,
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
            $valepp = DB::transaction(function () use ($validated, $request) {
                $personalElegible = Personal::query()
                    ->whereKey($validated['personal_id'])
                    ->whereHas('datosLaborales.estadoLaboral', function ($query) {
                        $query->where('clave', 'activo');
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$personalElegible) {
                    throw ValidationException::withMessages([
                        'personal_id' => 'Selecciona un colaborador con estado laboral activo.',
                    ]);
                }

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
     * Mostrar el formulario para corregir un vale EPP.
     */
    public function edit(Request $request, Valepp $valepp)
    {
        abort_unless(
            $request->user()->canManageValeEPP(),
            403
        );

        abort_unless(
            in_array($valepp->estatus, ['pendiente', 'aprobado'], true),
            403
        );

        $valepp->load([
            'solicitudMaterial.user',
            'personal.datosLaborales.puesto.departamento.area',
            'detalles.inventario',
            'detalles.solicitudMaterialDetalle',
        ]);

        if (!$valepp->solicitudMaterial) {
            abort(404);
        }

        $detalleSinOrigenValido = $valepp->detalles->contains(
            fn (ValeppDetalle $detalle) =>
                !$detalle->solicitudMaterialDetalle
                || (int) $detalle->solicitudMaterialDetalle->solicitud_material_id
                    !== (int) $valepp->solicitud_material_id
        );

        if ($detalleSinOrigenValido) {
            return redirect()
                ->route('valepp.show', $valepp)
                ->with(
                    'error',
                    'Este vale contiene productos sin vínculo válido con su solicitud y requiere revisión antes de editarse.'
                );
        }

        $this->validarSolicitudEpp($valepp->solicitudMaterial, true);

        return view('valepp.edit', compact('valepp'));
    }

    /**
     * Actualizar los productos asignados en un vale EPP.
     */
    public function update(Request $request, Valepp $valepp)
    {
        abort_unless(
            $request->user()->canManageValeEPP(),
            403
        );

        abort_unless(
            in_array($valepp->estatus, ['pendiente', 'aprobado'], true),
            403
        );

        $validated = $request->validate([
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
            'personal_id.required' => 'Debe seleccionar al trabajador que recibirá el EPP.',
            'fecha_solicitud.required' => 'La fecha del vale es obligatoria.',
            'fecha_solicitud.before_or_equal' => 'La fecha del vale no puede ser posterior a hoy.',
            'detalles.required' => 'El vale debe conservar al menos un producto.',
            'detalles.min' => 'El vale debe conservar al menos un producto.',
            'detalles.*.solicitud_material_detalle_id.distinct' => 'No puede repetir el mismo producto en el vale.',
            'detalles.*.cantidad.min' => 'Las cantidades deben ser mayores a cero.',
        ]);

        try {
            DB::transaction(function () use ($validated, $request, $valepp) {
                // Serializa cambios simultáneos al mismo vale.
                $valeBloqueado = Valepp::query()
                    ->whereKey($valepp->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!in_array($valeBloqueado->estatus, ['pendiente', 'aprobado'], true)) {
                    throw ValidationException::withMessages([
                        'error' => 'Este vale ya no está disponible para edición.',
                    ]);
                }

                // El encabezado conserva su solicitud de origen.
                $solicitud = SolicitudMaterial::query()
                    ->whereKey($valeBloqueado->solicitud_material_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->validarSolicitudEpp($solicitud, true);

                $detallesActuales = $valeBloqueado->detalles()
                    ->with('solicitudMaterialDetalle')
                    ->get();

                $detalleSinOrigenValido = $detallesActuales->contains(
                    fn (ValeppDetalle $detalle) =>
                        !$detalle->solicitudMaterialDetalle
                        || (int) $detalle->solicitudMaterialDetalle->solicitud_material_id
                            !== (int) $solicitud->id
                );

                if ($detalleSinOrigenValido) {
                    throw ValidationException::withMessages([
                        'detalles' => 'Este vale contiene productos sin vínculo válido con su solicitud y requiere revisión antes de editarse.',
                    ]);
                }

                $personalActivo = Personal::query()
                    ->whereKey($validated['personal_id'])
                    ->whereHas('datosLaborales.estadoLaboral', function ($query) {
                        $query->where('clave', 'activo');
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$personalActivo) {
                    throw ValidationException::withMessages([
                        'personal_id' => 'Selecciona un colaborador con estado laboral activo.',
                    ]);
                }

                $detalleIds = collect($validated['detalles'])
                    ->pluck('solicitud_material_detalle_id')
                    ->map(fn ($id) => (int) $id)
                    ->values();

                // Solo acepta renglones pertenecientes a la solicitud enlazada.
                $detallesSolicitud = SolicitudMaterialDetalle::query()
                    ->where('solicitud_material_id', $solicitud->id)
                    ->whereIn('id', $detalleIds)
                    ->with('inventario')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($detallesSolicitud->count() !== $detalleIds->count()) {
                    throw ValidationException::withMessages([
                        'detalles' => 'Todos los productos deben pertenecer a la solicitud EPP enlazada al vale.',
                    ]);
                }

                // Cuenta las cantidades usadas por otros vales y excluye este vale.
                $asignadoEnOtrosVales = ValeppDetalle::query()
                    ->select('solicitud_material_detalle_id')
                    ->selectRaw('SUM(cantidad) AS total')
                    ->whereIn('solicitud_material_detalle_id', $detalleIds)
                    ->where('valepp_id', '<>', $valeBloqueado->id)
                    ->groupBy('solicitud_material_detalle_id')
                    ->pluck('total', 'solicitud_material_detalle_id');

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

                    $cantidadAsignadaEnOtrosVales = (int) (
                        $asignadoEnOtrosVales[$detalleId] ?? 0
                    );

                    $cantidadDisponibleParaEsteVale = max(
                        0,
                        (int) $detalleSolicitud->cantidad_solicitada
                            - $cantidadAsignadaEnOtrosVales
                    );

                    if ($cantidad > $cantidadDisponibleParaEsteVale) {
                        throw ValidationException::withMessages([
                            "detalles.{$indice}.cantidad"
                                => "La cantidad de '{$inventario->nombre_producto}' excede las {$cantidadDisponibleParaEsteVale} unidades disponibles para este vale.",
                        ]);
                    }
                }

                $valeBloqueado->update([
                    'personal_id' => $validated['personal_id'],
                    'fecha_solicitud' => $validated['fecha_solicitud'],
                    'observaciones' => $validated['observaciones'] ?? null,
                ]);

                // Quita renglones eliminados en el formulario y libera su asignación.
                $valeBloqueado->detalles()
                    ->whereNotIn('solicitud_material_detalle_id', $detalleIds)
                    ->delete();

                // Actualiza renglones existentes o agrega productos de la solicitud.
                foreach ($validated['detalles'] as $asignacion) {
                    $detalleId = (int) $asignacion['solicitud_material_detalle_id'];
                    $detalleSolicitud = $detallesSolicitud->get($detalleId);

                    $valeBloqueado->detalles()->updateOrCreate(
                        [
                            'solicitud_material_detalle_id' => $detalleId,
                        ],
                        [
                            'inventario_id' => $detalleSolicitud->inventario_id,
                            'cantidad' => (int) $asignacion['cantidad'],
                            'fecha_entrega' => $validated['fecha_solicitud'],
                        ]
                    );
                }
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors([
                    'error' => 'No fue posible actualizar el vale EPP. Inténtelo nuevamente.',
                ])
                ->withInput();
        }

        return redirect()
            ->route('valepp.show', $valepp)
            ->with('success', 'Vale EPP actualizado correctamente.');
    }

    /**
     * Mostrar un vale EPP.
     */
    public function show(Valepp $valepp)
    {
        $valepp->load([
            'personal',
            'personal.datosLaborales.puesto.departamento.area',
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
            'personal.datosLaborales.puesto.departamento.area',
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
        ])->setPaper('letter', 'portrait');

        $personal = $valepp->personal;

        $nombreOperador = implode(' ', array_filter([
            $personal?->ape_pat,
            $personal?->ape_mat,
            $personal?->nombre,
        ]));

        $nombreOperadorArchivo = \Illuminate\Support\Str::slug(
            $nombreOperador ?: 'sin_operador',
            '_'
        );

        $personal = $valepp->personal;

        $nombreOperador = implode(' ', array_filter([
            $personal?->ape_pat,
            $personal?->ape_mat,
            $personal?->nombre,
        ]));

        $nombreOperadorArchivo = \Illuminate\Support\Str::slug(
            $nombreOperador ?: 'sin_operador',
            '_'
        );

        $nombreArchivo = "vale_epp_{$valepp->numero_vale}_{$nombreOperadorArchivo}.pdf";

        return $pdf->download(mb_strtoupper($nombreArchivo, 'UTF-8'));
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
