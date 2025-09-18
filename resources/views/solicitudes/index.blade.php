@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Solicitudes de Material</h1>
        <a href="{{ route('solicitudes.create') }}" 
           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Nueva Solicitud
        </a>
    </div>

    <!-- Mostrar filtros si es personal autorizado -->
    @if(auth()->user()->canApproveRequests() || auth()->user()->canManageInventory())
    <div class="bg-white shadow sm:rounded-lg p-4">
        <div class="flex flex-wrap gap-4 items-center">
            <span class="text-sm font-medium text-gray-700">Vista:</span>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                Todas las solicitudes
            </span>
            <span class="text-sm text-gray-500">
                • Total: {{ $solicitudes->total() }} solicitudes
            </span>
        </div>
    </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha / ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Productos Solicitados
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Destino
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Resumen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Solicitante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estatus
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($solicitudes as $solicitud)
                        <tr class="hover:bg-gray-50">
                            <!-- Fecha / ID -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    #{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $solicitud->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $solicitud->created_at->format('H:i') }}
                                </div>
                            </td>

                            <!-- Productos Solicitados -->
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    @if($solicitud->detalles && $solicitud->detalles->count() > 0)
                                        @foreach($solicitud->detalles->take(3) as $detalle)
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-medium text-blue-600">
                                                    {{ substr($detalle->inventario->nombre_producto ?? 'N/A', 0, 2) }}
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $detalle->inventario->nombre_producto ?? 'Producto no disponible' }}
                                                </div>
                                                
                                                <div class="text-xs text-gray-500">
                                                    {{ $detalle->cantidad_solicitada }} {{ $detalle->inventario->medida ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        
                                        @if($solicitud->detalles->count() > 3)
                                        <div class="text-xs text-blue-600 font-medium">
                                            +{{ $solicitud->detalles->count() - 3 }} producto(s) más
                                        </div>
                                        @endif
                                    @else
                                        <div class="text-sm text-red-500">
                                            No hay productos asociados
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Destino -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <!-- Icono de casa fuera del círculo -->
                                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/>
                                    </svg>
                                    <div>
                                        @if($solicitud->destino)
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $solicitud->destino }}
                                            </div>
                                            @if($solicitud->destino == 'BMS MAYA')
                                                <div class="text-xs text-gray-500">
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
                                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span class="font-medium text-gray-900">{{ $solicitud->total_productos }}</span>
                                        <span class="text-gray-500 ml-1">productos</span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 6 6 6-6 3 3-9 9-9-9z"/>
                                        </svg>
                                        <span class="font-medium text-gray-900">{{ $solicitud->total_unidades }}</span>
                                        <span class="text-gray-500 ml-1">unidades</span>
                                    </div>
                                    @if($solicitud->total > 0)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                        <span class="font-medium text-gray-900">${{ number_format($solicitud->total, 2) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Solicitante -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($solicitud->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $solicitud->user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ ucfirst(str_replace('_', ' ', $solicitud->user->role)) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Estatus -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($solicitud->estatus === 'pendiente')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Pendiente
                                    </span>
                                @elseif($solicitud->estatus === 'aprobado')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Aprobado
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
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
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium rounded-md transition-colors duration-200">
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
                                                        class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 text-green-800 text-xs font-medium rounded-md transition-colors duration-200" 
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
                                                        class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-800 text-xs font-medium rounded-md transition-colors duration-200"
                                                        onclick="return confirm('¿Denegar esta solicitud?')">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Denegar
                                                </button>
                                            </form>
                                        </div>
                                    @elseif(!auth()->user()->canApproveRequests() && !auth()->user()->hasRole(['almacen', 'aux_almacen']) && $solicitud->user_id != auth()->id())
                                        <span class="text-gray-400 text-xs italic">Sin acciones disponibles</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">No hay solicitudes</h3>
                                    <p class="text-sm text-gray-500 mb-4">No se encontraron solicitudes de material.</p>
                                    <a href="{{ route('solicitudes.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
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

<!-- Estilos adicionales para mejorar la legibilidad -->
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
</style>
@endsection