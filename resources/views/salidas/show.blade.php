@extends('layouts.app')

@section('content')
@php
    $solicitud = $salida->solicitudMaterial;
    $solicitante = $solicitud?->user?->name
        ?? $salida->cliente?->nombre
        ?? 'No disponible';
    $destino = $solicitud?->destino
        ?? $salida->cliente?->area
        ?? 'No disponible';
    $operador = $solicitud?->operadorPersonal?->nombre_completo
        ?? $solicitud?->operador
        ?? 'No asignado';
    $folio = $salida->numero_factura
        ?? 'SAL-' . str_pad($salida->id, 6, '0', STR_PAD_LEFT);
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('salidas.index') }}"
                class="text-sm font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                ← Volver a salidas
            </a>
            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                Salida {{ $folio }}
            </h1>
        </div>

        <a href="{{ route('salidas.pdf', $salida) }}"
            class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
            Descargar PDF
        </a>
    </div>

    <div class="rounded-lg bg-white p-5 shadow dark:bg-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
            Datos de la salida
        </h2>

        <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    Solicitud
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $solicitud ? '#' . $solicitud->id : 'Salida anterior' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    Fecha de salida
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ $salida->fecha_salida?->format('d/m/Y') ?? 'Sin fecha' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    Solicitante
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ $solicitante }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    Destino
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ $destino }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    Operador asignado
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ $operador }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    Registrado por
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ $salida->user?->name ?? 'Usuario no disponible' }}
                </dd>
            </div>
        </dl>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                Materiales entregados
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $salida->detalles->sum('cantidad') }} unidades
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Material
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Código
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Cantidad
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Precio unitario
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Importe
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($salida->detalles as $detalle)
                        @php
                            $importe = (float) $detalle->precio_unitario * (int) $detalle->cantidad;
                        @endphp
                        <tr>
                            <td class="px-5 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $detalle->inventario?->nombre_producto ?? 'Material no disponible' }}
                                </div>
                                @if($detalle->inventario?->categoria)
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $detalle->inventario->categoria }}
                                    </div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $detalle->inventario?->economico ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-gray-900 dark:text-white">
                                {{ $detalle->cantidad }}
                                {{ $detalle->inventario?->medida ?? 'unidades' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                ${{ number_format((float) $detalle->precio_unitario, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($importe, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                No hay materiales registrados en esta salida.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-end border-t border-gray-200 px-5 py-4 dark:border-gray-700">
            <dl class="w-full max-w-xs space-y-2 text-sm">
                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                    <dt>Subtotal</dt>
                    <dd>${{ number_format((float) $salida->precio_total, 2) }}</dd>
                </div>
                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                    <dt>IVA (16%)</dt>
                    <dd>${{ number_format((float) $salida->iva, 2) }}</dd>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-bold text-gray-900 dark:border-gray-600 dark:text-white">
                    <dt>Total</dt>
                    <dd>${{ number_format((float) $salida->total_con_iva, 2) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    @if($salida->observaciones)
        <div class="rounded-lg bg-white p-5 shadow dark:bg-gray-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                Observaciones
            </h2>
            <p class="mt-2 whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">
                {{ $salida->observaciones }}
            </p>
        </div>
    @endif
</div>
@endsection
