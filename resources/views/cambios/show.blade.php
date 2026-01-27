@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('cambios.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Cambios
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Cambio #{{ str_pad($cambio->id, 4, '0', STR_PAD_LEFT) }}
        </h1>
        
        <div class="ml-auto">
            <span class="px-3 py-2 text-sm font-medium rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300">
                Cambio Registrado
            </span>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información del Colaborador -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información del Colaborador
                    </h3>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-16 h-16 bg-pink-500 dark:bg-pink-600 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">
                                    {{ substr($cambio->personal->nombre_completo, 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $cambio->personal->nombre_completo }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $cambio->personal->area }} - {{ $cambio->personal->departamento }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparativa de Cambios -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Comparativa de Cambios
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cambio de Puesto -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3 uppercase">Puesto</h4>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Anterior</p>
                                    <p class="text-lg text-gray-600 dark:text-gray-300 line-through">
                                        {{ $cambio->puesto_anterior }}
                                    </p>
                                </div>
                                
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                                
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nuevo</p>
                                    <p class="text-xl font-bold text-green-600 dark:text-green-400">
                                        {{ $cambio->puesto_nuevo }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Cambio de Sueldo -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3 uppercase">Sueldo</h4>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Anterior</p>
                                    <p class="text-lg text-gray-600 dark:text-gray-300 line-through">
                                        ${{ number_format($cambio->sueldo_anterior, 2) }}
                                    </p>
                                </div>
                                
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nuevo</p>
                                    <p class="text-xl font-bold text-green-600 dark:text-green-400">
                                        ${{ number_format($cambio->sueldo_nuevo, 2) }}
                                    </p>
                                    @php
                                        $diferencia = $cambio->sueldo_nuevo - $cambio->sueldo_anterior;
                                        $porcentaje = $cambio->sueldo_anterior > 0 ? ($diferencia / $cambio->sueldo_anterior * 100) : 0;
                                    @endphp
                                    <p class="text-sm {{ $diferencia > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                                        {{ $diferencia > 0 ? '+' : '' }}${{ number_format(abs($diferencia), 2) }}
                                        ({{ $diferencia > 0 ? '+' : '' }}{{ number_format($porcentaje, 1) }}%)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Observaciones -->
            @if($cambio->observaciones)
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Observaciones
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $cambio->observaciones }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Información Lateral -->
        <div class="space-y-6">
            <!-- Información del Cambio -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información del Cambio
                    </h3>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha del Cambio</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $cambio->fecha_cambio->format('d/m/Y') }}
                            </dd>
                            <dd class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $cambio->fecha_cambio->diffForHumans() }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado el</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $cambio->created_at->format('d/m/Y H:i:s') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado por</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $cambio->user->name ?? 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Acciones -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Acciones
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('cambios.edit', $cambio) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Cambio
                        </a>

                        <a href="{{ route('cambios.historial', $cambio->personal_id) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ver Historial Completo
                        </a>

                        <form method="POST" action="{{ route('cambios.destroy', $cambio) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors duration-200"
                                    onclick="return confirm('¿Eliminar este registro de cambio?')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar Cambio
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection