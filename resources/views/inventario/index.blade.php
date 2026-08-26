@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Inventario de Productos</h1>
        <div class="flex space-x-3">
            <a href="{{ route('inventario.ajustes.index') }}"
               class="inline-flex items-center px-3 py-2 bg-purple-600 dark:bg-purple-700 hover:bg-purple-700 dark:hover:bg-purple-800 text-white text-sm font-medium rounded-md transition-colors duration-200 shadow-sm hover:shadow-md">
                Historial de ajustes
            </a>
            <!-- Botones de descarga -->
            <div class="flex space-x-2">
                <a href="{{ route('inventario.export.pdf') }}" 
                   class="inline-flex items-center px-3 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 text-white text-sm font-medium rounded-md transition-colors duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    PDF
                </a>
            </div>
            
            <!-- Botón agregar producto -->
            @if(auth()->user()->canManageInventory())
                <a href="{{ route('inventario.create') }}" 
                   class="bg-blue-500 dark:bg-blue-600 hover:bg-blue-700 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar Producto
                </a>
            @endif
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    @if(auth()->user()->canManageInventory() || auth()->user()->canManageInventoryadmin())
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Productos -->
        <a href="{{ route('inventario.index', ['filter' => 'all']) }}" 
           class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft rounded-lg border {{ $filter == 'all' ? 'border-blue-500 dark:border-blue-400 border-2' : 'border-gray-100 dark:border-gray-700' }} transition-colors duration-200 cursor-pointer hover:shadow-md" 
           id="filter-all">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Total Productos</dt>
                            <dd class="text-lg font-medium text-gray-800 dark:text-white">{{ $totalInventario }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>

        <!-- En Stock -->
        <a href="{{ route('inventario.index', ['filter' => 'with-stock']) }}" 
           class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft rounded-lg border {{ $filter == 'with-stock' ? 'border-green-500 dark:border-green-400 border-2' : 'border-gray-100 dark:border-gray-700' }} transition-colors duration-200 cursor-pointer hover:shadow-md" 
           id="filter-stock">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">En Stock</dt>
                            <dd class="text-lg font-medium text-gray-800 dark:text-white">{{ $totalEnStock }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>

        <!-- Sin Stock -->
        <a href="{{ route('inventario.index', ['filter' => 'without-stock']) }}" 
           class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft rounded-lg border {{ $filter == 'without-stock' ? 'border-red-500 dark:border-red-400 border-2' : 'border-gray-100 dark:border-gray-700' }} transition-colors duration-200 cursor-pointer hover:shadow-md" 
           id="filter-no-stock">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Sin Stock</dt>
                            <dd class="text-lg font-medium text-gray-800 dark:text-white">{{ $totalSinStock }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>

        <!-- Valor Total -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft rounded-lg border border-gray-100 dark:border-gray-700 transition-colors duration-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Valor Total</dt>
                            <dd class="text-lg font-medium text-gray-800 dark:text-white">${{ number_format($valorTotal, 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Buscador y tabla -->
    <div class="bg-white dark:bg-gray-800 shadow-soft overflow-hidden sm:rounded-md border border-gray-100 dark:border-gray-700 transition-colors duration-200">
        <div class="px-4 py-5 sm:p-6">
            <!-- Buscador mejorado con resultados en tiempo real -->
            <div class="mb-6">
                <form method="GET" action="{{ route('inventario.index') }}" id="search-form">
                    <input type="hidden" name="filter" id="filter-input" value="{{ $filter }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ $search }}"
                               placeholder="Buscar productos por nombre, categoría o medida..." 
                               class="light-mode-input block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 dark:focus:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        
                        <!-- Botón limpiar búsqueda -->
                        @if($search)
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <a href="{{ route('inventario.index', ['filter' => $filter]) }}" 
                               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </div>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Contador de resultados -->
            <div class="mb-4 flex justify-between items-center">
                <div id="result-counter" class="text-sm text-gray-600 dark:text-gray-400">
                    @if($filter == 'all' && !$search)
                        Mostrando {{ $inventarios->count() }} de {{ $totalInventario }} productos
                    @elseif($filter == 'with-stock' && !$search)
                        Mostrando {{ $inventarios->count() }} productos en stock de {{ $totalEnStock }} totales
                    @elseif($filter == 'without-stock' && !$search)
                        Mostrando {{ $inventarios->count() }} productos sin stock de {{ $totalSinStock }} totales
                    @else
                        {{ $inventarios->count() }} resultados encontrados
                    @endif
                </div>
                
                @if($filter !== 'all' || $search)
                <a href="{{ route('inventario.index') }}" 
                   class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Limpiar filtros
                </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Producto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Economico
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Categoría
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Medida
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Existencia
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Precio Unitario
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Valor Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="inventory-table">
                        @include('inventario.partials.table-rows', ['inventarios' => $inventarios])
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($showPagination && $inventarios instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">
                {{ $inventarios->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Script para búsqueda en tiempo real -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const filterInput = document.getElementById('filter-input');
    const inventoryTable = document.getElementById('inventory-table');
    const resultCounter = document.getElementById('result-counter');
    const searchForm = document.getElementById('search-form');
    
    // Variables para debounce
    let searchTimeout;
    let currentSearch = '{{ $search }}';
    let currentFilter = '{{ $filter }}';
    
    // Función para realizar búsqueda AJAX
    function performSearch(searchTerm, filter) {
        // Mostrar indicador de carga
        inventoryTable.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-12 text-center">
                    <div class="flex justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    </div>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Buscando productos...</p>
                </td>
            </tr>
        `;
        
        fetch('{{ route("inventario.search.ajax") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                search: searchTerm,
                filter: filter
            })
        })
        .then(response => response.json())
        .then(data => {
            inventoryTable.innerHTML = data.html;
            resultCounter.textContent = `${data.count} resultados encontrados`;
            
            // Agregar animación a las filas nuevas
            setTimeout(() => {
                document.querySelectorAll('.inventory-row').forEach((row, index) => {
                    row.style.animationDelay = `${index * 0.05}s`;
                    row.classList.add('fade-in');
                });
            }, 100);
        })
        .catch(error => {
            console.error('Error:', error);
            inventoryTable.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-red-500">
                        Error al cargar los datos. Intenta nuevamente.
                    </td>
                </tr>
            `;
        });
    }
    
    // Evento para búsqueda en tiempo real (con debounce)
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        currentSearch = searchTerm;
        
        // Limpiar timeout anterior
        clearTimeout(searchTimeout);
        
        // Si el campo está vacío, enviar formulario inmediatamente
        if (searchTerm === '') {
            searchForm.submit();
            return;
        }
        
        // Esperar 500ms después de que el usuario deje de escribir
        searchTimeout = setTimeout(() => {
            performSearch(searchTerm, currentFilter);
        }, 500);
    });
    
    // Evento para tecla Enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchForm.submit();
        }
    });
    
    // Función para cambiar filtro
    function changeFilter(filter) {
        currentFilter = filter;
        filterInput.value = filter;
        
        // Si hay búsqueda, usar AJAX
        if (currentSearch) {
            performSearch(currentSearch, filter);
        } else {
            // Si no hay búsqueda, redirigir
            window.location.href = '{{ route("inventario.index") }}?filter=' + filter;
        }
    }
    
    // Asignar eventos a los filtros
    document.getElementById('filter-all').addEventListener('click', function(e) {
        e.preventDefault();
        changeFilter('all');
    });
    
    document.getElementById('filter-stock').addEventListener('click', function(e) {
        e.preventDefault();
        changeFilter('with-stock');
    });
    
    document.getElementById('filter-no-stock').addEventListener('click', function(e) {
        e.preventDefault();
        changeFilter('without-stock');
    });
    
    // Agregar animación a las filas iniciales
    setTimeout(() => {
        document.querySelectorAll('.inventory-row').forEach((row, index) => {
            row.style.animationDelay = `${index * 0.05}s`;
            row.classList.add('fade-in');
        });
    }, 100);
});

// Estilos CSS para animaciones
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .fade-in {
            animation: fadeInUp 0.3s ease-out forwards;
            opacity: 0;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
`);
</script>
@endsection
