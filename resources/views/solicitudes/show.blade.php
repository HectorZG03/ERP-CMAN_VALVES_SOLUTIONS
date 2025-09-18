@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header con navegación -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('solicitudes.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Solicitudes
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900">
            Solicitud #{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}
        </h1>
        
        <!-- Estado de la solicitud -->
        <div class="ml-auto">
            @if($solicitud->estatus === 'pendiente')
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Pendiente de Aprobación
                </span>
            @elseif($solicitud->estatus === 'aprobado')
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-green-100 text-green-800">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Solicitud Aprobada
                </span>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-red-100 text-red-800">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Solicitud Denegada
                </span>
            @endif
        </div>
    </div>

    <!-- Resumen de la solicitud -->
    @if($solicitud->detalles && $solicitud->detalles->count() > 0)
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de la Solicitud</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $solicitud->total_productos }}</div>
                <div class="text-sm text-gray-600">Productos Diferentes</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $solicitud->total_unidades }}</div>
                <div class="text-sm text-gray-600">Total Unidades</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">${{ number_format($solicitud->total, 2) }}</div>
                <div class="text-sm text-gray-600">Valor Estimado</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-600">{{ $solicitud->created_at->format('d/m/Y') }}</div>
                <div class="text-sm text-gray-600">Fecha Solicitud</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Contenido principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Productos solicitados -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Productos Solicitados
                    </h3>
                    
                    @if($solicitud->detalles && $solicitud->detalles->count() > 0)
                        <div class="space-y-4">
                            @foreach($solicitud->detalles as $index => $detalle)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-gray-300 transition-colors">
                                <div class="flex items-start space-x-4">
                                    <!-- Ícono del producto -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                {{ $index + 1 }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Información del producto -->
                                    <div class="flex-1">
                                        @if($detalle->inventario)
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                {{ $detalle->inventario->nombre_producto }}
                                            </h4>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $detalle->inventario->categoria }}
                                            </p>
                                            
                                            <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</dt>
                                                    <dd class="text-lg font-bold text-blue-600">
                                                        {{ $detalle->cantidad_solicitada }}
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</dt>
                                                    <dd class="text-sm font-medium text-gray-900">
                                                        {{ $detalle->inventario->medida }}
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</dt>
                                                    <dd class="text-sm font-semibold {{ $detalle->inventario->existencia >= $detalle->cantidad_solicitada ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $detalle->inventario->existencia }}
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</dt>
                                                    <dd class="text-sm font-medium text-gray-900">
                                                        ${{ number_format($detalle->precio_unitario ?? 0, 2) }}
                                                    </dd>
                                                </div>
                                            </div>
                                            
                                            <!-- Indicador de disponibilidad -->
                                            <div class="mt-3">
                                                @if($detalle->inventario->existencia >= $detalle->cantidad_solicitada)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Stock Disponible
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Stock Insuficiente
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-red-500">
                                                <h4 class="text-lg font-semibold">Producto no disponible</h4>
                                                <p class="text-sm">Este producto ya no existe en el inventario</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Subtotal -->
                                    @if($detalle->inventario && $detalle->precio_unitario)
                                    <div class="flex-shrink-0 text-right">
                                        <div class="text-xs text-gray-500">Subtotal</div>
                                        <div class="text-lg font-bold text-gray-900">
                                            ${{ number_format($detalle->subtotal, 2) }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="font-medium">No hay productos en esta solicitud</p>
                            <p class="text-sm">Los productos pueden haber sido eliminados del inventario</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comentario de la solicitud -->
            @if($solicitud->comentario)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Comentarios de la Solicitud
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $solicitud->comentario }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Acciones para aprobar/denegar -->
            @if(auth()->user()->canApproveRequests() && $solicitud->estatus === 'pendiente')
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Acciones de Aprobación
                    </h3>
                    
                    <!-- Validación de stock antes de mostrar botones -->
                    @php
                        $stockSuficiente = true;
                        $productosProblema = [];
                        
                        if($solicitud->detalles) {
                            foreach($solicitud->detalles as $detalle) {
                                if(!$detalle->inventario || $detalle->inventario->existencia < $detalle->cantidad_solicitada) {
                                    $stockSuficiente = false;
                                    $productosProblema[] = $detalle->inventario->nombre_producto ?? 'Producto eliminado';
                                }
                            }
                        }
                    @endphp
                    
                    @if(!$stockSuficiente)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-red-800">Stock Insuficiente</h4>
                                <p class="text-sm text-red-700 mt-1">
                                    Los siguientes productos no tienen stock suficiente: 
                                    <strong>{{ implode(', ', $productosProblema) }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex space-x-4">
                        <form method="POST" action="{{ route('solicitudes.updateEstatus', $solicitud) }}" class="flex-1">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="estatus" value="aprobado">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white transition-colors duration-200 {{ $stockSuficiente ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-gray-400 cursor-not-allowed' }}"
                                    {{ !$stockSuficiente ? 'disabled' : '' }}
                                    @if($stockSuficiente) onclick="return confirm('¿Aprobar esta solicitud?\n\nEsto descontará {{ $solicitud->total_unidades }} unidades de {{ $solicitud->total_productos }} productos del inventario.')" @endif>
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $stockSuficiente ? 'Aprobar Solicitud' : 'Stock Insuficiente' }}
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('solicitudes.updateEstatus', $solicitud) }}" class="flex-1">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="estatus" value="denegado">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                    onclick="return confirm('¿Denegar esta solicitud?')">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Denegar Solicitud
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
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Información del Solicitante
                    </h3>
                    
                    <div class="text-center">
                        <div class="mx-auto h-20 w-20 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-2xl font-medium text-white">
                                {{ substr($solicitud->user->name, 0, 1) }}
                            </span>
                        </div>
                        <h4 class="mt-3 text-lg font-medium text-gray-900">
                            {{ $solicitud->user->name }}
                        </h4>
                        <p class="text-sm text-gray-500">
                            {{ $solicitud->user->email }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ ucfirst(str_replace('_', ' ', $solicitud->user->role)) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información de fechas -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Información de Fechas
                    </h3>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Solicitud</dt>
                            <dd class="text-sm text-gray-900 font-medium">
                                {{ $solicitud->created_at->format('d/m/Y H:i:s') }}
                            </dd>
                            <dd class="text-xs text-gray-500">
                                {{ $solicitud->created_at->diffForHumans() }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                            <dd class="text-sm text-gray-900 font-medium">
                                {{ $solicitud->updated_at->format('d/m/Y H:i:s') }}
                            </dd>
                            <dd class="text-xs text-gray-500">
                                {{ $solicitud->updated_at->diffForHumans() }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- ID de seguimiento -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Seguimiento
                    </h3>
                    
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-center">
                            <p class="text-sm text-gray-500">ID de Solicitud</p>
                            <p class="text-2xl font-mono font-bold text-gray-900">
                                #{{ str_pad($solicitud->id, 6, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection