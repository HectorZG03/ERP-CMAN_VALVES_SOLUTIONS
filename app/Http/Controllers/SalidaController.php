<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\SolicitudMaterial;
use App\Services\SalidaPdfService;
use App\Services\SalidaService;
use App\Services\SolicitudSalidaService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SalidaController extends Controller
{
    public function __construct(
        private SalidaService $salidaService,
        private SolicitudSalidaService $solicitudSalidaService,
        private SalidaPdfService $salidaPdfService
    ) {
    }

    /**
     * Mostrar el listado de salidas.
     */
    public function index(Request $request)
    {
        $buscar = trim($request->input('buscar', ''));

        $query = Salida::with([
            /*
             * Cliente se conserva para salidas históricas
             * registradas antes de vincularlas con solicitudes.
             */
            'cliente',
            'user',
            'solicitudMaterial.user',
            'detalles.inventario',
        ]);

        if ($buscar !== '') {
            $fecha = null;

            // Acepta fechas d/m/Y y Y-m-d.
            try {
                $fechaCandidata = \Carbon\Carbon::createFromFormat('d/m/Y', $buscar);

                if ($fechaCandidata && $fechaCandidata->format('d/m/Y') === $buscar) {
                    $fecha = $fechaCandidata->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                // El término no tiene formato d/m/Y.
            }

            if (!$fecha && preg_match('/^\d{4}-\d{2}-\d{2}$/', $buscar)) {
                try {
                    $fechaCandidata = \Carbon\Carbon::createFromFormat('Y-m-d', $buscar);

                    if ($fechaCandidata && $fechaCandidata->format('Y-m-d') === $buscar) {
                        $fecha = $buscar;
                    }
                } catch (\Throwable $e) {
                    // El término no es una fecha válida.
                }
            }

            $query->where(function ($q) use ($buscar, $fecha) {
                $q->where('numero_factura', 'like', "%{$buscar}%")
                    ->orWhereHas('cliente', function ($cliente) use ($buscar) {
                        $cliente->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('area', 'like', "%{$buscar}%");
                    })
                    ->orWhereHas('user', function ($usuario) use ($buscar) {
                        $usuario->where('name', 'like', "%{$buscar}%");
                    })
                    ->orWhereHas('solicitudMaterial', function ($solicitud) use ($buscar) {
                        $solicitud->where('destino', 'like', "%{$buscar}%")
                            ->orWhereHas('user', function ($usuario) use ($buscar) {
                                $usuario->where('name', 'like', "%{$buscar}%");
                            })
                            ->orWhereHas('operadorPersonal', function ($personal) use ($buscar) {
                                $personal->whereRaw(
                                    "CONCAT_WS(' ', nombre, ape_pat, ape_mat) LIKE ?",
                                    ["%{$buscar}%"]
                                )
                                    ->orWhere('nombre', 'like', "%{$buscar}%")
                                    ->orWhere('ape_pat', 'like', "%{$buscar}%")
                                    ->orWhere('ape_mat', 'like', "%{$buscar}%");
                            });
                    })
                    ->orWhereHas('detalles.inventario', function ($inventario) use ($buscar) {
                        $inventario->where('nombre_producto', 'like', "%{$buscar}%")
                            ->orWhere('categoria', 'like', "%{$buscar}%");
                    });

                if ($fecha) {
                    $q->orWhereDate('fecha_salida', $fecha)
                        ->orWhereDate('created_at', $fecha);
                }
            });
        }

        $salidas = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

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

    /**
     * Buscar solicitudes disponibles para generar una salida.
     */
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

        $solicitudes =
            $this->solicitudSalidaService
                ->buscarSolicitudes($termino);

        return response()->json(
            $solicitudes
        );
    }

    /**
     * Obtener los datos y materiales pendientes
     * de una solicitud.
     */
    public function obtenerSolicitud(
        SolicitudMaterial $solicitud
    ) {
        $datos =
            $this->solicitudSalidaService
                ->obtenerSolicitud(
                    $solicitud
                );

        return response()->json(
            $datos
        );
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
            $salida =
                $this->salidaService->registrar(
                    $validated,
                    (int) auth()->id()
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
                    ?->nombre ??
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
        return $this->salidaPdfService
            ->download($salida);
    }

    /**
     * Visualizar el PDF de la salida.
     */
    public function viewPDF(Salida $salida)
    {
        return $this->salidaPdfService
            ->stream($salida);
    }
}
