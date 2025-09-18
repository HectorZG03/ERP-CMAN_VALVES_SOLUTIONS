@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header con navegación -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('requisiciones.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Requisiciones
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900">
            Requisición de Compra #{{ $requisicion->id }}
        </h1>
        
        <!-- Estado de la requisición -->
        <div class="ml-auto">
            @if($requisicion->estatus === 'pendiente')
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Pendiente de Aprobación
                </span>
            @elseif($requisicion->estatus === 'aprobado')
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-green-100 text-green-800">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Requisición Aprobada
                </span>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full bg-red-100 text-red-800">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Requisición Denegada
                </span>
            @endif
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Detalles del material -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Material Solicitado
                    </h3>
                    
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-purple-500 rounded-lg flex items-center justify-center">
                                    @if($requisicion->tipo_requerimiento === 'interno')
                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0v-.5A1.5 1.5 0 0114.5 6c.526 0 .988-.27 1.256-.679a6.012 6.012 0 011.912 2.706A8.012 8.012 0 0110 16a8.012 8.012 0 01-7.668-8.027z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold text-gray-900">
                                    {{ $requisicion->material }}
                                </h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    Tipo: {{ ucfirst($requisicion->tipo_requerimiento) }}
                                </p>
                                
                                <div class="mt-4">
                                    <dt class="text-sm font-medium text-gray-500">Cantidad Solicitada</dt>
                                    <dd class="text-2xl font-bold text-purple-600">
                                        {{ $requisicion->cantidad }} {{ $requisicion->unidad }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de ubicación -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Información de Ubicación
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Plataforma</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $requisicion->plataforma }}</dd>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Embarcación</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $requisicion->embarcacion }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comentarios -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Comentarios y Justificación
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $requisicion->comentario }}</p>
                    </div>
                </div>
            </div>

            <!-- Acciones para aprobar/denegar -->
            @if(auth()->user()->canApproveRequests() && $requisicion->estatus === 'pendiente')
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Acciones de Aprobación
                    </h3>
                    
                    <div class="flex space-x-4">
                        <form method="POST" action="{{ route('requisiciones.updateEstatus', $requisicion) }}" class="flex-1">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="estatus" value="aprobado">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200" 
                                    onclick="return confirm('¿Aprobar esta requisición?')">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Aprobar Requisición
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('requisiciones.updateEstatus', $requisicion) }}" class="flex-1">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="estatus" value="denegado">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                    onclick="return confirm('¿Denegar esta requisición?')">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Denegar Requisición
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
                        <div class="mx-auto h-20 w-20 rounded-full bg-purple-500 flex items-center justify-center">
                            <span class="text-2xl font-medium text-white">
                                {{ substr($requisicion->nombre_solicitante, 0, 1) }}
                            </span>
                        </div>
                        <h4 class="mt-3 text-lg font-medium text-gray-900">
                            {{ $requisicion->nombre_solicitante }}
                        </h4>
                        <p class="text-sm text-gray-500">
                            {{ $requisicion->departamento }}
                        </p>
                        @if($requisicion->user)
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $requisicion->user->email }}
                            </p>
                        @endif
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
                            <dt class="text-sm font-medium text-gray-500">Fecha de Requisición</dt>
                            <dd class="text-sm text-gray-900 font-medium">
                                @if($requisicion->created_at)
                                    {{ $requisicion->created_at->format('d/m/Y H:i:s') }}
                                @else
                                    <span class="text-gray-400">No disponible</span>
                                @endif
                            </dd>
                            @if($requisicion->created_at)
                                <dd class="text-xs text-gray-500">
                                    {{ $requisicion->created_at->diffForHumans() }}
                                </dd>
                            @endif
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                            <dd class="text-sm text-gray-900 font-medium">
                                @if($requisicion->updated_at)
                                    {{ $requisicion->updated_at->format('d/m/Y H:i:s') }}
                                @else
                                    <span class="text-gray-400">No disponible</span>
                                @endif
                            </dd>
                            @if($requisicion->updated_at)
                                <dd class="text-xs text-gray-500">
                                    {{ $requisicion->updated_at->diffForHumans() }}
                                </dd>
                            @endif
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Tipo de requerimiento -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Tipo de Requerimiento
                    </h3>
                    
                    <div class="text-center">
                        <div class="mx-auto h-16 w-16 rounded-full {{ $requisicion->tipo_requerimiento === 'interno' ? 'bg-blue-100' : 'bg-orange-100' }} flex items-center justify-center">
                            @if($requisicion->tipo_requerimiento === 'interno')
                                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0v-.5A1.5 1.5 0 0114.5 6c.526 0 .988-.27 1.256-.679a6.012 6.012 0 011.912 2.706A8.012 8.012 0 0110 16a8.012 8.012 0 01-7.668-8.027z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                        <h4 class="mt-3 text-lg font-medium {{ $requisicion->tipo_requerimiento === 'interno' ? 'text-blue-900' : 'text-orange-900' }}">
                            {{ ucfirst($requisicion->tipo_requerimiento) }}
                        </h4>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $requisicion->tipo_requerimiento === 'interno' ? 'Material disponible en almacén' : 'Material requiere compra externa' }}
                        </p>
                    </div>
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
                            <p class="text-sm text-gray-500">ID de Requisición</p>
                            <p class="text-2xl font-mono font-bold text-gray-900">
                                #{{ str_pad($requisicion->id, 6, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection