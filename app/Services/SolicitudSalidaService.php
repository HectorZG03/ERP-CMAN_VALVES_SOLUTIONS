<?php

namespace App\Services;

use App\Models\SolicitudMaterial;

class SolicitudSalidaService
{
    /**
     * Estatus que permiten generar salidas de almacén.
     */
    private const ESTATUS_SOLICITUD_PERMITIDOS = [
        'aprobado',
        'pendiente',
    ];

    /**
     * Buscar solicitudes disponibles para generar una salida.
     */
    public function buscarSolicitudes(
        string $termino
    ) {
        $termino = trim($termino);

        $solicitudId =
            $this->obtenerIdBusqueda(
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
            return collect();
        }

        return SolicitudMaterial::query()
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
            ->map(
                function ($solicitud) {
                    $pendientes =
                        $this->calcularCantidadesPendientes(
                            $solicitud
                        )
                        ->filter(
                            function ($cantidad) {
                                return $cantidad > 0;
                            }
                        );

                    if ($pendientes->isEmpty()) {
                        return null;
                    }

                    return [
                        'id' =>
                            $solicitud->id,

                        'folio' =>
                            str_pad(
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
                }
            )
            ->filter()
            ->take(10)
            ->values();
    }

    /**
     * Obtener los datos y materiales pendientes
     * de una solicitud.
     */
    public function obtenerSolicitud(
        SolicitudMaterial $solicitud
    ): array {
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
                            $inventario->nombre_producto,

                        'categoria' =>
                            $inventario->categoria,

                        'medida' =>
                            $inventario->medida,

                        'existencia' =>
                            (int) $inventario->existencia,

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

        return [
            'id' =>
                $solicitud->id,

            'estatus' =>
                $solicitud->estatus,

            'destino' =>
                $solicitud->destino ??
                'Sin destino',

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
        ];
    }

    /**
     * Determinar si una solicitud puede generar
     * una salida.
     */
    public function solicitudPermiteSalida(
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
    public function calcularCantidadesPendientes(
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
     * Convertir una fecha del buscador
     * al formato de MySQL.
     */
    private function normalizarFechaBusqueda(
        string $termino
    ): ?string {
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
}