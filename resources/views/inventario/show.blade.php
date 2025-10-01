@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header con navegación -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('inventario.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Inventario
            </a>
            
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $inventario->nombre_producto }}
            </h1>
        </div>
        
        <!-- Botones de acción -->
        @if(auth()->user()->canManageInventory())
        <div class="flex space-x-2">
            <a href="{{ route('inventario.edit', $inventario) }}" 
               class="inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-700 hover:bg-yellow-700 dark:hover:bg-yellow-800 text-white font-medium rounded-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar Producto
            </a>
        </div>
        @endif
    </div>

    <!-- Contenido principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información principal -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Información del Producto -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información del Producto
                    </h3>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600 transition-colors duration-200">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-20 h-20 bg-blue-500 dark:bg-blue-600 rounded-lg flex items-center justify-center">
                                    <span class="text-2xl font-bold text-white">
                                        {{ substr($inventario->nombre_producto, 0, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">
                                    {{ $inventario->nombre_producto }}
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Categoría</dt>
                                        <dd class="mt-1">
                                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                                {{ $inventario->categoria }}
                                            </span>
                                        </dd>
                                    </div>
                                    
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unidad de Medida</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                                            {{ $inventario->medida }}                                         
                                            
                                        </dd>
                                        {{-- economico --}}

                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Economico</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                                            {{ $inventario->economico ?? 'N/A' }}
       
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Stock -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Estado del Stock
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Existencia actual -->
                        <div class="text-center">
                            <div class="mx-auto w-20 h-20 rounded-full flex items-center justify-center mb-3
                                {{ $inventario->existencia > 10 ? 'bg-green-100 dark:bg-green-900/30' : ($inventario->existencia > 0 ? 'bg-yellow-100 dark:bg-yellow-900/30' : 'bg-red-100 dark:bg-red-900/30') }}">
                                <svg class="w-8 h-8 {{ $inventario->existencia > 10 ? 'text-green-600 dark:text-green-400' : ($inventario->existencia > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Existencia Actual</dt>
                            <dd class="text-3xl font-bold {{ $inventario->existencia > 10 ? 'text-green-600 dark:text-green-400' : ($inventario->existencia > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                {{ $inventario->existencia }}
                            </dd>
                            <dd class="text-sm text-gray-500 dark:text-gray-400">{{ $inventario->medida }}</dd>
                        </div>

                        <!-- Precio unitario promedio -->
                        <div class="text-center">
                            <div class="mx-auto w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Precio Unitario</dt>
                            <dd class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                ${{ number_format($inventario->getPrecioPromedio(), 2) }}
                            </dd>
                            <dd class="text-sm text-gray-500 dark:text-gray-400">por {{ $inventario->medida }}</dd>
                        </div>

                        <!-- Valor total -->
                        <div class="text-center">
                            <div class="mx-auto w-20 h-20 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor Total</dt>
                            <dd class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                ${{ number_format($inventario->precio_total, 2) }}
                            </dd>
                            <dd class="text-sm text-gray-500 dark:text-gray-400">inventario total</dd>
                        </div>
                    </div>

                    <!-- Indicador de estado -->
                    <div class="mt-6 p-4 rounded-lg border
                        {{ $inventario->existencia > 10 ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : ($inventario->existencia > 0 ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800') }} transition-colors duration-200">
                        <div class="flex items-center">
                            @if($inventario->existencia > 10)
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-green-800 dark:text-green-300">Stock disponible</span>
                            @elseif($inventario->existencia > 0)
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Stock bajo - Considerar reabastecimiento</span>
                            @else
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-red-800 dark:text-red-300">Sin stock - Reabastecimiento urgente</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de Movimientos -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Últimos Movimientos
                    </h3>
                    
                    <div class="space-y-4">
                        @if($inventario->entradas && $inventario->entradas->count() > 0)
                            @foreach($inventario->entradas->take(3) as $entrada)
                            <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-300">Entrada</p>
                                        <p class="text-xs text-green-600 dark:text-green-400">{{ $entrada->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-green-800 dark:text-green-300">+{{ $entrada->cantidad }} {{ $inventario->medida }}</p>
                                    <p class="text-xs text-green-600 dark:text-green-400">${{ number_format($entrada->total_con_iva, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        @endif

                        @if($inventario->salidas && $inventario->salidas->count() > 0)
                            @foreach($inventario->salidas->take(3) as $salida)
                            <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H3"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800 dark:text-red-300">Salida</p>
                                        <p class="text-xs text-red-600 dark:text-red-400">{{ $salida->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-red-800 dark:text-red-300">-{{ $salida->cantidad }} {{ $inventario->medida }}</p>
                                    <p class="text-xs text-red-600 dark:text-red-400">${{ number_format($salida->total_con_iva, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        @endif

                        @if((!$inventario->entradas || $inventario->entradas->count() == 0) && (!$inventario->salidas || $inventario->salidas->count() == 0))
                        <div class="text-center py-6">
                            <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No hay movimientos registrados</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Información lateral -->
        <div class="space-y-6">
            
            <!-- Información Básica -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información Básica
                    </h3>
                    
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID del Producto</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                                #{{ str_pad($inventario->id, 6, '0', STR_PAD_LEFT) }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre Completo</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                                {{ $inventario->nombre_producto }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Categoría</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    {{ $inventario->categoria }}
                                </span>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unidad de Medida</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $inventario->medida }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Fechas importantes -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Fechas Importantes
                    </h3>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Creación</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $inventario->created_at ? $inventario->created_at->format('d/m/Y H:i:s') : 'No disponible' }}
                            </dd>
                            @if($inventario->created_at)
                                <dd class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $inventario->created_at->diffForHumans() }}
                                </dd>
                            @endif
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última Actualización</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $inventario->updated_at ? $inventario->updated_at->format('d/m/Y H:i:s') : 'No disponible' }}
                            </dd>
                            @if($inventario->updated_at)
                                <dd class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $inventario->updated_at->diffForHumans() }}
                                </dd>
                            @endif
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Estadísticas
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Entradas:</span>
                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                {{ $inventario->entradas ? $inventario->entradas->count() : 0 }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Salidas:</span>
                            <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                                {{ $inventario->salidas ? $inventario->salidas->count() : 0 }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-600">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Movimientos Totales:</span>
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                {{ ($inventario->entradas ? $inventario->entradas->count() : 0) + ($inventario->salidas ? $inventario->salidas->count() : 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection