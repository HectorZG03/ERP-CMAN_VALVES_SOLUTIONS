@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Inventario de Productos</h1>
        <div class="flex space-x-3">
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
    @if($inventarios->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Productos -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft rounded-lg border border-gray-100 dark:border-gray-700 transition-colors duration-200 cursor-pointer hover:shadow-md" 
             id="filter-all" data-filter="all">
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
                            <dd class="text-lg font-medium text-gray-800 dark:text-white">{{ $inventarios->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- En Stock -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft rounded-lg border border-gray-100 dark:border-gray-700 transition-colors duration-200 cursor-pointer hover:shadow-md" 
             id="filter-stock" data-filter="with-stock">
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
                            <dd class="text-lg font-medium text-gray-800 dark:text-white">{{ $inventarios->where('existencia', '>', 0)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sin Stock -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft rounded-lg border border-gray-100 dark:border-gray-700 transition-colors duration-200 cursor-pointer hover:shadow-md" 
             id="filter-no-stock" data-filter="without-stock">
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
                            <dd class="text-lg font-medium text-gray-800 dark:text-white">{{ $inventarios->where('existencia', '<=', 0)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

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
                            <dd class="text-lg font-medium text-gray-800 dark:text-white">${{ number_format($inventarios->sum('precio_total'), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

    <!-- Buscador y tabla -->
    <div class="bg-white dark:bg-gray-800 shadow-soft overflow-hidden sm:rounded-md border border-gray-100 dark:border-gray-700 transition-colors duration-200">
        <div class="px-4 py-5 sm:p-6">
            <!-- Buscador mejorado -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="search" placeholder="Buscar productos por nombre, categoría o medida..." 
                           class="light-mode-input block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 dark:focus:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Producto
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
                        @forelse($inventarios as $inventario)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 inventory-row" 
                            data-stock="{{ $inventario->existencia > 0 ? 'with-stock' : 'without-stock' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg bg-blue-500 dark:bg-blue-600 flex items-center justify-center shadow-sm">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($inventario->nombre_producto, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-800 dark:text-white">
                                            {{ $inventario->nombre_producto }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    {{ $inventario->categoria }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $inventario->medida }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($inventario->existencia > 10)
                                        <div class="flex-shrink-0 h-2 w-2 bg-green-400 dark:bg-green-500 rounded-full mr-2"></div>
                                        <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $inventario->existencia }}</span>
                                    @elseif($inventario->existencia > 0)
                                        <div class="flex-shrink-0 h-2 w-2 bg-yellow-400 dark:bg-yellow-500 rounded-full mr-2"></div>
                                        <span class="text-sm font-semibold text-yellow-600 dark:text-yellow-400">{{ $inventario->existencia }}</span>
                                    @else
                                        <div class="flex-shrink-0 h-2 w-2 bg-red-400 dark:bg-red-500 rounded-full mr-2"></div>
                                        <span class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $inventario->existencia }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    <span class="font-medium">${{ number_format($inventario->getPrecioPromedio(), 2) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
                                <span class="font-semibold text-blue-600 dark:text-blue-400">
                                    ${{ number_format($inventario->precio_total, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- Botón Ver -->
                                    <a href="{{ route('inventario.show', $inventario) }}" 
                                       class="inline-flex items-center px-2 py-1 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 text-blue-800 dark:text-blue-300 text-xs font-medium rounded-md transition-colors duration-200 shadow-sm">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Ver
                                    </a>

                                    @if(auth()->user()->canManageInventory())
                                        <!-- Botón Editar -->
                                        <a href="{{ route('inventario.edit', $inventario) }}" 
                                           class="inline-flex items-center px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 hover:bg-yellow-200 dark:hover:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 text-xs font-medium rounded-md transition-colors duration-200 shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </a>

                                        <!-- Botón Eliminar -->
                                        <form method="POST" action="{{ route('inventario.destroy', $inventario) }}" 
                                              class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto?\n\nEsta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-2 py-1 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-800 dark:text-red-300 text-xs font-medium rounded-md transition-colors duration-200 shadow-sm">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-800 dark:text-white">No hay productos</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza agregando tu primer producto al inventario.</p>
                                @if(auth()->user()->canManageInventory())
                                <div class="mt-6">
                                    <a href="{{ route('inventario.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Agregar Producto
                                    </a>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $inventarios->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Script mejorado para búsqueda y filtros -->
<script>
// Variables globales
let currentFilter = 'all';
let searchTerm = '';

// Función para aplicar filtros
function applyFilters() {
    let rows = document.querySelectorAll('.inventory-row');
    
    rows.forEach(row => {
        let stockStatus = row.getAttribute('data-stock');
        let text = row.textContent.toLowerCase();
        
        // Verificar si la fila coincide con el filtro de stock y el término de búsqueda
        const matchesFilter = currentFilter === 'all' || stockStatus === currentFilter;
        const matchesSearch = searchTerm === '' || text.includes(searchTerm);
        
        if (matchesFilter && matchesSearch) {
            row.style.display = '';
            row.classList.add('fade-in');
        } else {
            row.style.display = 'none';
        }
    });
}

// Evento para el buscador
document.getElementById('search').addEventListener('keyup', function() {
    searchTerm = this.value.toLowerCase();
    applyFilters();
});

// Eventos para los filtros de stock
document.getElementById('filter-all').addEventListener('click', function() {
    currentFilter = 'all';
    updateActiveFilter(this);
    applyFilters();
});

document.getElementById('filter-stock').addEventListener('click', function() {
    currentFilter = 'with-stock';
    updateActiveFilter(this);
    applyFilters();
});

document.getElementById('filter-no-stock').addEventListener('click', function() {
    currentFilter = 'without-stock';
    updateActiveFilter(this);
    applyFilters();
});

// Función para actualizar el filtro activo visualmente
function updateActiveFilter(activeElement) {
    // Quitar la clase activa de todos los filtros
    document.querySelectorAll('[id^="filter-"]').forEach(filter => {
        filter.classList.remove('border-blue-500', 'dark:border-blue-400', 'border-2');
        filter.classList.add('border-gray-100', 'dark:border-gray-700');
    });
    
    // Agregar la clase activa al filtro seleccionado
    activeElement.classList.remove('border-gray-100', 'dark:border-gray-700');
    activeElement.classList.add('border-blue-500', 'dark:border-blue-400', 'border-2');
}

// Inicializar con el filtro "Todos" activo
document.addEventListener('DOMContentLoaded', function() {
    updateActiveFilter(document.getElementById('filter-all'));
});

// Animación simple para las filas filtradas
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
`);
</script>
@endsection