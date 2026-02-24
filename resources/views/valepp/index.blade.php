@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Vales de EPP</h1>
        <a href="{{ route('valepp.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Nuevo Vale EPP
        </a>
    </div>

    <!-- Estadística simple -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total de Vales</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalVales }}</p>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md transition-colors duration-200">
        <div class="px-4 py-5 sm:p-6">
            <!-- Buscador -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="search" placeholder="Buscar por número de vale o colaborador..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vale / Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Colaborador</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Materiales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Entregado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($valepp as $vale)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <!-- Vale / Fecha -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $vale->numero_vale }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $vale->fecha_solicitud->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $vale->fecha_solicitud->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Colaborador -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-emerald-500 dark:bg-emerald-600 flex items-center justify-center">
                                            <span class="text-white font-medium">
                                                {{ substr($vale->personal->nombre_completo, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $vale->personal->nombre_completo }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $vale->personal->area }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Materiales -->
<td class="px-6 py-4">
    <div class="space-y-1">
        @foreach($vale->detalles->take(3) as $index => $detalle)
            <div class="text-sm text-gray-900 dark:text-white flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-500 flex-shrink-0"></span>
                <span class="truncate max-w-[140px]" title="{{ $detalle->inventario->nombre_producto }}">
                    {{ Str::limit($detalle->inventario->nombre_producto, 18) }}
                </span>
                <span class="text-gray-500 dark:text-gray-400 flex-shrink-0">({{ $detalle->cantidad }})</span>
            </div>
        @endforeach

        @if($vale->detalles->count() > 3)
            <div class="text-xs text-blue-500 dark:text-blue-400 font-medium pl-1">
                +{{ $vale->detalles->count() - 3 }} más...
            </div>
        @endif
    </div>
</td>

                            <!-- Fecha de Entrega -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    @if($vale->detalles->first() && $vale->detalles->first()->fecha_entrega)
                                        {{ $vale->detalles->first()->fecha_entrega->format('d/m/Y') }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Acciones -->
<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
    <div class="flex items-center space-x-2">

        <!-- Ver -->
        <a href="{{ route('valepp.show', $vale) }}" 
           class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Ver
        </a>

        <!-- PDF -->
        <a href="{{ route('valepp.exportPDF', $vale) }}" 
           target="_blank"
           class="inline-flex items-center gap-1 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6M9 17h4" />
            </svg>
            PDF
        </a>

        <!-- Excel -->
        <a href="{{ route('valepp.exportExcel', $vale) }}" 
           target="_blank"
           class="inline-flex items-center gap-1 text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6z" />
            </svg>
            Excel
        </a>

    </div>
</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="font-medium">No hay vales registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-6">
                {{ $valepp->links() }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const tableRows = document.querySelectorAll('tbody tr');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase();
            let visibleCount = 0;

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                
                if (searchTerm === '' || text.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection