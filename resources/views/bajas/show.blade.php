@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('bajas.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Bajas
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Baja #{{ str_pad($baja->id, 4, '0', STR_PAD_LEFT) }}
        </h1>
        
        <div class="ml-auto">
            <span class="px-3 py-2 text-sm font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                Baja Registrada
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
                            <div class="w-16 h-16 bg-red-500 dark:bg-red-600 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">
                                    {{ substr($baja->personal->nombre_completo, 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $baja->personal->nombre_completo }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $baja->personal->grado ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Área</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $baja->personal->area }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Departamento</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $baja->personal->departamento }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha Ingreso</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $baja->personal->fecha_ingreso->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sueldo</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($baja->personal->sueldo, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tiempo en empresa</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $baja->personal->fecha_ingreso->diffForHumans($baja->fecha_baja, true) }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motivo de Baja -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Motivo de la Baja
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $baja->motivo_baja }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Lateral -->
        <div class="space-y-6">
            <!-- Información de la Baja -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información de la Baja
                    </h3>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Baja</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $baja->fecha_baja->format('d/m/Y') }}
                            </dd>
                            <dd class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $baja->fecha_baja->diffForHumans() }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado el</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $baja->created_at->format('d/m/Y H:i:s') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado por</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $baja->user->name ?? 'N/A' }}
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
            <a href="{{ route('bajas.edit', $baja) }}" 
               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar Baja
            </a>

            <!-- Botón para PDF individual -->
            <a href="{{ route('bajas.individual-pdf', $baja) }}" 
               target="_blank"
               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Generar PDF de esta Baja
            </a>

            <form method="POST" action="{{ route('bajas.destroy', $baja) }}">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors duration-200"
                        onclick="return confirm('¿Eliminar esta baja? El colaborador volverá a estado activo.')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar Baja
                </button>
            </form>
        </div>
    </div>
</div>




        </div>
    </div>
</div>
@endsection