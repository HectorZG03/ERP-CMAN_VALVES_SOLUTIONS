@extends('layouts.app')

@section('content')
<div class="mx-auto w-full max-w-7xl space-y-5">

    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">
                Inventario / Compras
            </p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                Entradas de materiales
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Consulta las recepciones registradas, sus proveedores y sus importes.
            </p>
        </div>

        @if(auth()->user()->canManageInventory())
            <a href="{{ route('entradas.create') }}"
               class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva entrada
            </a>
        @endif
    </header>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">Buscar entradas</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Filtra por proveedor, usuario o fecha.
                </p>
            </div>

            <form method="GET"
                  action="{{ route('entradas.index') }}"
                  data-entradas-filter-form
                  class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-[minmax(0,1fr)_210px_auto_auto]">

                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4"/>
                        </svg>
                    </div>

                    <input
                        type="search"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Folio, proveedor, usuario o fecha..."
                        autocomplete="off"
                        aria-label="Buscar entradas"
                        class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400"
                    >
                </div>

                <select
                    name="periodo"
                    aria-label="Filtrar entradas por periodo"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                >
                    <option value="todos" @selected(request('periodo', 'todos') === 'todos')>
                        Todos los periodos
                    </option>
                    <option value="hoy" @selected(request('periodo') === 'hoy')>
                        Hoy
                    </option>
                    <option value="semana" @selected(request('periodo') === 'semana')>
                        Últimos 7 días
                    </option>
                    <option value="mes" @selected(request('periodo') === 'mes')>
                        Este mes
                    </option>
                </select>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Buscar
                </button>

                <button type="button"
                        data-entradas-clear
                        class="@if(!request()->filled('buscar') && request('periodo', 'todos') === 'todos') hidden @endif inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    Limpiar
                </button>
            </form>
        </div>

        <div data-live-results>
        <div class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">Historial de entradas</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Resultados encontrados:
                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                        {{ $entradas->total() }}
                    </span>
                </p>
            </div>

            <span class="inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                {{ $entradas->total() === 1 ? '1 entrada' : $entradas->total() . ' entradas' }}
            </span>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="min-w-[850px] w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/70">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Entrada
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Fecha
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Proveedor
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Productos
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Total
                        </th>
                        <th scope="col" class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($entradas as $entrada)
                        @php
                            $cantidadProductos = $entrada->detalles->count();
                            $cantidadUnidades = $entrada->detalles->sum('cantidad');
                            $descuentoGlobal = (float) ($entrada->descuento_global ?? 0);
                        @endphp

                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $entrada->numero_factura ?? 'ENT-' . str_pad($entrada->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ $entrada->fecha_entrada ? $entrada->fecha_entrada->format('d/m/Y') : 'N/A' }}
                                </span>
                                @if($entrada->created_at)
                                    <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">
                                        Registro: {{ $entrada->created_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                @if($entrada->proveedor)
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                            {{ mb_substr($entrada->proveedor->proveedor ?? '?', 0, 1) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ $entrada->proveedor->proveedor }}
                                            </p>
                                            @if($entrada->proveedor->contacto)
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $entrada->proveedor->contacto }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-red-600 dark:text-red-400">
                                        Proveedor no disponible
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $cantidadProductos }}
                                </span>
                                <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">
                                    {{ number_format($cantidadUnidades) }} unidad(es)
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <span class="font-bold tabular-nums text-green-700 dark:text-green-400">
                                    ${{ number_format($entrada->total_con_iva, 2) }}
                                </span>
                                @if($descuentoGlobal > 0)
                                    <span class="mt-1 block text-xs text-amber-700 dark:text-amber-400">
                                        Descuento: ${{ number_format($descuentoGlobal, 2) }}
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('entradas.show', $entrada) }}"
                                       class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                                       aria-label="Ver detalles de la entrada">
                                        Ver
                                    </a>

                                    <a href="{{ route('entradas.pdf', $entrada) }}"
                                       class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       aria-label="Descargar PDF de la entrada">
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                    <svg class="h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7l1 13h12l1-13M9 7V4h6v3"/>
                                    </svg>
                                </div>

                                <h3 class="mt-3 text-sm font-semibold text-gray-900 dark:text-white">
                                    No se encontraron entradas
                                </h3>

                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Ajusta la búsqueda o el periodo seleccionado.
                                </p>

                                @if(auth()->user()->canManageInventory())
                                    <a href="{{ route('entradas.create') }}"
                                       class="mt-5 inline-flex items-center rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800">
                                        Nueva entrada
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-gray-200 px-4 py-3 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Mostrando {{ $entradas->firstItem() ?? 0 }}–{{ $entradas->lastItem() ?? 0 }}
                de {{ $entradas->total() }} entradas
            </p>

            <div data-live-pagination>
                {{ $entradas->links() }}
            </div>
        </div>
        </div>
    </section>
</div>

<script src="{{ asset('js/inventario/entradas/index.js') }}" defer></script>
@endsection
