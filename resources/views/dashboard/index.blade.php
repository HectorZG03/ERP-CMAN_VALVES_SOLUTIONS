@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Bienvenido, {{ $user->name }} ({{ ucfirst(str_replace('_', ' ', $user->role)) }})
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 dark:bg-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                                <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Total Inventario
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ number_format($data['totalInventario']) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 dark:bg-yellow-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Solicitudes Pendientes
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $data['solicitudesPendientes'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 dark:bg-green-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Requisiciones Pendientes
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $data['requisicionesPendientes'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 dark:bg-purple-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Usuarios Activos
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ \App\Models\User::count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700 transition-colors duration-200">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($user->canManageInventory())
                <a href="{{ route('entradas.create') }}" 
                   class="bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 p-4 rounded-lg border border-blue-200 dark:border-blue-700 transition-colors group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500 dark:bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-blue-700 dark:text-blue-300 font-semibold">Nueva Entrada</div>
                            <div class="text-blue-600 dark:text-blue-400 text-sm">Registrar entrada de productos</div>
                        </div>
                    </div>
                </a>
                
                <a href="{{ route('salidas.create') }}" 
                   class="bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 p-4 rounded-lg border border-red-200 dark:border-red-700 transition-colors group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-500 dark:bg-red-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-red-700 dark:text-red-300 font-semibold">Nueva Salida</div>
                            <div class="text-red-600 dark:text-red-400 text-sm">Registrar salida de productos</div>
                        </div>
                    </div>
                </a>
            @endif
            
            <a href="{{ route('solicitudes.create') }}" 
               class="bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/40 p-4 rounded-lg border border-yellow-200 dark:border-yellow-700 transition-colors group">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-yellow-500 dark:bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-yellow-700 dark:text-yellow-300 font-semibold">Solicitar Material</div>
                        <div class="text-yellow-600 dark:text-yellow-400 text-sm">Nueva solicitud de material</div>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('requisiciones.create') }}" 
               class="bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/40 p-4 rounded-lg border border-purple-200 dark:border-purple-700 transition-colors group">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-500 dark:bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-purple-700 dark:text-purple-300 font-semibold">Nueva Requisición</div>
                        <div class="text-purple-600 dark:text-purple-400 text-sm">Requisición de material</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Últimas Actividades -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Productos con Bajo Stock -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Productos con Bajo Stock</h3>
            <div class="space-y-3">
                @foreach(\App\Models\Inventario::where('existencia', '<=', 5)->limit(5)->get() as $producto)
                <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-100 dark:border-red-800">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $producto->nombre_producto }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $producto->categoria }}</div>
                    </div>
                    <div class="text-red-600 dark:text-red-400 font-bold">{{ $producto->existencia }}</div>
                </div>
                @endforeach
                
                @if(\App\Models\Inventario::where('existencia', '<=', 5)->count() === 0)
                <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2">Todos los productos tienen stock suficiente</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Últimas Solicitudes -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Últimas Solicitudes</h3>
                <a href="{{ route('solicitudes.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">Ver todas</a>
            </div>
            <div class="space-y-3">
                @php
                    // Usar la nueva estructura de consulta
                    $ultimasSolicitudes = \App\Models\SolicitudMaterial::with(['detalles.inventario', 'user'])
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp
                
                @forelse($ultimasSolicitudes as $solicitud)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-500 dark:bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-medium">#{{ $solicitud->id }}</span>
                        </div>
                        <div>
                            @if($solicitud->detalles && $solicitud->detalles->count() > 0)
                                @if($solicitud->detalles->count() == 1)
                                    {{-- Una sola solicitud --}}
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $solicitud->detalles->first()->inventario->nombre_producto ?? 'Producto no disponible' }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $solicitud->user->name }} - {{ $solicitud->detalles->first()->cantidad_solicitada }} unidades
                                    </div>
                                @else
                                    {{-- Múltiples productos --}}
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        Solicitud múltiple ({{ $solicitud->detalles->count() }} productos)
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $solicitud->user->name }} - {{ $solicitud->total_unidades }} unidades total
                                    </div>
                                @endif
                            @elseif($solicitud->inventario)
                                {{-- Compatibilidad con estructura antigua --}}
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $solicitud->inventario->nombre_producto }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $solicitud->user->name }} - {{ $solicitud->cantidad_solicitada }} unidades
                                </div>
                            @else
                                {{-- Sin productos (solicitud corrupta) --}}
                                <div class="font-medium text-red-500 dark:text-red-400">Solicitud sin productos</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $solicitud->user->name }}</div>
                            @endif
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        @if($solicitud->estatus === 'pendiente') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                        @elseif($solicitud->estatus === 'aprobado') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                        @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @endif">
                        {{ ucfirst($solicitud->estatus) }}
                    </span>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p>No hay solicitudes recientes</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection