@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header con navegación -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('prestamos.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Préstamos
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Préstamo #{{ str_pad($prestamo->id, 5, '0', STR_PAD_LEFT) }}
        </h1>
        
        <!-- Estado del préstamo -->
        <div class="ml-auto flex items-center space-x-3">
            @if($prestamo->estatus === 'pendiente')
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Pendiente de Aprobación
                </span>
            @elseif($prestamo->estatus === 'prestado')
                @if($prestamo->esta_vencido)
                    <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 animate-pulse">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        VENCIDO ({{ $prestamo->dias_vencido }} días)
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd"/>
                        </svg>
                        Material Prestado
                    </span>
                @endif
            @elseif($prestamo->estatus === 'devuelto')
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Material Devuelto
                </span>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Préstamo Denegado
                </span>
            @endif
            
            <!-- Botón de devolución si aplica -->
            @if(auth()->user()->canManageInventory() && $prestamo->estatus === 'prestado')
                <a href="{{ route('prestamos.devolucion', $prestamo) }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800 text-white text-sm font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                    Procesar Devolución
                </a>
            @endif
        </div>










        <a href="{{ route('prestamos.exportExcel', $prestamo) }}"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
            Exportar con Plantilla Excel
        </a>





    </div>

    <!-- Resumen del préstamo -->
    @if($prestamo->detalles && $prestamo->detalles->count() > 0)
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800 transition-colors duration-200">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen del Préstamo</h3>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $prestamo->total_productos }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Productos Diferentes</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $prestamo->total_unidades_prestadas }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Unidades Prestadas</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $prestamo->total_unidades_devueltas }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Unidades Devueltas</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $prestamo->dias_prestados }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Días Prestado</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                    ${{ number_format($prestamo->detalles->sum('subtotal'), 2) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Valor Total</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Contenido principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Productos prestados -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Productos Prestados
                    </h3>
                    
                    @if($prestamo->detalles && $prestamo->detalles->count() > 0)
                        <div class="space-y-4">
                            @foreach($prestamo->detalles as $index => $detalle)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 transition-colors duration-200">
                                <div class="flex items-start space-x-4">
                                    <!-- Ícono del producto -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 {{ $detalle->esta_completo ? 'bg-green-500 dark:bg-green-600' : 'bg-blue-500 dark:bg-blue-600' }} rounded-lg flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                {{ $index + 1 }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Información del producto -->
                                    <div class="flex-1">
                                        @if($detalle->inventario)
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $detalle->inventario->nombre_producto }}
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $detalle->inventario->categoria }}
                                            </p>
                                            
                                            <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prestado</dt>
                                                    <dd class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                        {{ $detalle->cantidad_prestada }}
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Devuelto</dt>
                                                    <dd class="text-lg font-bold {{ $detalle->esta_completo ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}">
                                                        {{ $detalle->cantidad_devuelta }}
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pendiente</dt>
                                                    <dd class="text-lg font-bold {{ $detalle->cantidad_pendiente > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                        {{ $detalle->cantidad_pendiente }}
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unidad</dt>
                                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $detalle->inventario->medida }}
                                                    </dd>
                                                </div>
                                            </div>
                                            
                                            <!-- Estado de devolución -->
                                            <div class="mt-3 flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    @if($detalle->estado_devolucion === 'prestado')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M11 3a1 1 0 10-2 0v8a1 1 0 102 0V3z"/>
                                                                <path d="M15.657 5.757a1 1 0 00-1.414-1.414l-5.657 5.657a1 1 0 001.414 1.414l5.657-5.657z"/>
                                                            </svg>
                                                            Prestado
                                                        </span>
                                                    @elseif($detalle->estado_devolucion === 'devuelto_completo')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Devuelto Completo
                                                        </span>
                                                    @elseif($detalle->estado_devolucion === 'devuelto_parcial')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Devuelto Parcial
                                                        </span>
                                                    @endif
                                                    
                                                    @if($detalle->condicion_devolucion)
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            Condición: <strong>{{ ucfirst($detalle->condicion_devolucion) }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <!-- Valor -->
                                                @if($detalle->precio_unitario)
                                                <div class="text-right">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">Valor</div>
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                        ${{ number_format($detalle->subtotal, 2) }}
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-red-500 dark:text-red-400">
                                                <h4 class="text-lg font-semibold">Producto no disponible</h4>
                                                <p class="text-sm">Este producto ya no existe en el inventario</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="font-medium">No hay productos en este préstamo</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comentarios y observaciones -->
            @if($prestamo->comentario || $prestamo->observaciones_devolucion)
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    @if($prestamo->comentario)
                    <div class="mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-3">
                            Motivo del Préstamo
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 transition-colors duration-200">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $prestamo->comentario }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($prestamo->observaciones_devolucion)
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-3">
                            Observaciones de Devolución
                        </h3>
                        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4 border border-amber-200 dark:border-amber-800 transition-colors duration-200">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $prestamo->observaciones_devolucion }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Acciones para aprobar/denegar -->
            @if(auth()->user()->canApproveRequests() && $prestamo->estatus === 'pendiente')
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Acciones de Aprobación
                    </h3>
                    
                    <!-- Validación de stock antes de mostrar botones -->
                    @php
                        $stockSuficiente = true;
                        $productosProblema = [];
                        
                        if($prestamo->detalles) {
                            foreach($prestamo->detalles as $detalle) {
                                if(!$detalle->inventario || $detalle->inventario->existencia < $detalle->cantidad_prestada) {
                                    $stockSuficiente = false;
                                    $productosProblema[] = $detalle->inventario->nombre_producto ?? 'Producto eliminado';
                                }
                            }
                        }
                    @endphp
                    
                    @if(!$stockSuficiente)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4 transition-colors duration-200">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400 dark:text-red-300 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-red-800 dark:text-red-300">Stock Insuficiente</h4>
                                <p class="text-sm text-red-700 dark:text-red-400 mt-1">
                                    Los siguientes productos no tienen stock suficiente: 
                                    <strong>{{ implode(', ', $productosProblema) }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex space-x-4">
                        <form method="POST" action="{{ route('prestamos.updateEstatus', $prestamo) }}" class="flex-1">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="estatus" value="aprobado">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white transition-colors duration-200 {{ $stockSuficiente ? 'bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 focus:ring-green-500' : 'bg-gray-400 dark:bg-gray-600 cursor-not-allowed' }}"
                                    {{ !$stockSuficiente ? 'disabled' : '' }}
                                    @if($stockSuficiente) onclick="return confirm('¿Aprobar este préstamo?\n\nEsto descontará {{ $prestamo->total_unidades_prestadas }} unidades de {{ $prestamo->total_productos }} productos del inventario.')" @endif>
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $stockSuficiente ? 'Aprobar Préstamo' : 'Stock Insuficiente' }}
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('prestamos.updateEstatus', $prestamo) }}" class="flex-1">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="estatus" value="denegado">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                    onclick="return confirm('¿Denegar este préstamo?')">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Denegar Préstamo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Información lateral -->
        <div class="space-y-6">
            <!-- Información del solicitante -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información del Solicitante
                    </h3>
                    
                    <div class="text-center">
                        <div class="mx-auto h-20 w-20 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center">
                            <span class="text-2xl font-medium text-white">
                                {{ substr($prestamo->user->name, 0, 1) }}
                            </span>
                        </div>
                        <h4 class="mt-3 text-lg font-medium text-gray-900 dark:text-white">
                            {{ $prestamo->user->name }}
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $prestamo->user->email }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ ucfirst(str_replace('_', ' ', $prestamo->user->role)) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información de fechas -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información de Fechas
                    </h3>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Préstamo</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $prestamo->fecha_prestamo->format('d/m/Y') }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Devolución Esperada</dt>
                            <dd class="text-sm {{ $prestamo->esta_vencido ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-900 dark:text-white' }}">
                                {{ $prestamo->fecha_devolucion_esperada->format('d/m/Y') }}
                            </dd>
                            @if($prestamo->esta_vencido)
                            <dd class="text-xs text-red-600 dark:text-red-400">
                                Vencido hace {{ $prestamo->dias_vencido }} días
                            </dd>
                            @endif
                        </div>
                        
                        @if($prestamo->fecha_devolucion_real)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Devolución Real</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $prestamo->fecha_devolucion_real->format('d/m/Y') }}
                            </dd>
                        </div>
                        @endif
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Creado</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                {{ $prestamo->created_at->format('d/m/Y H:i') }}
                            </dd>
                            <dd class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $prestamo->created_at->diffForHumans() }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Destino y seguimiento -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Destino y Seguimiento
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 transition-colors duration-200">
                            <div class="text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Destino</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $prestamo->destino }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 transition-colors duration-200">
                            <div class="text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">ID de Préstamo</p>
                                <p class="text-xl font-mono font-bold text-gray-900 dark:text-white">
                                    #{{ str_pad($prestamo->id, 6, '0', STR_PAD_LEFT) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection