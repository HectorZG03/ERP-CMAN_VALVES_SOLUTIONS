@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('proveedores.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Proveedores
            </a>
            
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Proveedor #{{ str_pad($proveedor->id, 4, '0', STR_PAD_LEFT) }} ID: {{ $proveedor->economico }}
            </h1>
        </div>

        <a href="{{ route('proveedores.edit', $proveedor) }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar Proveedor
        </a>
    </div>

    <!-- Información del Proveedor -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800 transition-colors duration-200">
        <div class="flex items-start space-x-6">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <div class="h-24 w-24 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center">
                    <span class="text-3xl font-bold text-white">
                        {{ substr($proveedor->proveedor, 0, 2) }}
                    </span>
                </div>
            </div>

            <!-- Información -->
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $proveedor->proveedor }}
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Dirección</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $proveedor->direccion }}
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Fecha de Registro</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $proveedor->created_at->format('d/m/Y') }}
                            <span class="text-sm text-gray-500 dark:text-gray-400">({{ $proveedor->created_at->diffForHumans() }})</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Proveedor -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Total Entradas</p>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $proveedor->entradas->count() }}
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Última Entrada</p>
                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                    @if($proveedor->entradas->count() > 0)
                        {{ $proveedor->entradas->sortByDesc('created_at')->first()->created_at->format('d/m/Y') }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Días Activo</p>
                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    {{ intval($proveedor->created_at->diffInDays(now())) }}
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Estado</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Activo
                </span>
            </div>
        </div>
    </div>

    <!-- Historial de Entradas -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                Historial de Entradas
            </h3>

            @if($proveedor->entradas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Producto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Cantidad
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($proveedor->entradas->sortByDesc('created_at')->take(10) as $entrada)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $entrada->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $entrada->inventario->nombre_producto ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $entrada->cantidad }} {{ $entrada->inventario->medida ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('entradas.show', $entrada) }}" 
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        Ver Detalles
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($proveedor->entradas->count() > 10)
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Mostrando las últimas 10 entradas de {{ $proveedor->entradas->count() }} totales
                    </p>
                </div>
                @endif
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="font-medium">No hay entradas registradas para este proveedor</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Acciones -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                Acciones Adicionales
            </h3>
            
            <div class="flex space-x-3">
                <a href="{{ route('proveedores.edit', $proveedor) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white text-sm font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar Información
                </a>

                <form method="POST" action="{{ route('proveedores.destroy', $proveedor) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 text-white text-sm font-medium rounded-md transition-colors duration-200"
                            onclick="return confirm('¿Estás seguro de eliminar este proveedor?\n\nEsta acción no se puede deshacer y eliminará toda la información relacionada.')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar Proveedor
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection