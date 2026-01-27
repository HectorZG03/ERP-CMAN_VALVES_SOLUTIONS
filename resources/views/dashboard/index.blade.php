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
        <!-- Total Inventario -->
        <a href="{{ route('inventario.index') }}" 
           class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200 hover:shadow-md cursor-pointer group">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 dark:bg-blue-600 rounded-full flex items-center justify-center group-hover:bg-blue-600 dark:group-hover:bg-blue-500 transition-colors">
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
        </a>

        <!-- Solicitudes Pendientes -->
        <a href="{{ route('solicitudes.index', ['estatus' => 'pendiente']) }}" 
           class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200 hover:shadow-md cursor-pointer group">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 dark:bg-yellow-600 rounded-full flex items-center justify-center group-hover:bg-yellow-600 dark:group-hover:bg-yellow-500 transition-colors">
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
        </a>

        <!-- Requisiciones Pendientes -->
        <a href="{{ route('requisiciones.index', ['estatus' => 'pendiente']) }}" 
           class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200 hover:shadow-md cursor-pointer group">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 dark:bg-green-600 rounded-full flex items-center justify-center group-hover:bg-green-600 dark:group-hover:bg-green-500 transition-colors">
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
        </a>

        <!-- Usuarios Activos -->
        <a href="{{ route('users.index') }}" 
           class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200 hover:shadow-md cursor-pointer group">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 dark:bg-purple-600 rounded-full flex items-center justify-center group-hover:bg-purple-600 dark:group-hover:bg-purple-500 transition-colors">
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
        </a>
    </div>

    <!-- Acciones Rápidas -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700 transition-colors duration-200">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- ALMACÉN --}}
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
            
            {{-- PARA TODOS --}}
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

            {{-- ✅ RECURSOS HUMANOS --}}
            @if($user->canManagePersonal())
                <a href="{{ route('personal.create') }}" 
                   class="bg-cyan-50 dark:bg-cyan-900/20 hover:bg-cyan-100 dark:hover:bg-cyan-900/40 p-4 rounded-lg border border-cyan-200 dark:border-cyan-700 transition-colors group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-cyan-500 dark:bg-cyan-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-cyan-700 dark:text-cyan-300 font-semibold">Alta Personal</div>
                            <div class="text-cyan-600 dark:text-cyan-400 text-sm">Nuevo colaborador</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('bajas.create') }}" 
                   class="bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/40 p-4 rounded-lg border border-orange-200 dark:border-orange-700 transition-colors group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-500 dark:bg-orange-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-orange-700 dark:text-orange-300 font-semibold">Baja Personal</div>
                            <div class="text-orange-600 dark:text-orange-400 text-sm">Registrar baja</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('cambios.create') }}" 
                   class="bg-pink-50 dark:bg-pink-900/20 hover:bg-pink-100 dark:hover:bg-pink-900/40 p-4 rounded-lg border border-pink-200 dark:border-pink-700 transition-colors group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-pink-500 dark:bg-pink-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-pink-700 dark:text-pink-300 font-semibold">Cambio Puesto</div>
                            <div class="text-pink-600 dark:text-pink-400 text-sm">Cambiar puesto/sueldo</div>
                        </div>
                    </div>
                </a>
            @endif

            {{-- ✅ HSE --}}
            @if($user->canManageValeEPP())
                <a href="{{ route('valepp.create') }}" 
                   class="bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 p-4 rounded-lg border border-emerald-200 dark:border-emerald-700 transition-colors group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-emerald-500 dark:bg-emerald-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-emerald-700 dark:text-emerald-300 font-semibold">Nuevo Vale EPP</div>
                            <div class="text-emerald-600 dark:text-emerald-400 text-sm">Equipo de protección</div>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </div>

    <!-- Últimas Actividades -->
    @if($user->canManageInventory())
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Productos con Bajo Stock -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Productos con Bajo Stock</h3>
                <a href="{{ route('inventario.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">Ver inventario</a>
            </div>
            <div class="space-y-3">
                @foreach(\App\Models\Inventario::where('existencia', '<=', 5)->limit(5)->get() as $producto)
                <a href="{{ route('inventario.show', $producto) }}" class="block">
                    <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-100 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $producto->nombre_producto }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $producto->categoria }}</div>
                        </div>
                        <div class="text-red-600 dark:text-red-400 font-bold">{{ $producto->existencia }}</div>
                    </div>
                </a>
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
                    $ultimasSolicitudes = \App\Models\SolicitudMaterial::with(['detalles.inventario', 'user'])
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp
                
                @forelse($ultimasSolicitudes as $solicitud)
                <a href="{{ route('solicitudes.show', $solicitud) }}" class="block">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-500 dark:bg-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xs font-medium">#{{ $solicitud->id }}</span>
                            </div>
                            <div>
                                @if($solicitud->detalles && $solicitud->detalles->count() > 0)
                                    @if($solicitud->detalles->count() == 1)
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $solicitud->detalles->first()->inventario->nombre_producto ?? 'Producto no disponible' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $solicitud->user->name }} - {{ $solicitud->detalles->first()->cantidad_solicitada }} unidades
                                        </div>
                                    @else
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            Solicitud múltiple ({{ $solicitud->detalles->count() }} productos)
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $solicitud->user->name }} - {{ $solicitud->total_unidades }} unidades total
                                        </div>
                                    @endif
                                @elseif($solicitud->inventario)
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $solicitud->inventario->nombre_producto }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $solicitud->user->name }} - {{ $solicitud->cantidad_solicitada }} unidades
                                    </div>
                                @else
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
                </a>
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
    @endif
</div>

<!-- Estilos adicionales para mejorar la interactividad -->
<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .group:hover .group-hover\:bg-blue-600 {
        background-color: #2563eb;
    }
    .group:hover .group-hover\:bg-yellow-600 {
        background-color: #d97706;
    }
    .group:hover .group-hover\:bg-green-600 {
        background-color: #059669;
    }
    .group:hover .group-hover\:bg-purple-600 {
        background-color: #7c3aed;
    }
</style>
@endsection