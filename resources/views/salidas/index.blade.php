@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-5">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">Inventario / Almacén</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                Salidas de material
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Historial de materiales entregados por almacén.
            </p>
        </div>

        @if(auth()->user()->canManageInventory())
            <a href="{{ route('salidas.create') }}"
                class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                Nueva salida
            </a>
        @endif
    </div>

    <form method="GET"
          action="{{ route('salidas.index') }}"
          data-salidas-filter-form
          class="flex flex-col gap-2 rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800 sm:flex-row">
        <input
            type="search"
            name="buscar"
            value="{{ request('buscar') }}"
            placeholder="Folio, solicitante, destino, material o fecha..."
            autocomplete="off"
            aria-label="Buscar salidas"
            class="min-w-0 flex-1 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
        >
        <button type="submit"
                class="rounded-md bg-blue-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-800">
            Buscar
        </button>
        <button type="button"
                data-salidas-clear
                class="@if(!request()->filled('buscar')) hidden @endif rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
            Limpiar
        </button>
    </form>

    <div data-live-results>
        <div class="flex items-center justify-between px-1 text-sm text-gray-500 dark:text-gray-400">
            <span>Historial de salidas</span>
            <span>{{ $salidas->total() }} {{ $salidas->total() === 1 ? 'resultado' : 'resultados' }}</span>
        </div>

    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Folio
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Fecha
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Solicitante
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Destino
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Materiales
                        </th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Total
                        </th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($salidas as $salida)
                        @php
                            $solicitud = $salida->solicitudMaterial;
                            $solicitante = $solicitud?->user?->name
                                ?? $salida->cliente?->nombre
                                ?? 'No disponible';
                            $destino = $solicitud?->destino
                                ?? $salida->cliente?->area
                                ?? 'No disponible';
                            $folio = $salida->numero_factura
                                ?? 'SAL-' . str_pad($salida->id, 6, '0', STR_PAD_LEFT);
                        @endphp

                        <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $folio }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $solicitud ? 'Solicitud #' . $solicitud->id : 'Registro anterior' }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $salida->fecha_salida?->format('d/m/Y') ?? 'Sin fecha' }}
                            </td>

                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $solicitante }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $destino }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $salida->detalles->sum('cantidad') }} unidades
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $salida->detalles->count() }} producto(s)
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                ${{ number_format((float) $salida->total_con_iva, 2) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('salidas.show', $salida) }}"
                                        class="rounded-md bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                        Ver
                                    </a>
                                    <a href="{{ route('salidas.pdf', $salida) }}"
                                        class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50">
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    No hay salidas registradas.
                                </p>
                                @if(auth()->user()->canManageInventory())
                                    <a href="{{ route('salidas.create') }}"
                                        class="mt-4 inline-flex rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                                        Registrar primera salida
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($salidas->hasPages())
            <div data-live-pagination class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                {{ $salidas->withQueryString()->links() }}
            </div>
        @endif
    </div>
    </div>
</div>
<script src="{{ asset('js/inventario/salidas/index.js') }}" defer></script>
@endsection
