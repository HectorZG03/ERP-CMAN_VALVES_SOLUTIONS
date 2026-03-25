@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Requisiciones de Compra</h1>
            @if(auth()->user()->canApproveFinanzas())
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Mostrando todas las requisiciones - Puedes filtrar por estado</p>
            @elseif(auth()->user()->canApproveRequests())
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Mostrando requisiciones aprobadas por finanzas</p>
            @endif
        </div>
        <a href="{{ route('requisiciones.create') }}" 
           class="bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Nueva Requisición
        </a>
    </div>

    @if(auth()->user()->canApproveRequests() || auth()->user()->canManageInventory() || auth()->user()->canApproveFinanzas())
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 transition-colors duration-200">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    @if(auth()->user()->canApproveFinanzas())
                        Filtrar por estado en Finanzas:
                    @else
                        Vista:
                    @endif
                </span>

                {{-- ✅ Filtros para FINANZAS (filtran por estatus_finanzas via URL) --}}
                @if(auth()->user()->canApproveFinanzas())
                <div class="flex gap-2">
                    <a href="{{ route('requisiciones.index', ['finanzas' => 'all']) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200
                              {{ $filterFinanzas === 'all' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-purple-100 dark:hover:bg-purple-900/30 hover:text-purple-800 dark:hover:text-purple-300' }}">
                        <span>Todas</span>
                        <span class="ml-1 font-bold">{{ $counts['all'] }}</span>
                    </a>

                    <a href="{{ route('requisiciones.index', ['finanzas' => 'pendiente']) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200
                              {{ $filterFinanzas === 'pendiente' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:text-blue-800 dark:hover:text-blue-300' }}">
                        <span>⏳ Pendientes</span>
                        <span class="ml-1 font-bold">{{ $counts['pendiente'] }}</span>
                    </a>

                    <a href="{{ route('requisiciones.index', ['finanzas' => 'aprobado']) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200
                              {{ $filterFinanzas === 'aprobado' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-green-100 dark:hover:bg-green-900/30 hover:text-green-800 dark:hover:text-green-300' }}">
                        <span>✓ Aprobadas</span>
                        <span class="ml-1 font-bold">{{ $counts['aprobado'] }}</span>
                    </a>

                    <a href="{{ route('requisiciones.index', ['finanzas' => 'denegado']) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200
                              {{ $filterFinanzas === 'denegado' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-800 dark:hover:text-red-300' }}">
                        <span>✗ Denegadas</span>
                        <span class="ml-1 font-bold">{{ $counts['denegado'] }}</span>
                    </a>
                </div>

                @else
                {{-- ✅ Filtros normales para otros roles (filtran por estatus via URL) --}}
                <div class="flex gap-2">
                    @if(auth()->user()->canManageInventory() || auth()->user()->canApproveRequests())
                    <a href="{{ route('requisiciones.index', ['status' => 'all']) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200
                              {{ $filterStatus === 'all' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-purple-100 dark:hover:bg-purple-900/30 hover:text-purple-800 dark:hover:text-purple-300' }}">
                        <span>Todas</span>
                        <span class="ml-1 font-bold">{{ $counts['all'] }}</span>
                    </a>
                    @endif

                    <a href="{{ route('requisiciones.index', ['status' => 'pendiente']) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200
                              {{ $filterStatus === 'pendiente' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 hover:text-yellow-800 dark:hover:text-yellow-300' }}">
                        <span>Pendientes</span>
                        <span class="ml-1 font-bold">{{ $counts['pendiente'] }}</span>
                    </a>

                    <a href="{{ route('requisiciones.index', ['status' => 'aprobado']) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200
                              {{ $filterStatus === 'aprobado' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-green-100 dark:hover:bg-green-900/30 hover:text-green-800 dark:hover:text-green-300' }}">
                        <span>Aprobadas</span>
                        <span class="ml-1 font-bold">{{ $counts['aprobado'] }}</span>
                    </a>

                    <a href="{{ route('requisiciones.index', ['status' => 'denegado']) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200
                              {{ $filterStatus === 'denegado' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-800 dark:hover:text-red-300' }}">
                        <span>Denegadas</span>
                        <span class="ml-1 font-bold">{{ $counts['denegado'] }}</span>
                    </a>
                </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    • Total visible:
                    <span class="font-medium">
                        {{ $isPaginated ? $requisiciones->total() : $requisiciones->count() }}
                    </span> requisiciones
                </span>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md transition-colors duration-200">
        <div class="px-4 py-5 sm:p-6">

            <!-- Barra de búsqueda -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="search" placeholder="Buscar requisiciones por material, solicitante, departamento..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 dark:focus:placeholder-gray-500 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Materiales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Solicitante</th>
                            @if(auth()->user()->canManageInventory() || auth()->user()->canApproveRequests())
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Finanzas</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estatus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($requisiciones as $requisicion)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 searchable-row"
                            data-status="{{ $requisicion->estatus }}"
                            data-estatus-finanzas="{{ $requisicion->estatus_finanzas }}">

                            <!-- Fecha -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if($requisicion->created_at)
                                    {{ $requisicion->created_at->format('d/m/Y') }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                @endif
                            </td>

                            <!-- Resumen materiales -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 mr-1 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $requisicion->total_materiales }}</span>
                                        <span class="text-gray-500 dark:text-gray-400 ml-1">materiales</span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 mr-1 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 6 6 6-6 3 3-9 9-9-9z"/>
                                        </svg>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $requisicion->total_unidades }}</span>
                                        <span class="text-gray-500 dark:text-gray-400 ml-1">unidades</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Solicitante -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-purple-500 dark:bg-purple-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($requisicion->nombre_solicitante, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ strlen($requisicion->nombre_solicitante) > 19 ? substr($requisicion->nombre_solicitante, 0, 19) . '...' : $requisicion->nombre_solicitante }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ ucfirst(str_replace('_', ' ', $requisicion->user->role)) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Columna Finanzas -->
                            @if(auth()->user()->canManageInventory() || auth()->user()->canApproveRequests())
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($requisicion->estatus_finanzas === 'pendiente')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Pendiente
                                    </span>
                                @elseif($requisicion->estatus_finanzas === 'aprobado')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Aprobado
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Denegado
                                    </span>
                                @endif
                            </td>
                            @endif

                            <!-- Estatus general -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($requisicion->estatus === 'pendiente')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Pendiente
                                    </span>
                                @elseif($requisicion->estatus === 'aprobado')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Aprobado
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Denegado
                                    </span>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">

                                    <!-- Ver -->
                                    <a href="{{ route('requisiciones.show', $requisicion) }}" 
                                       class="inline-flex items-center px-2 py-1 bg-purple-100 hover:bg-purple-200 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 text-purple-800 dark:text-purple-300 text-xs font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Ver
                                    </a>

                                    <!-- PDF -->
                                    <a href="{{ route('requisiciones.pdf', $requisicion) }}"
                                        target="_blank"
                                        class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 dark:bg-yellow-900/30 dark:hover:bg-red-900/50 text-red-800 dark:text-red-300 text-xs font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0014.414 6L10 1.586A2 2 0 008.586 1H6zM13 8V3.5L17.5 8H13z"/>
                                        </svg>
                                        PDF
                                    </a>

                                    <!-- Botones FINANZAS -->
                                    @if(auth()->user()->canApproveFinanzas() && $requisicion->estatus_finanzas === 'pendiente')
                                        <form method="POST" action="{{ route('requisiciones.updateEstatusFinanzas', $requisicion) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="estatus_finanzas" value="aprobado">
                                            <button type="submit"
                                                    class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-800 dark:text-green-300 text-xs font-medium rounded-md transition-colors duration-200"
                                                    onclick="return confirm('¿Aprobar esta requisición desde finanzas?')">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Aprobar
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('requisiciones.updateEstatusFinanzas', $requisicion) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="estatus_finanzas" value="denegado">
                                            <button type="submit"
                                                    class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-800 dark:text-red-300 text-xs font-medium rounded-md transition-colors duration-200"
                                                    onclick="return confirm('¿Denegar esta requisición desde finanzas?')">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                                Denegar
                                            </button>
                                        </form>
                                    @elseif(auth()->user()->canApproveFinanzas() && $requisicion->estatus_finanzas !== 'pendiente')
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md
                                            {{ $requisicion->estatus_finanzas === 'aprobado' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                            {{ $requisicion->estatus_finanzas === 'aprobado' ? '✓ Ya aprobada' : '✗ Ya denegada' }}
                                        </span>
                                    @endif

                                    <!-- Botones DIRECCIÓN -->
                                    @if(auth()->user()->canApproveRequests() && $requisicion->estatus_finanzas === 'aprobado' && $requisicion->estatus === 'pendiente')
                                        <form method="POST" action="{{ route('requisiciones.updateEstatus', $requisicion) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="estatus" value="aprobado">
                                            <button type="submit"
                                                    class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-800 dark:text-green-300 text-xs font-medium rounded-md transition-colors duration-200"
                                                    onclick="return confirm('¿Aprobar esta requisición?')">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Aprobar
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('requisiciones.updateEstatus', $requisicion) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="estatus" value="denegado">
                                            <button type="submit"
                                                    class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-800 dark:text-red-300 text-xs font-medium rounded-md transition-colors duration-200"
                                                    onclick="return confirm('¿Denegar esta requisición?')">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                                Denegar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-results-row">
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No hay requisiciones</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">No se encontraron requisiciones con el filtro seleccionado.</p>
                                    <a href="{{ route('requisiciones.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Crear Primera Requisición
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación: solo cuando no hay filtro activo -->
            @if($isPaginated)
            <div class="mt-4">
                {{ $requisiciones->appends(request()->query())->links() }}
            </div>
            @else
            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                Mostrando todos los registros
                @if(auth()->user()->canApproveFinanzas())
                    con estatus finanzas <span class="font-semibold">
                        @if($filterFinanzas === 'pendiente') Pendiente
                        @elseif($filterFinanzas === 'aprobado') Aprobado
                        @elseif($filterFinanzas === 'denegado') Denegado
                        @endif
                    </span>
                @else
                    con estatus <span class="font-semibold">
                        @if($filterStatus === 'pendiente') Pendiente
                        @elseif($filterStatus === 'aprobado') Aprobado
                        @elseif($filterStatus === 'denegado') Denegado
                        @endif
                    </span>
                @endif
                ({{ $requisiciones->count() }} en total)
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.status-filter.active, a.active {
    transform: scale(1.02);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.fade-out {
    opacity: 0.3;
    transition: opacity 0.3s ease;
}
@media (prefers-color-scheme: dark) {
    .status-filter.active, a.active {
        box-shadow: 0 4px 6px -1px rgba(255, 255, 255, 0.1);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const tableRows = document.querySelectorAll('tbody tr.searchable-row');
    const noResultsRow = document.getElementById('no-results-row');

    if (!searchInput) return;

    searchInput.addEventListener('keyup', function () {
        const term = this.value.toLowerCase().trim();
        let visibleCount = 0;

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (term === '' || text.includes(term)) {
                row.style.display = '';
                row.classList.remove('fade-out');
                visibleCount++;
            } else {
                row.style.display = 'none';
                row.classList.add('fade-out');
            }
        });

        if (noResultsRow) {
            noResultsRow.style.display = (visibleCount === 0 && tableRows.length > 0) ? '' : 'none';
        }
    });
});
</script>

@endsection