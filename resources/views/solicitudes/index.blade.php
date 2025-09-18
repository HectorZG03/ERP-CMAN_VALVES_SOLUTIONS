@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Solicitudes de Material</h1>
        <a href="{{ route('solicitudes.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Nueva Solicitud
        </a>
    </div>

    <!-- Mostrar filtros si es personal autorizado -->
    @if(auth()->user()->canApproveRequests() || auth()->user()->canManageInventory())
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 transition-colors duration-200">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Vista:</span>
                
                <!-- Filtros por Estado -->
                <div class="flex gap-2">
                    <!-- Filtro Todos -->
                    <button type="button" data-status="all" 
                            class="status-filter active px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                        <span>Todas</span>
                        <span class="status-count ml-1" id="count-all">{{ $solicitudes->total() }}</span>
                    </button>

                    <!-- Filtro Pendientes -->
                    <button type="button" data-status="pendiente" 
                            class="status-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 hover:text-yellow-800 dark:hover:text-yellow-300">
                        <span>Pendientes</span>
                        <span class="status-count ml-1" id="count-pendiente">0</span>
                    </button>

                    <!-- Filtro Aprobados -->
                    <button type="button" data-status="aprobado" 
                            class="status-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-green-100 dark:hover:bg-green-900/30 hover:text-green-800 dark:hover:text-green-300">
                        <span>Aprobados</span>
                        <span class="status-count ml-1" id="count-aprobado">0</span>
                    </button>

                    <!-- Filtro Denegados -->
                    <button type="button" data-status="denegado" 
                            class="status-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-800 dark:hover:text-red-300">
                        <span>Denegados</span>
                        <span class="status-count ml-1" id="count-denegado">0</span>
                    </button>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    • Total visible: <span id="total-visible" class="font-medium">{{ $solicitudes->total() }}</span> solicitudes
                </span>
            </div>
        </div>
    </div>
    @endif
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <!-- Buscador mejorado -->
                    <div class="mb-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="search" placeholder="Buscar solicitudes por nombre, categoría o medida..." 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 dark:focus:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha / ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Productos Solicitados
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Destino
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Resumen
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Solicitante
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estatus
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($solicitudes as $solicitud)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" data-status="{{ $solicitud->estatus }}">
                                    <!-- Fecha / ID -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            #{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $solicitud->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $solicitud->created_at->format('H:i') }}
                                        </div>
                                    </td>

                                    <!-- Productos Solicitados -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            @if($solicitud->detalles && $solicitud->detalles->count() > 0)
                                                @foreach($solicitud->detalles->take(3) as $detalle)
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <span class="text-xs font-medium text-blue-600 dark:text-blue-400">
                                                            {{ substr($detalle->inventario->nombre_producto ?? 'N/A', 0, 2) }}
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                            {{ $detalle->inventario->nombre_producto ?? 'Producto no disponible' }}
                                                        </div>
                                                        
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $detalle->cantidad_solicitada }} {{ $detalle->inventario->medida ?? '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                                
                                                @if($solicitud->detalles->count() > 3)
                                                <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                                                    +{{ $solicitud->detalles->count() - 3 }} producto(s) más
                                                </div>
                                                @endif
                                            @else
                                                <div class="text-sm text-red-500 dark:text-red-400">
                                                    No hay productos asociados
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Destino -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <!-- Icono de casa fuera del círculo -->
                                            <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/>
                                            </svg>
                                            <div>
                                                @if($solicitud->destino)
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $solicitud->destino }}
                                                    </div>
                                                    @if($solicitud->destino == 'BMS MAYA')
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $solicitud->user->name }} - {{ $solicitud->cantidad_solicitada }} unidades
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="text-sm font-medium text-red-600 dark:text-red-400">
                                                        Destino no disponible
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Resumen -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 mr-1 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $solicitud->total_productos }}</span>
                                                <span class="text-gray-500 dark:text-gray-400 ml-1">productos</span>
                                            </div>
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 mr-1 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 6 6 6-6 3 3-9 9-9-9z"/>
                                                </svg>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $solicitud->total_unidades }}</span>
                                                <span class="text-gray-500 dark:text-gray-400 ml-1">unidades</span>
                                            </div>
                                            @if($solicitud->total > 0)
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 mr-1 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                                </svg>
                                                <span class="font-medium text-gray-900 dark:text-white">${{ number_format($solicitud->total, 2) }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Solicitante -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        {{ substr($solicitud->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $solicitud->user->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ ucfirst(str_replace('_', ' ', $solicitud->user->role)) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Estatus -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($solicitud->estatus === 'pendiente')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                Pendiente
                                            </span>
                                        @elseif($solicitud->estatus === 'aprobado')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Aprobado
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                                Denegado
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <!-- Botón Ver -->
                                            @if(auth()->user()->hasRole(['almacen', 'aux_almacen']) || $solicitud->user_id == auth()->id() || auth()->user()->canApproveRequests())
                                                <a href="{{ route('solicitudes.show', $solicitud) }}" 
                                                   class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-800 dark:text-blue-300 text-xs font-medium rounded-md transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Ver Detalles
                                                </a>
                                            @endif

                                            <!-- Botones de aprobar/denegar para quienes pueden aprobar -->
                                            @if(auth()->user()->canApproveRequests() && $solicitud->estatus === 'pendiente')
                                                <div class="flex space-x-1">
                                                    <form method="POST" action="{{ route('solicitudes.updateEstatus', $solicitud) }}" class="inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="estatus" value="aprobado">
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-800 dark:text-green-300 text-xs font-medium rounded-md transition-colors duration-200" 
                                                                onclick="return confirm('¿Aprobar esta solicitud?\n\nEsto descontará los productos del inventario.')">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Aprobar
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('solicitudes.updateEstatus', $solicitud) }}" class="inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="estatus" value="denegado">
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-800 dark:text-red-300 text-xs font-medium rounded-md transition-colors duration-200"
                                                                onclick="return confirm('¿Denegar esta solicitud?')">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Denegar
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif(!auth()->user()->canApproveRequests() && !auth()->user()->hasRole(['almacen', 'aux_almacen']) && $solicitud->user_id != auth()->id())
                                                <span class="text-gray-400 dark:text-gray-500 text-xs italic">Sin acciones disponibles</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr id="no-results-row">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No hay solicitudes</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">No se encontraron solicitudes de material.</p>
                                            <a href="{{ route('solicitudes.create') }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Crear Primera Solicitud
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $solicitudes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
.table-hover-row:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.status-badge {
    transition: all 0.2s ease-in-out;
}

.status-badge:hover {
    transform: scale(1.05);
}

.action-button {
    transition: all 0.2s ease-in-out;
}

.action-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.status-filter.active {
    transform: scale(1.02);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.status-filter:not(.active):hover {
    transform: translateY(-1px);
}

/* Mejora para modo oscuro */
@media (prefers-color-scheme: dark) {
    .table-hover-row:hover {
        box-shadow: 0 4px 6px -1px rgba(255, 255, 255, 0.1);
    }
    
    .action-button:hover {
        box-shadow: 0 2px 4px rgba(255, 255, 255, 0.1);
    }

    .status-filter.active {
        box-shadow: 0 4px 6px -1px rgba(255, 255, 255, 0.1);
    }
}

.fade-out {
    opacity: 0.3;
    transition: opacity 0.3s ease;
}
</style>

<!-- Script mejorado para búsqueda y filtros -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const statusFilters = document.querySelectorAll('.status-filter');
    const tableRows = document.querySelectorAll('tbody tr[data-status]');
    const noResultsRow = document.getElementById('no-results-row');
    
    let currentStatusFilter = 'all';
    let currentSearchTerm = '';

    // Contar solicitudes por estado al cargar la página
    function updateStatusCounts() {
        const counts = {
            pendiente: 0,
            aprobado: 0,
            denegado: 0,
            all: tableRows.length
        };

        tableRows.forEach(row => {
            const status = row.getAttribute('data-status');
            if (counts.hasOwnProperty(status)) {
                counts[status]++;
            }
        });

        // Actualizar contadores en la interfaz
        Object.keys(counts).forEach(status => {
            const countElement = document.getElementById(`count-${status}`);
            if (countElement) {
                countElement.textContent = counts[status];
            }
        });
    }

    // Función para aplicar filtros
    function applyFilters() {
        let visibleCount = 0;
        let hasVisibleRows = false;

        tableRows.forEach(row => {
            const status = row.getAttribute('data-status');
            const text = row.textContent.toLowerCase();
            
            const statusMatch = currentStatusFilter === 'all' || status === currentStatusFilter;
            const searchMatch = currentSearchTerm === '' || text.includes(currentSearchTerm);
            
            if (statusMatch && searchMatch) {
                row.style.display = '';
                row.classList.remove('fade-out');
                visibleCount++;
                hasVisibleRows = true;
            } else {
                row.style.display = 'none';
                row.classList.add('fade-out');
            }
        });

        // Mostrar/ocultar mensaje de "no hay resultados"
        if (noResultsRow) {
            if (!hasVisibleRows && tableRows.length > 0) {
                noResultsRow.style.display = '';
                noResultsRow.querySelector('h3').textContent = 'No se encontraron solicitudes';
                noResultsRow.querySelector('p').textContent = currentSearchTerm 
                    ? `No hay solicitudes que coincidan con "${currentSearchTerm}" y el filtro seleccionado.`
                    : `No hay solicitudes con el estado "${getStatusLabel(currentStatusFilter)}".`;
            } else {
                noResultsRow.style.display = 'none';
            }
        }

        // Actualizar contador total visible
        const totalVisibleElement = document.getElementById('total-visible');
        if (totalVisibleElement) {
            totalVisibleElement.textContent = visibleCount;
        }
    }

    // Obtener etiqueta del estado
    function getStatusLabel(status) {
        const labels = {
            'all': 'Todas',
            'pendiente': 'Pendientes',
            'aprobado': 'Aprobados',
            'denegado': 'Denegados'
        };
        return labels[status] || status;
    }

    // Event listener para búsqueda
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            currentSearchTerm = this.value.toLowerCase();
            applyFilters();
        });
    }

    // Event listeners para filtros de estado
    statusFilters.forEach(filter => {
        filter.addEventListener('click', function () {
            // Remover clase active de todos los filtros
            statusFilters.forEach(f => {
                f.classList.remove('active');
                f.classList.remove('bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-800', 'dark:text-blue-300');
                f.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });

            // Agregar clase active al filtro seleccionado
            this.classList.add('active');
            this.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            this.classList.add('bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-800', 'dark:text-blue-300');

            // Actualizar filtro actual
            currentStatusFilter = this.getAttribute('data-status');

            // Aplicar filtros
            applyFilters();
        });
    });

    // Inicializar contadores y aplicar filtros
    updateStatusCounts();
    applyFilters();
});
</script>

@endsection