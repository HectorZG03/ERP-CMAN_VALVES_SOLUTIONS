<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\Salida;
use App\Models\SalidaDetalle;
use App\Models\SolicitudMaterial;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalidaService
{
    public function __construct(
        private SolicitudSalidaService $solicitudSalidaService
    ) {
    }

    /**
     * Registrar una salida vinculada con una solicitud.
     */
    public function registrar(
        array $validated,
        int $usuarioId
    ): Salida {
        return DB::transaction(
            function () use (
                $validated,
                $usuarioId
            ) {
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
                    !$this->solicitudSalidaService
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
                    $this->solicitudSalidaService
                        ->calcularCantidadesPendientes(
                            $solicitud
                        );

                /*
                 * Agrupar materiales repetidos.
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
                 * Validar que cada producto pertenezca
                 * a la solicitud y no exceda lo pendiente.
                 */
                foreach (
                    $productos as $producto
                ) {
                    $inventarioId =
                        $producto['inventario_id'];

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
                 * Bloquear inventario durante la operación.
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
                            $producto['inventario_id']
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

                /*
                 * Crear la salida.
                 */
                $salida = Salida::create([
                    'solicitud_material_id' =>
                        $solicitud->id,

                    'cliente_id' =>
                        null,

                    'fecha_salida' =>
                        $validated['fecha_salida'],

                    'observaciones' =>
                        $validated['observaciones'] ?? null,

                    'user_id' =>
                        $usuarioId,
                ]);

                /*
                 * Crear los detalles.
                 */
                foreach (
                    $productos as $producto
                ) {
                    $inventario =
                        $inventarios->get(
                            $producto['inventario_id']
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
    }
}