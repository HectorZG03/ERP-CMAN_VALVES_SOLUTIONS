@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- ============================================================
         ENCABEZADO
    ============================================================ --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div>
            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Inventario
                    </h1>

                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        Control y consulta de productos disponibles en almacén.
                    </p>
                </div>

            </div>
        </div>


        {{-- Acciones principales --}}
        <div class="flex flex-wrap items-center gap-2">

            {{-- Historial --}}
            <a href="{{ route('inventario.ajustes.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:border-purple-300 hover:bg-purple-50 hover:text-purple-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-purple-600 dark:hover:bg-purple-900/20 dark:hover:text-purple-300">

                <svg class="h-4 w-4"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>

                Historial
            </a>


            {{-- PDF --}}
            <a href="{{ route('inventario.export.pdf') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-300">

                <svg class="h-4 w-4"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.707.707V19a2 2 0 01-2 2z"/>
                </svg>

                PDF
            </a>


            {{-- Agregar producto --}}
            @if(auth()->user()->canManageInventory())

                <a href="{{ route('inventario.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 hover:shadow-md dark:bg-blue-600 dark:hover:bg-blue-500">

                    <svg class="h-4 w-4"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>

                    Agregar producto
                </a>

            @endif

        </div>

    </div>


    {{-- ============================================================
         INDICADORES
    ============================================================ --}}
    @if(auth()->user()->canManageInventory() || auth()->user()->canManageInventoryadmin())

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Total productos --}}
            <a href="{{ route('inventario.index', ['filter' => 'all']) }}"
               id="filter-all"
               class="group rounded-xl border bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-800
               {{ $filter == 'all'
                    ? 'border-blue-500 ring-1 ring-blue-500/20 dark:border-blue-400'
                    : 'border-gray-200 dark:border-gray-700' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total productos</p>
                        <p class="mt-1 text-xl font-bold leading-tight text-gray-900 dark:text-white">{{ $totalInventario }}</p>
                        <p class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">Catálogo completo</p>
                    </div>
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- En stock --}}
            <a href="{{ route('inventario.index', ['filter' => 'with-stock']) }}"
               id="filter-stock"
               class="group rounded-xl border bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-800
               {{ $filter == 'with-stock'
                    ? 'border-green-500 ring-1 ring-green-500/20 dark:border-green-400'
                    : 'border-gray-200 dark:border-gray-700' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">En stock</p>
                        <p class="mt-1 text-xl font-bold leading-tight text-gray-900 dark:text-white">{{ $totalEnStock }}</p>
                        <p class="mt-0.5 text-[11px] text-green-600 dark:text-green-400">Productos disponibles</p>
                    </div>
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Sin stock --}}
            <a href="{{ route('inventario.index', ['filter' => 'without-stock']) }}"
               id="filter-no-stock"
               class="group rounded-xl border bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-800
               {{ $filter == 'without-stock'
                    ? 'border-red-500 ring-1 ring-red-500/20 dark:border-red-400'
                    : 'border-gray-200 dark:border-gray-700' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Sin stock</p>
                        <p class="mt-1 text-xl font-bold leading-tight text-gray-900 dark:text-white">{{ $totalSinStock }}</p>
                        <p class="mt-0.5 text-[11px] text-red-600 dark:text-red-400">Requieren atención</p>
                    </div>
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Valor del inventario --}}
            <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Valor del inventario</p>
                        <p class="mt-1 truncate text-xl font-bold leading-tight text-gray-900 dark:text-white">{{ '$' }}{{ number_format($valorTotal, 2) }}</p>
                        <p class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">Valor total registrado</p>
                    </div>
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

    @endif

    {{-- ============================================================
         PRODUCTOS
    ============================================================ --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

        {{-- Encabezado --}}
        <div class="border-b border-gray-200 px-5 py-5 dark:border-gray-700 sm:px-6">

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Productos
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Consulta existencias, costos y productos disponibles.
                    </p>
                </div>


                {{-- Buscador --}}
                <form method="GET"
                      action="{{ route('inventario.index') }}"
                      id="search-form"
                      data-ajax-url="{{ route('inventario.search.ajax') }}"
                      data-csrf-token="{{ csrf_token() }}"
                      class="w-full lg:w-96">

                    <input type="hidden"
                           name="filter"
                           id="filter-input"
                           value="{{ $filter }}">

                    <div class="relative">

                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">

                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>

                        </div>

                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ $search }}"
                               autocomplete="off"
                               placeholder="Buscar producto, categoría, económico..."
                               class="block w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">


                        <button type="button"
                                id="clear-search"
                                @if(!$search) hidden @endif
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition hover:text-gray-700 dark:hover:text-gray-200"
                                title="Limpiar búsqueda"
                                aria-label="Limpiar búsqueda">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- Controles de resultados --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-3 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div id="result-counter"
                 class="text-sm text-gray-500 dark:text-gray-400"
                 aria-live="polite">
                @if($inventarios->total() > 0)
                    Mostrando {{ $inventarios->firstItem() }}–{{ $inventarios->lastItem() }} de {{ $inventarios->total() }} productos
                @else
                    No hay productos para mostrar
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <label for="per-page" class="text-sm text-gray-600 dark:text-gray-300">
                    Mostrar
                </label>
                <select id="per-page"
                        name="per_page"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach([15, 25, 50, 100] as $option)
                        <option value="{{ $option }}" @selected($perPage === $option)>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                <span class="text-sm text-gray-600 dark:text-gray-300">por página</span>

                <button type="button"
                        id="clear-filters"
                        @if($filter === 'all' && $search === '') hidden @endif
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">
                    Limpiar filtros
                </button>
            </div>
        </div>

        {{-- ========================================================
             TABLA
        ========================================================= --}}
        <div class="w-full overflow-x-auto">

            <table class="min-w-[1100px] w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">

                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="w-[30%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Producto
                        </th>

                        <th class="w-[14%] px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Económico
                        </th>

                        <th class="w-[12%] px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Categoría
                        </th>

                        <th class="w-[8%] px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Medida
                        </th>

                        <th class="w-[13%] px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Existencia
                        </th>

                        <th class="w-[10%] px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Precio
                        </th>

                        <th class="w-[13%] px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Acciones
                        </th>
                    </tr>
                </thead>


                <tbody id="inventory-table"
                       class="divide-y divide-gray-100 bg-white dark:divide-gray-700 dark:bg-gray-800">

                    @include('inventario.partials.table-rows', [
                        'inventarios' => $inventarios
                    ])

                </tbody>

            </table>

        </div>

        {{-- Paginación, también actualizada por AJAX --}}
        <div id="pagination-container"
             class="border-t border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
            {{ $inventarios->links() }}
        </div>

    </div>

</div>
@endsection


{{-- ================================================================
     JAVASCRIPT
================================================================ --}}
@push('scripts')

    <script src="{{ asset('js/inventario/index.js') }}"></script>

@endpush