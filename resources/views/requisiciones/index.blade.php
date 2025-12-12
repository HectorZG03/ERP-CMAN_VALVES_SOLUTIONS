@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Requisiciones de Compra</h1>
            <!-- ✅ Mostrar mensaje según el rol -->
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

    <!-- Mostrar filtros si es personal autorizado -->
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
                
                <!-- ✅ Filtros ESPECIALES para FINANZAS -->
                @if(auth()->user()->canApproveFinanzas())
                <div class="flex gap-2">
                    <!-- Filtro Todas -->
                    <button type="button" data-finanzas-status="all" 
                            class="finanzas-filter active px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                        <span>Todas</span>
                        <span class="status-count ml-1" id="count-finanzas-all">{{ $requisiciones->total() }}</span>
                    </button>

                    <!-- Filtro Pendientes Finanzas -->
                    <button type="button" data-finanzas-status="pendiente" 
                            class="finanzas-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:text-blue-800 dark:hover:text-blue-300">
                        <span>⏳ Pendientes</span>
                        <span class="status-count ml-1" id="count-finanzas-pendiente">0</span>
                    </button>

                    <!-- Filtro Aprobadas Finanzas -->
                    <button type="button" data-finanzas-status="aprobado" 
                            class="finanzas-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-green-100 dark:hover:bg-green-900/30 hover:text-green-800 dark:hover:text-green-300">
                        <span>✓ Aprobadas</span>
                        <span class="status-count ml-1" id="count-finanzas-aprobado">0</span>
                    </button>

                    <!-- Filtro Denegadas Finanzas -->
                    <button type="button" data-finanzas-status="denegado" 
                            class="finanzas-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-800 dark:hover:text-red-300">
                        <span>✗ Denegadas</span>
                        <span class="status-count ml-1" id="count-finanzas-denegado">0</span>
                    </button>
                </div>
                @else
                <!-- Filtros normales para otros roles -->
                <div class="flex gap-2">
                    <!-- Filtro Todos (solo para almacén) -->
                    @if(auth()->user()->canManageInventory())
                    <button type="button" data-status="all" 
                            class="status-filter active px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                        <span>Todas</span>
                        <span class="status-count ml-1" id="count-all">{{ $requisiciones->total() }}</span>
                    </button>
                    @endif

                    <!-- Filtro Pendientes -->
                    <button type="button" data-status="pendiente" 
                            class="status-filter {{ !auth()->user()->canManageInventory() ? 'active bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 hover:text-yellow-800 dark:hover:text-yellow-300">
                        <span>Pendientes</span>
                        <span class="status-count ml-1" id="count-pendiente">0</span>
                    </button>

                    <!-- Filtro Aprobados -->
                    <button type="button" data-status="aprobado" 
                            class="status-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-green-100 dark:hover:bg-green-900/30 hover:text-green-800 dark:hover:text-green-300">
                        <span>Aprobados</span>
                        <span class="status-count ml-1" id="count-aprobado">0</span>
                    </button>

                    <!-- Filtro Denegados -->
                    <button type="button" data-status="denegado" 
                            class="status-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-800 dark:hover:text-red-300">
                        <span>Denegados</span>
                        <span class="status-count ml-1" id="count-denegado">0</span>
                    </button>
                </div>
                @endif
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    • Total visible: <span id="total-visible" class="font-medium">{{ $requisiciones->total() }}</span> requisiciones
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Folio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Materiales
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Solicitante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Departamento
                            </th>
                            {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tipo
                            </th> --}}
                            <!-- ✅ NUEVA COLUMNA: Estado Finanzas -->
                            @if(auth()->user()->canManageInventory() || auth()->user()->canApproveRequests())
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Finanzas
                            </th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Estatus
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($requisiciones as $requisicion)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" 
                            data-status="{{ $requisicion->estatus }}"
                            data-estatus-finanzas="{{ $requisicion->estatus_finanzas }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                {{ $requisicion->folio ?? '#' . str_pad($requisicion->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if($requisicion->created_at)
                                    {{ $requisicion->created_at->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $requisicion->total_materiales }} materiales
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-medium">{{ $requisicion->total_unidades }}</span> unidades
                                </div>
                            </td>
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
                                            {{ $requisicion->nombre_solicitante }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $requisicion->departamento }}
                            </td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $requisicion->tipo_requerimiento === 'interno' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' }}">
                                    @if($requisicion->tipo_requerimiento === 'interno')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0v-.5A1.5 1.5 0 0114.5 6c.526 0 .988-.27 1.256-.679a6.012 6.012 0 011.912 2.706A8.012 8.012 0 0110 16a8.012 8.012 0 01-7.668-8.027z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    {{ ucfirst($requisicion->tipo_requerimiento) }}
                                </span>
                            </td> --}}
                            
                            <!-- ✅ NUEVA COLUMNA: Estado Finanzas -->
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- Botón Ver -->
                                    <a href="{{ route('requisiciones.show', $requisicion) }}" 
                                       class="inline-flex items-center px-2 py-1 bg-purple-100 hover:bg-purple-200 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 text-purple-800 dark:text-purple-300 text-xs font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Ver
                                    </a>

                                    <!-- ✅ Botones para FINANZAS (solo si está pendiente) -->
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
                                        <!-- Mostrar estado actual si ya fue procesada -->
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md
                                            {{ $requisicion->estatus_finanzas === 'aprobado' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                            {{ $requisicion->estatus_finanzas === 'aprobado' ? '✓ Ya aprobada' : '✗ Ya denegada' }}
                                        </span>
                                    @endif

                                    <!-- ✅ Botones para DIRECCIÓN (solo si finanzas aprobó) -->
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

                                    <!-- Excel export -->
                                    <a href="{{ route('requisiciones.exportExcel', $requisicion) }}" 
                                       class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300 text-xs font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0014.414 6L10 1.586A2 2 0 008.586 1H6zM13 8V3.5L17.5 8H13z"/>
                                        </svg>
                                        Excel
                                    </a>
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
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">No se encontraron requisiciones de compra.</p>
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

            <div class="mt-4">
                {{ $requisiciones->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
.table-hover-row:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.status-badge {
    transition: all 0.2s ease-in-out;
}

.status-badge:hover {
    transform: scale(1.05);
}

.action-button {
    transition: all 0.2s ease-in-out;
}

.action-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.status-filter.active {
    transform: scale(1.02);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.status-filter:not(.active):hover {
    transform: translateY(-1px);
}

/* Mejora para modo oscuro */
@media (prefers-color-scheme: dark) {
    .table-hover-row:hover {
        box-shadow: 0 4px 6px -1px rgba(255, 255, 255, 0.1);
    }
    
    .action-button:hover {
        box-shadow: 0 2px 4px rgba(255, 255, 255, 0.1);
    }

    .status-filter.active {
        box-shadow: 0 4px 6px -1px rgba(255, 255, 255, 0.1);
    }
}

.fade-out {
    opacity: 0.3;
    transition: opacity 0.3s ease;
}
</style>

<!-- Script para búsqueda y filtros -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const statusFilters = document.querySelectorAll('.status-filter');
    const finanzasFilters = document.querySelectorAll('.finanzas-filter');
    const tableRows = document.querySelectorAll('tbody tr[data-status]');
    const noResultsRow = document.getElementById('no-results-row');
    
    const isFinanzas = {{ auth()->user()->canApproveFinanzas() ? 'true' : 'false' }};
    const isAlmacen = {{ auth()->user()->canManageInventory() ? 'true' : 'false' }};
    
    let currentStatusFilter = isAlmacen ? 'all' : 'pendiente';
    let currentFinanzasFilter = 'all';
    let currentSearchTerm = '';

    // Contar requisiciones por estado al cargar la página
    function updateStatusCounts() {
        if (isFinanzas) {
            // Contadores para finanzas
            const counts = {
                pendiente: 0,
                aprobado: 0,
                denegado: 0,
                all: tableRows.length
            };

            tableRows.forEach(row => {
                const estatusFinanzas = row.getAttribute('data-estatus-finanzas');
                if (counts.hasOwnProperty(estatusFinanzas)) {
                    counts[estatusFinanzas]++;
                }
            });

            // Actualizar contadores en la interfaz
            Object.keys(counts).forEach(status => {
                const countElement = document.getElementById(`count-finanzas-${status}`);
                if (countElement) {
                    countElement.textContent = counts[status];
                }
            });
        } else {
            // Contadores normales para otros roles
            const counts = {
                pendiente: 0,
                aprobado: 0,
                denegado: 0,
                all: tableRows.length
            };

            tableRows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (counts.hasOwnProperty(status)) {
                    counts[status]++;
                }
            });

            // Actualizar contadores en la interfaz
            Object.keys(counts).forEach(status => {
                const countElement = document.getElementById(`count-${status}`);
                if (countElement) {
                    countElement.textContent = counts[status];
                }
            });
        }
    }

    // Función para aplicar filtros
    function applyFilters() {
        let visibleCount = 0;
        let hasVisibleRows = false;

        tableRows.forEach(row => {
            const status = row.getAttribute('data-status');
            const estatusFinanzas = row.getAttribute('data-estatus-finanzas');
            const text = row.textContent.toLowerCase();
            
            let statusMatch = true;
            
            // Determinar si coincide con el filtro según el rol
            if (isFinanzas) {
                statusMatch = currentFinanzasFilter === 'all' || estatusFinanzas === currentFinanzasFilter;
            } else {
                statusMatch = currentStatusFilter === 'all' || status === currentStatusFilter;
            }
            
            const searchMatch = currentSearchTerm === '' || text.includes(currentSearchTerm);
            
            if (statusMatch && searchMatch) {
                row.style.display = '';
                row.classList.remove('fade-out');
                visibleCount++;
                hasVisibleRows = true;
            } else {
                row.style.display = 'none';
                row.classList.add('fade-out');
            }
        });

        // Mostrar/ocultar mensaje de "no hay resultados"
        if (noResultsRow) {
            if (!hasVisibleRows && tableRows.length > 0) {
                noResultsRow.style.display = '';
                noResultsRow.querySelector('h3').textContent = 'No se encontraron requisiciones';
                noResultsRow.querySelector('p').textContent = currentSearchTerm 
                    ? `No hay requisiciones que coincidan con "${currentSearchTerm}" y el filtro seleccionado.`
                    : `No hay requisiciones con el estado seleccionado.`;
            } else {
                noResultsRow.style.display = 'none';
            }
        }

        // Actualizar contador total visible
        const totalVisibleElement = document.getElementById('total-visible');
        if (totalVisibleElement) {
            totalVisibleElement.textContent = visibleCount;
        }
    }

    // Event listener para búsqueda
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            currentSearchTerm = this.value.toLowerCase();
            applyFilters();
        });
    }

    // Event listeners para filtros de FINANZAS
    finanzasFilters.forEach(filter => {
        filter.addEventListener('click', function () {
            // Remover clase active de todos los filtros
            finanzasFilters.forEach(f => {
                f.classList.remove('active');
                f.classList.remove('bg-purple-100', 'dark:bg-purple-900/30', 'text-purple-800', 'dark:text-purple-300');
                f.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });

            // Agregar clase active al filtro seleccionado
            this.classList.add('active');
            this.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            this.classList.add('bg-purple-100', 'dark:bg-purple-900/30', 'text-purple-800', 'dark:text-purple-300');

            // Actualizar filtro actual
            currentFinanzasFilter = this.getAttribute('data-finanzas-status');

            // Aplicar filtros
            applyFilters();
        });
    });

    // Event listeners para filtros de estado normales
    statusFilters.forEach(filter => {
        filter.addEventListener('click', function () {
            // Remover clase active de todos los filtros
            statusFilters.forEach(f => {
                f.classList.remove('active');
                f.classList.remove('bg-purple-100', 'dark:bg-purple-900/30', 'text-purple-800', 'dark:text-purple-300');
                f.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });

            // Agregar clase active al filtro seleccionado
            this.classList.add('active');
            this.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            this.classList.add('bg-purple-100', 'dark:bg-purple-900/30', 'text-purple-800', 'dark:text-purple-300');

            // Actualizar filtro actual
            currentStatusFilter = this.getAttribute('data-status');

            // Aplicar filtros
            applyFilters();
        });
    });

    // Inicializar contadores y aplicar filtros
    updateStatusCounts();
    applyFilters();
});
</script>

@endsection