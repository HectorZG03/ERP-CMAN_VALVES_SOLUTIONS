@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">

    <header class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                Inventario / Almacén
            </p>

            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                Salidas de material
            </h1>

            <p class="mt-2 max-w-2xl text-sm text-gray-600 dark:text-gray-300">
                Consulta los materiales que Almacén ha entregado y revisa la solicitud,
                el área solicitante y el comprobante PDF.
            </p>
        </div>

        @if(auth()->user()->canManageInventory())
            <a
                href="{{ route('salidas.create') }}"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 4a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 10 4Z" />
                </svg>
                Nueva salida
            </a>
        @endif
    </header>

    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form
            method="GET"
            action="{{ route('salidas.index') }}"
            data-salidas-filter-form
            class="flex flex-col gap-3 sm:flex-row"
        >
            <label for="buscar-salidas" class="sr-only">
                Buscar salidas
            </label>

            <div class="relative min-w-0 flex-1">
                <svg
                    class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 1 0 3.478 9.76l3.631 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.631A5.5 5.5 0 0 0 8.5 3ZM4.5 8.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                </svg>

                <input
                    id="buscar-salidas"
                    type="search"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Buscar por folio, solicitante, destino, material o fecha..."
                    autocomplete="off"
                    data-salidas-search
                    class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400"
                >
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    class="inline-flex flex-1 items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 sm:flex-none"
                >
                    Buscar
                </button>

                <button
                    type="button"
                    data-salidas-clear
                    class="@if(!request()->filled('buscar')) hidden @endif rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Limpiar
                </button>
            </div>
        </form>
    </section>

    <div data-live-results class="space-y-3">
        <div class="flex flex-col gap-1 px-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    Historial de entregas
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Folio, solicitante, destino y materiales entregados.
                </p>
            </div>

            <span class="inline-flex w-fit items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                {{ $salidas->total() }}
                {{ $salidas->total() === 1 ? 'resultado' : 'resultados' }}
            </span>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-[1000px] divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/70">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Folio / solicitud
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Fecha
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Solicitante y área
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Destino
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Materiales
                            </th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Total
                            </th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($salidas as $salida)
                            @php
                                $solicitud = $salida->solicitudMaterial;

                                $solicitante = $solicitud?->user?->name
                                    ?? $salida->cliente?->nombre
                                    ?? 'No disponible';

                                $rolSolicitante = $solicitud?->user?->role;

                                $areaSolicitante = match ($rolSolicitante) {
                                    'operaciones' => 'Operaciones',
                                    'aux_operaciones' => 'Auxiliar de Operaciones',
                                    'logistica' => 'Logística',
                                    'aux_logistica' => 'Auxiliar de Logística',
                                    default => $rolSolicitante
                                        ? ucfirst(str_replace('_', ' ', $rolSolicitante))
                                        : 'No determinada',
                                };

                                $destino = $solicitud?->destino
                                    ?? $salida->cliente?->area
                                    ?? 'No disponible';

                                $folio = $salida->numero_factura
                                    ?? 'SAL-' . str_pad($salida->id, 6, '0', STR_PAD_LEFT);

                                $cantidadUnidades = $salida->detalles->sum('cantidad');
                                $cantidadProductos = $salida->detalles->count();
                            @endphp

                            <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $folio }}
                                    </div>

                                    @if($solicitud)
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Solicitud #{{ $solicitud->id }}
                                        </div>
                                    @else
                                        <span class="mt-1 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                            Registro anterior
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $salida->fecha_salida?->format('d/m/Y') ?? 'Sin fecha' }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $solicitante }}
                                    </div>

                                    <span class="mt-1 inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ $areaSolicitante }}
                                    </span>
                                </td>

                                <td class="max-w-xs px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    <span class="line-clamp-2">
                                        {{ $destino }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $cantidadUnidades }}
                                        {{ $cantidadUnidades === 1 ? 'unidad' : 'unidades' }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $cantidadProductos }}
                                        {{ $cantidadProductos === 1 ? 'producto' : 'productos' }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        ${{ number_format((float) $salida->total_con_iva, 2) }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('salidas.show', $salida) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                                            aria-label="Ver detalle de la salida {{ $folio }}"
                                        >
                                            Ver detalle
                                        </a>

                                        <a href="{{ route('salidas.pdf', $salida) }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">Descargar PDF</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex max-w-md flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M3 4.75A1.75 1.75 0 0 1 4.75 3h10.5A1.75 1.75 0 0 1 17 4.75v10.5A1.75 1.75 0 0 1 15.25 17H4.75A1.75 1.75 0 0 1 3 15.25V4.75Zm1.75-.25a.25.25 0 0 0-.25.25v1.5h11v-1.5a.25.25 0 0 0-.25-.25H4.75Zm10.75 3.25h-11v7.5c0 .138.112.25.25.25h10.5a.25.25 0 0 0 .25-.25v-7.5Z" />
                                            </svg>
                                        </div>

                                        <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
                                            @if(request()->filled('buscar'))
                                                No se encontraron salidas
                                            @else
                                                Aún no hay salidas registradas
                                            @endif
                                        </h3>

                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            @if(request()->filled('buscar'))
                                                Prueba con otro término de búsqueda o limpia el filtro.
                                            @else
                                                Las salidas registradas por Almacén aparecerán aquí.
                                            @endif
                                        </p>

                                        @if(auth()->user()->canManageInventory() && !request()->filled('buscar'))
                                            <a
                                                href="{{ route('salidas.create') }}"
                                                class="mt-4 inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700"
                                            >
                                                Registrar primera salida
                                            </a>
                                        @endif
                                    </div>
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
