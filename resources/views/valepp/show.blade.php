@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('valepp.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Vales EPP
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Vale PP {{ $valepp->numero_vale }}
        </h1>
        
        <div class="ml-auto">
            <span class="px-3 py-2 text-sm font-medium rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300">
                Vale Entregado
            </span>
        </div>

        {{-- descargar excel --}}
        <a href="{{ route('valepp.exportExcel', $valepp->id) }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Descargar Excel
        </a>
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
                            <div class="w-16 h-16 bg-emerald-500 dark:bg-emerald-600 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">
                                    {{ substr($valepp->personal->nombre_completo, 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $valepp->personal->nombre_completo }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $valepp->personal->area }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Área</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $valepp->personal->area }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Departamento</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $valepp->personal->departamento ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha Ingreso</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $valepp->personal->fecha_ingreso->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID Colaborador</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $valepp->personal->id }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materiales Entregados -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Materiales Entregados
                        </h3>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                            {{ $valepp->detalles->count() }} material(es)
                        </span>
                    </div>
                    
                    @if($valepp->detalles->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-600">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Material</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categoría</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha Entrega</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($valepp->detalles as $detalle)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $detalle->inventario->nombre_producto }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            {{ $detalle->inventario->categoria }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $detalle->cantidad }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        @if($detalle->fecha_entrega)
                                            {{ $detalle->fecha_entrega->format('d/m/Y') }}
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100 dark:bg-gray-600">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">
                                        Total:
                                    </td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $valepp->detalles->sum('cantidad') }}
                                    </td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No hay materiales registrados en este vale</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Observaciones -->
            @if($valepp->observaciones)
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Observaciones
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $valepp->observaciones }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Información Lateral -->
        <div class="space-y-6">
            <!-- Información del Vale -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información del Vale
                    </h3>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número de Vale</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $valepp->numero_vale }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Solicitud</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $valepp->fecha_solicitud->format('d/m/Y') }}
                            </dd>
                            <dd class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $valepp->fecha_solicitud->diffForHumans() }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Entrega</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                @if($valepp->detalles->first() && $valepp->detalles->first()->fecha_entrega)
                                    {{ $valepp->detalles->first()->fecha_entrega->format('d/m/Y') }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                @endif
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado el</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $valepp->created_at->format('d/m/Y H:i:s') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado por</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $valepp->user->name ?? 'Sistema' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Materiales</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $valepp->detalles->sum('cantidad') }}
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
                        <a href="{{ route('valepp.exportPDF', $valepp) }}" 
                           target="_blank"
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Generar PDF
                        </a>

                        @if($valepp->estatus === 'pendiente')
                        <a href="{{ route('valepp.edit', $valepp) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Vale
                        </a>

                        <form method="POST" action="{{ route('valepp.destroy', $valepp) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors duration-200"
                                    onclick="return confirm('¿Eliminar este vale? Esta acción no se puede deshacer.')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar Vale
                            </button>
                        </form>
                        @endif

                        <button onclick="window.print()" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Imprimir
                        </button>
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