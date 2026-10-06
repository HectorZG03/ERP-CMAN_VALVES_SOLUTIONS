<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

    {{-- HEADER --}}
    <div class="p-5 border-b border-gray-200 dark:border-gray-700">

        <div class="flex items-start justify-between gap-4">

            <div class="flex items-start gap-3 min-w-0">

                <div class="w-10 h-10 shrink-0 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">

                    <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-400"></i>

                </div>


                <div class="min-w-0">

                    <h2 class="font-semibold text-gray-800 dark:text-white">
                        Últimas solicitudes
                    </h2>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">

                        {{ $ultimasSolicitudes->total() }}

                        {{ $ultimasSolicitudes->total() === 1 ? 'solicitud registrada' : 'solicitudes registradas' }}

                    </p>

                </div>

            </div>


            <a
                href="{{ route('solicitudes.index') }}"
                class="shrink-0 text-sm text-blue-600 dark:text-blue-400 hover:underline">

                Ver solicitudes

            </a>

        </div>


        {{-- ACORDEÓN --}}
        <button
            type="button"
            data-dashboard-toggle="latest-requests"
            aria-expanded="false"
            class="mt-4 w-full flex items-center justify-between text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">

            <span>
                Mostrar solicitudes
            </span>

            <i
                data-dashboard-chevron="latest-requests"
                class="fas fa-chevron-down text-xs transition-transform duration-200">
            </i>

        </button>

    </div>


    {{-- CONTENIDO --}}
    <div
        data-dashboard-content="latest-requests"
        class="hidden">


        {{-- FILTROS --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">

            <form
                method="GET"
                action="{{ route('dashboard') }}"
                data-dashboard-filter="latest-requests"
                class="space-y-3">

                {{-- Mantener filtros de stock --}}
                @if(request('stock_search'))

                    <input
                        type="hidden"
                        name="stock_search"
                        value="{{ request('stock_search') }}">

                @endif


                @if(request('stock_filter'))

                    <input
                        type="hidden"
                        name="stock_filter"
                        value="{{ request('stock_filter') }}">

                @endif


                <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2">


                    {{-- Buscar --}}
                    <div class="relative">

                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

                        <input
                            type="text"
                            name="request_search"
                            value="{{ request('request_search') }}"
                            data-dashboard-live-search
                            autocomplete="off"
                            placeholder="Buscar por ID, solicitante o material..."
                            class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                    </div>


                    {{-- Estado --}}
                    <select
                        name="request_status"
                        data-dashboard-live-filter
                        class="w-full sm:w-auto min-w-[150px] px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">

                        <option
                            value="todos"
                            {{ request('request_status', 'todos') === 'todos' ? 'selected' : '' }}>

                            Todos los estados

                        </option>


                        @foreach($estadosSolicitudes as $estado)

                            <option
                                value="{{ $estado }}"
                                {{ request('request_status') === $estado ? 'selected' : '' }}>

                                {{ ucfirst($estado) }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="flex items-center justify-between gap-2">


                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">

                        <i class="fas fa-filter"></i>

                        Filtrar

                    </button>


                    @if(request('request_search') || request('request_status'))

                        <a
                            href="{{ route('dashboard', array_filter([
                                'stock_search' => request('stock_search'),
                                'stock_filter' => request('stock_filter'),
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

                        <th class="w-[16%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Solicitud
                        </th>

                        <th class="w-[39%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Solicitante
                        </th>

                        <th class="w-[45%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Material
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                    @forelse($ultimasSolicitudes as $solicitud)

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">


                            {{-- ID --}}
                            <td class="px-4 py-3 align-middle">

                                <span class="font-semibold text-sm text-gray-800 dark:text-gray-200">

                                    #{{ $solicitud->id }}

                                </span>

                            </td>


                            {{-- SOLICITANTE --}}
                            <td class="px-4 py-3 align-middle">

                                <div
                                    class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    style="
                                        display: -webkit-box;
                                        -webkit-box-orient: vertical;
                                        -webkit-line-clamp: 2;
                                        overflow: hidden;
                                        word-break: break-word;
                                    "
                                    title="{{ $solicitud->user->name ?? 'Sin usuario' }}">

                                    {{ $solicitud->user->name ?? 'Sin usuario' }}

                                </div>

                            </td>


                            {{-- MATERIAL --}}
                            <td class="px-4 py-3 align-middle">

                                @php
                                    $materiales = $solicitud->detalles
                                        ->map(fn ($detalle) => $detalle->inventario?->nombre_producto)
                                        ->filter()
                                        ->unique()
                                        ->values();
                                @endphp


                                @if($materiales->isNotEmpty())

                                    <div
                                        class="text-sm text-gray-700 dark:text-gray-300"
                                        style="
                                            display: -webkit-box;
                                            -webkit-box-orient: vertical;
                                            -webkit-line-clamp: 2;
                                            overflow: hidden;
                                            word-break: break-word;
                                        "
                                        title="{{ $materiales->join(', ') }}">

                                        {{ $materiales->join(', ') }}

                                    </div>

                                @else

                                    <span class="text-sm text-gray-400 dark:text-gray-500">
                                        Sin material
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="3" class="px-4 py-10 text-center">

                                <div class="flex flex-col items-center justify-center">

                                    <i class="fas fa-clipboard-list text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>

                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        No hay solicitudes que coincidan con el filtro.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINACIÓN --}}
        @if($ultimasSolicitudes->hasPages())

            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


                    <p class="text-xs text-gray-500 dark:text-gray-400">

                        Mostrando

                        <span class="font-medium">
                            {{ $ultimasSolicitudes->firstItem() }}
                        </span>

                        -

                        <span class="font-medium">
                            {{ $ultimasSolicitudes->lastItem() }}
                        </span>

                        de

                        <span class="font-medium">
                            {{ $ultimasSolicitudes->total() }}
                        </span>

                    </p>


                    <div data-dashboard-pagination="latest-requests">

                        {{ $ultimasSolicitudes->onEachSide(1)->links() }}

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>