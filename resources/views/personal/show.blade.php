@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('personal.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Personal
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ $personal->nombre_completo }}
        </h1>
        
        <div class="ml-auto">
            @if($personal->estatus === 'activo')
                <span class="px-3 py-2 text-sm font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                    Activo
                </span>
            @else
                <span class="px-3 py-2 text-sm font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                    Baja
                </span>
            @endif
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
                            <div class="w-16 h-16 bg-blue-500 dark:bg-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">
                                    {{ substr($personal->nombre_completo, 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $personal->nombre_completo }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $personal->grado ?? 'Colaborador' }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Área</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->area }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Departamento</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->departamento }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha Ingreso</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->fecha_ingreso->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sueldo</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($personal->sueldo, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Antigüedad</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $personal->fecha_ingreso->diffForHumans(now(), true) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->id }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de Vales EPP -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Vales de EPP
                        </h3>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                            {{ $personal->valepp->count() }} vale(s)
                        </span>
                    </div>
                    
                    @if($personal->valepp->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-600">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vale</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Materiales</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($personal->valepp as $vale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $vale->numero_vale }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $vale->fecha_solicitud->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        @foreach($vale->detalles as $detalle)
                                            <div>{{ $detalle->inventario->nombre_producto }} ({{ $detalle->cantidad }})</div>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('valepp.show', $vale) }}" 
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No hay vales de EPP registrados</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información Lateral -->
        <div class="space-y-6">
            <!-- Información del Registro -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información del Registro
                    </h3>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                @if($personal->estatus === 'activo')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Baja
                                    </span>
                                @endif
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado el</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $personal->created_at->format('d/m/Y H:i:s') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Actualizado el</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $personal->updated_at->format('d/m/Y H:i:s') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Historial de Cambios -->
            @if($personal->cambiosPuestoSueldo->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Cambios Recientes
                        </h3>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300">
                            {{ $personal->cambiosPuestoSueldo->count() }}
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($personal->cambiosPuestoSueldo->take(3) as $cambio)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <div class="text-xs font-medium text-gray-900 dark:text-white">
                                {{ $cambio->fecha_cambio->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $cambio->puesto_anterior }} → {{ $cambio->puesto_nuevo }}
                            </div>
                        </div>
                        @endforeach
                        
                        @if($personal->cambiosPuestoSueldo->count() > 3)
                        <a href="{{ route('cambios.historial', $personal->id) }}" 
                           class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                            Ver historial completo ({{ $personal->cambiosPuestoSueldo->count() }})
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Historial de Bajas -->
            @if($personal->bajas->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Bajas Registradas
                        </h3>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                            {{ $personal->bajas->count() }}
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($personal->bajas->take(2) as $baja)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <div class="text-xs font-medium text-gray-900 dark:text-white">
                                {{ $baja->fecha_baja->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 truncate">
                                {{ Str::limit($baja->motivo_baja, 50) }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Acciones -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Acciones
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('personal.edit', $personal) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Colaborador
                        </a>

                        @if($personal->estatus === 'activo')
                        <a href="{{ route('bajas.create') }}?personal_id={{ $personal->id }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                            Registrar Baja
                        </a>

                        <a href="{{ route('cambios.create') }}?personal_id={{ $personal->id }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            Registrar Cambio
                        </a>
                        @endif

                        <form method="POST" action="{{ route('personal.destroy', $personal) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors duration-200"
                                    onclick="return confirm('¿Eliminar este colaborador? Esta acción no se puede deshacer.')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar Colaborador
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos para impresión -->
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        font-size: 12px;
        line-height: 1.4;
    }
    
    .bg-white, .bg-gray-50, .bg-gray-100 {
        background-color: white !important;
    }
    
    .text-gray-900, .text-gray-800, .text-gray-700 {
        color: black !important;
    }
    
    .border, .border-gray-200, .border-gray-300 {
        border: 1px solid #ccc !important;
    }
    
    .shadow, .shadow-lg, .shadow-md {
        box-shadow: none !important;
    }
    
    .rounded-lg, .rounded-md {
        border-radius: 0 !important;
    }
    
    .space-y-6 > * + * {
        margin-top: 1rem !important;
    }
    
    .grid {
        display: block !important;
    }
    
    .flex {
        display: flex !important;
    }
    
    .lg\:col-span-2 {
        width: 100% !important;
    }
}
</style>
@endsection