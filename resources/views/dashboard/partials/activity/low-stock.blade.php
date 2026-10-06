<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-300 dark:border-gray-700 overflow-hidden">

    {{-- HEADER --}}
    <div class="p-5 border-b border-gray-200 dark:border-gray-700">

        <div class="flex items-start justify-between gap-4">

            <div class="flex items-start gap-3 min-w-0">

                <div class="w-10 h-10 shrink-0 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <i class="fas fa-triangle-exclamation text-red-600 dark:text-red-400"></i>
                </div>

                <div class="min-w-0">

                    <h2 class="font-semibold text-gray-800 dark:text-white">
                        Bajo stock
                    </h2>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $productosBajoStock->total() }}
                        {{ $productosBajoStock->total() === 1 ? 'producto requiere' : 'productos requieren' }}
                        atención
                    </p>

                </div>

            </div>


            <a href="{{ route('inventario.index') }}"
               class="shrink-0 text-sm text-blue-600 dark:text-blue-400 hover:underline">

                Ver inventario

            </a>

        </div>


        {{-- BOTÓN ACORDEÓN --}}
        <button
            type="button"
            data-dashboard-toggle="low-stock"
            aria-expanded="false"
            class="mt-4 w-full flex items-center justify-between text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">

            <span>
                Mostrar productos
            </span>

            <i
                data-dashboard-chevron="low-stock"
                class="fas fa-chevron-down text-xs transition-transform duration-200">
            </i>

        </button>

    </div>


    {{-- CONTENIDO --}}
    <div
        data-dashboard-content="low-stock"
        class="hidden">

        {{-- FILTROS --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">

            <form
                method="GET"
                action="{{ route('dashboard') }}"
                data-dashboard-filter="low-stock"
                class="space-y-3">

                {{-- Mantener búsqueda/estado de la otra tabla --}}
                @if(request('request_search'))
                    <input
                        type="hidden"
                        name="request_search"
                        value="{{ request('request_search') }}">
                @endif

                @if(request('request_status'))
                    <input
                        type="hidden"
                        name="request_status"
                        value="{{ request('request_status') }}">
                @endif


                <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2">

                    {{-- Buscar --}}
                    <div class="relative">

                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

                        <input
                            type="text"
                            name="stock_search"
                            value="{{ request('stock_search') }}"
                            data-dashboard-live-search
                            autocomplete="off"
                            placeholder="Buscar producto..."
                            class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>


                    {{-- Estado --}}
                    <select
                        name="stock_filter"
                        data-dashboard-live-filter
                        class="w-full sm:w-auto min-w-[150px] px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">

                        <option value="todos" {{ request('stock_filter', 'todos') === 'todos' ? 'selected' : '' }}>
                            Todos
                        </option>

                        <option value="agotados" {{ request('stock_filter') === 'agotados' ? 'selected' : '' }}>
                            Agotados
                        </option>

                        <option value="bajo" {{ request('stock_filter') === 'bajo' ? 'selected' : '' }}>
                            Stock 1 - 5
                        </option>

                    </select>

                </div>


                <div class="flex items-center justify-between gap-2">

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">

                        <i class="fas fa-filter"></i>

                        Filtrar

                    </button>


                    @if(request('stock_search') || request('stock_filter'))

                        <a
                            href="{{ route('dashboard', array_filter([
                                'request_search' => request('request_search'),
                                'request_status' => request('request_status'),
                            ])) }}"
                            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">

                            Limpiar

                        </a>

                    @endif

                </div>

            </form>

        </div>


        {{-- TABLA --}}
        <div class="overflow-x-auto">

            <table class="w-full table-fixed">

                <thead class="bg-gray-50 dark:bg-gray-900/40">

                    <tr>

                        <th class="w-[75%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Producto
                        </th>

                        <th class="w-[25%] px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Stock
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                    @forelse($productosBajoStock as $producto)

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">

                            <td class="px-4 py-3 align-middle">

                                <div
                                    class="font-medium text-sm text-gray-800 dark:text-gray-200"
                                    style="
                                        display: -webkit-box;
                                        -webkit-box-orient: vertical;
                                        -webkit-line-clamp: 2;
                                        overflow: hidden;
                                        word-break: break-word;
                                    "
                                    title="{{ $producto->nombre_producto }}">

                                    {{ $producto->nombre_producto }}

                                </div>

                            </td>


                            <td class="px-4 py-3 text-center align-middle">

                                @if((int) $producto->existencia === 0)

                                    <span class="inline-flex items-center justify-center min-w-[42px] px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        0
                                    </span>

                                @else

                                    <span class="inline-flex items-center justify-center min-w-[42px] px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        {{ $producto->existencia }}
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="2" class="px-4 py-10 text-center">

                                <div class="flex flex-col items-center justify-center">

                                    <i class="fas fa-box-open text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>

                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        No hay productos que coincidan con el filtro.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINACIÓN --}}
        @if($productosBajoStock->hasPages())

            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <p class="text-xs text-gray-500 dark:text-gray-400">

                        Mostrando
                        <span class="font-medium">
                            {{ $productosBajoStock->firstItem() }}
                        </span>

                        -
                        <span class="font-medium">
                            {{ $productosBajoStock->lastItem() }}
                        </span>

                        de
                        <span class="font-medium">
                            {{ $productosBajoStock->total() }}
                        </span>

                    </p>


                    <div data-dashboard-pagination="low-stock">

                        {{ $productosBajoStock->onEachSide(1)->links() }}

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>