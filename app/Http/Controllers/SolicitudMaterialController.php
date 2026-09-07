<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Personal;
use App\Models\SolicitudMaterial;
use App\Models\SolicitudMaterialDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class SolicitudMaterialController extends Controller
{
    /**
     * Obtener la imagen correspondiente al estatus de la solicitud.
     */
    private function obtenerImagenEstatusSolicitud(string $estatus): string
    {
        $base = storage_path('app/public/admin/');

        return match (strtolower($estatus)) {
            'aprobado' => $base . '01.png',
            'denegado' => $base . '03.png',
            default => $base . '04.png',
        };
    }

    /**
     * Mostrar el listado de solicitudes.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $filterStatus = $request->get('status', 'all');

        $estatusValidos = [
            'all',
            'pendiente',
            'aprobado',
            'denegado',
        ];

        if (!in_array($filterStatus, $estatusValidos, true)) {
            $filterStatus = 'all';
        }

        $puedeVerTodas = $user->canApproveRequests()
            || $user->canManageInventory();

        $baseQuery = SolicitudMaterial::query()
            ->with([
                'detalles.inventario',
                'user',
                'operadorPersonal',
            ]);

        $countQuery = SolicitudMaterial::query();

        if (!$puedeVerTodas) {
            $baseQuery->where('user_id', $user->id);
            $countQuery->where('user_id', $user->id);
        }

        $counts = [
            'all' => (clone $countQuery)->count(),
            'pendiente' => (clone $countQuery)
                ->where('estatus', 'pendiente')
                ->count(),
            'aprobado' => (clone $countQuery)
                ->where('estatus', 'aprobado')
                ->count(),
            'denegado' => (clone $countQuery)
                ->where('estatus', 'denegado')
                ->count(),
        ];

        if ($filterStatus !== 'all') {
            $solicitudes = $baseQuery
                ->where('estatus', $filterStatus)
                ->orderByDesc('created_at')
                ->get();

            $isPaginated = false;
        } else {
            $solicitudes = $baseQuery
                ->orderByDesc('created_at')
                ->paginate(15)
                ->withQueryString();

            $isPaginated = true;
        }

        return view('solicitudes.index', compact(
            'solicitudes',
            'counts',
            'filterStatus',
            'isPaginated'
        ));
    }

    /**
     * Mostrar el formulario para crear una solicitud.
     */
    public function create(Request $request)
    {
        $personal = Personal::activo()
            ->orderBy('nombre_completo')
            ->get([
                'id',
                'nombre_completo',
                'employee_id',
                'area',
            ]);

        $puedeCrearEpp = $request->user()->canManageValeEPP();

        return view('solicitudes.create', compact(
            'personal',
            'puedeCrearEpp'
        ));
    }

    /**
     * Registrar una solicitud.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'personal_id' => [
                'nullable',
                'exists:personal,id',
            ],
            'destino' => [
                'required',
                'string',
                'max:255',
            ],
            'comentario' => [
                'nullable',
                'string',
            ],
            'operador' => [
                'nullable',
                'string',
                'max:255',
            ],
            'categoria' => [
                'nullable',
                'string',
                'max:255',
            ],
            'tipo_solicitud' => [
                'nullable',
                Rule::in(SolicitudMaterial::TIPOS_VALIDOS),
            ],
            'productos' => [
                'required',
                'array',
                'min:1',
            ],
            'productos.*.inventario_id' => [
                'required',
                'integer',
                'distinct',
                'exists:inventarios,id',
            ],
            'productos.*.cantidad_solicitada' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'destino.required' => 'Debe seleccionar un destino.',
            'tipo_solicitud.in' => 'El tipo de solicitud no es válido.',
            'productos.required' => 'Debe agregar al menos un producto a la solicitud.',
            'productos.min' => 'Debe agregar al menos un producto a la solicitud.',
            'productos.*.inventario_id.required' => 'Debe seleccionar un producto válido.',
            'productos.*.inventario_id.distinct' => 'No puede agregar el mismo producto más de una vez.',
            'productos.*.inventario_id.exists' => 'Uno de los productos seleccionados no existe.',
            'productos.*.cantidad_solicitada.required' => 'La cantidad es obligatoria.',
            'productos.*.cantidad_solicitada.integer' => 'La cantidad debe ser un número entero.',
            'productos.*.cantidad_solicitada.min' => 'La cantidad debe ser mayor a cero.',
        ]);

        $tipoSolicitud = $validated['tipo_solicitud']
            ?? SolicitudMaterial::TIPO_ESTANDAR;

        if (
            $tipoSolicitud === SolicitudMaterial::TIPO_EPP
            && !$user->canManageValeEPP()
        ) {
            throw ValidationException::withMessages([
                'tipo_solicitud' => 'Solo el personal de HSE puede crear solicitudes de EPP.',
            ]);
        }

        try {
            DB::transaction(function () use (
                $validated,
                $tipoSolicitud,
                $user
            ) {
                $inventarioIds = collect($validated['productos'])
                    ->pluck('inventario_id')
                    ->map(fn ($id) => (int) $id)
                    ->values();

                $inventarios = Inventario::query()
                    ->whereIn('id', $inventarioIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($validated['productos'] as $indice => $producto) {
                    $inventarioId = (int) $producto['inventario_id'];
                    $cantidad = (int) $producto['cantidad_solicitada'];
                    $inventario = $inventarios->get($inventarioId);

                    if (!$inventario) {
                        throw ValidationException::withMessages([
                            "productos.{$indice}.inventario_id"
                                => 'El producto seleccionado ya no está disponible.',
                        ]);
                    }

                    if (
                        $tipoSolicitud === SolicitudMaterial::TIPO_EPP
                        && strtoupper(trim((string) $inventario->categoria))
                            !== 'SEGURIDAD'
                    ) {
                        throw ValidationException::withMessages([
                            "productos.{$indice}.inventario_id"
                                => "El producto '{$inventario->nombre_producto}' no pertenece a la categoría SEGURIDAD.",
                        ]);
                    }

                    if ($inventario->existencia < $cantidad) {
                        throw ValidationException::withMessages([
                            "productos.{$indice}.cantidad_solicitada"
                                => "No hay suficiente existencia de '{$inventario->nombre_producto}'. Disponible: {$inventario->existencia}.",
                        ]);
                    }
                }

                $solicitud = SolicitudMaterial::create([
                    'user_id' => $user->id,
                    'personal_id' => $validated['personal_id'] ?? null,
                    'destino' => $validated['destino'],
                    'comentario' => $validated['comentario'] ?? null,
                    'operador' => $validated['operador'] ?? 'N/A',
                    'categoria' => $validated['categoria'] ?? 'N/A',
                    'tipo_solicitud' => $tipoSolicitud,
                    'estatus' => 'pendiente',
                ]);

                foreach ($validated['productos'] as $producto) {
                    $inventarioId = (int) $producto['inventario_id'];
                    $inventario = $inventarios->get($inventarioId);

                    SolicitudMaterialDetalle::create([
                        'solicitud_material_id' => $solicitud->id,
                        'inventario_id' => $inventarioId,
                        'cantidad_solicitada'
                            => (int) $producto['cantidad_solicitada'],
                        'precio_unitario'
                            => $inventario->getPrecioPromedio(),
                    ]);
                }
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors([
                    'error' => 'No fue posible registrar la solicitud. Inténtelo nuevamente.',
                ])
                ->withInput();
        }

        $mensaje = $tipoSolicitud === SolicitudMaterial::TIPO_EPP
            ? 'Solicitud de EPP enviada correctamente.'
            : 'Solicitud de materiales enviada correctamente.';

        return redirect()
            ->route('solicitudes.index')
            ->with('success', $mensaje);
    }

    /**
     * Mostrar una solicitud.
     */
    public function show(
        Request $request,
        SolicitudMaterial $solicitud
    ) {
        $user = $request->user();

        $puedeVer = (int) $solicitud->user_id === (int) $user->id
            || $user->canApproveRequests()
            || $user->canManageInventory();

        if (!$puedeVer) {
            abort(
                403,
                'No tienes permisos para ver esta solicitud.'
            );
        }

        $solicitud->load([
            'detalles.inventario',
            'user',
            'operadorPersonal',
        ]);

        return view('solicitudes.show', compact('solicitud'));
    }

    /**
     * Aprobar o denegar una solicitud.
     *
     * Este proceso no descuenta inventario.
     */
    public function updateEstatus(
        Request $request,
        SolicitudMaterial $solicitud
    ) {
        if (!$request->user()->canApproveRequests()) {
            abort(403);
        }

        $validated = $request->validate([
            'estatus' => [
                'required',
                Rule::in([
                    'aprobado',
                    'denegado',
                ]),
            ],
        ]);

        $solicitud->update([
            'estatus' => $validated['estatus'],
        ]);

        return back()->with(
            'success',
            'Estatus actualizado correctamente.'
        );
    }

    /**
     * Buscar productos disponibles mediante AJAX.
     */
    public function buscarProductos(Request $request)
    {
        $validated = $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:150',
            ],
            'tipo_solicitud' => [
                'nullable',
                Rule::in(SolicitudMaterial::TIPOS_VALIDOS),
            ],
        ]);

        $search = trim($validated['q'] ?? '');
        $tipoSolicitud = $validated['tipo_solicitud']
            ?? SolicitudMaterial::TIPO_ESTANDAR;

        if (
            $tipoSolicitud === SolicitudMaterial::TIPO_EPP
            && !$request->user()->canManageValeEPP()
        ) {
            $tipoSolicitud = SolicitudMaterial::TIPO_ESTANDAR;
        }

        $productos = Inventario::query()
            ->where('existencia', '>', 0)
            ->when(
                $tipoSolicitud === SolicitudMaterial::TIPO_EPP,
                function ($query) {
                    $query->whereRaw(
                        'UPPER(TRIM(categoria)) = ?',
                        ['SEGURIDAD']
                    );
                }
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where(
                            'nombre_producto',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'economico',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'categoria',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'medida',
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->select([
                'id',
                'nombre_producto',
                'economico',
                'categoria',
                'medida',
                'ubicacion',
                'existencia',
            ])
            ->orderBy('nombre_producto')
            ->limit(20)
            ->get();

        return response()->json($productos);
    }

    /**
     * Obtener un producto específico.
     */
    public function obtenerProducto(Request $request, int $id)
    {
        $validated = $request->validate([
            'tipo_solicitud' => [
                'nullable',
                Rule::in(SolicitudMaterial::TIPOS_VALIDOS),
            ],
        ]);

        $tipoSolicitud = $validated['tipo_solicitud']
            ?? SolicitudMaterial::TIPO_ESTANDAR;

        if (
            $tipoSolicitud === SolicitudMaterial::TIPO_EPP
            && !$request->user()->canManageValeEPP()
        ) {
            $tipoSolicitud = SolicitudMaterial::TIPO_ESTANDAR;
        }

        $producto = Inventario::query()
            ->whereKey($id)
            ->where('existencia', '>', 0)
            ->when(
                $tipoSolicitud === SolicitudMaterial::TIPO_EPP,
                function ($query) {
                    $query->whereRaw(
                        'UPPER(TRIM(categoria)) = ?',
                        ['SEGURIDAD']
                    );
                }
            )
            ->first();

        if (!$producto) {
            return response()->json([
                'error' => 'Producto no encontrado o sin existencia.',
            ], 404);
        }

        return response()->json([
            'id' => $producto->id,
            'nombre_producto' => $producto->nombre_producto,
            'economico' => $producto->economico,
            'categoria' => $producto->categoria,
            'medida' => $producto->medida,
            'ubicacion' => $producto->ubicacion,
            'existencia' => $producto->existencia,
            'precio_promedio' => $producto->getPrecioPromedio(),
        ]);
    }

    /**
     * Mostrar el PDF de la solicitud.
     */
    public function pdf(SolicitudMaterial $solicitud)
    {
        $solicitud->load([
            'detalles.inventario',
            'user',
            'operadorPersonal',
        ]);

        $firmaAdminPath = $this->obtenerImagenEstatusSolicitud(
            (string) $solicitud->estatus
        );

        $firmaAdminBase64 = file_exists($firmaAdminPath)
            ? base64_encode(file_get_contents($firmaAdminPath))
            : null;

        $firmaUserBase64 = null;

        if ($solicitud->user?->signature) {
            $signaturePath = storage_path(
                'app/public/' . $solicitud->user->signature
            );

            if (file_exists($signaturePath)) {
                $firmaUserBase64 = base64_encode(
                    file_get_contents($signaturePath)
                );
            }
        }

            $vistaPdf = $solicitud->esEpp()
                ? 'solicitudes.pdf-epp'
                : 'solicitudes.pdf';

            return view(
                $vistaPdf,
                compact(
                    'solicitud',
                    'firmaAdminBase64',
                    'firmaUserBase64'
                )
            );
    }
}