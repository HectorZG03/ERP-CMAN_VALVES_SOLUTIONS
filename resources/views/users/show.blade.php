@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Perfil de Usuario</h1>
            <p class="mt-1 text-sm text-gray-600">Detalles completos de {{ $user->name }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('users.edit', $user) }}" 
               class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <a href="{{ route('users.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Tarjeta Principal con Foto y Firma -->
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-2xl overflow-hidden">
        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Foto de Perfil -->
                <div class="lg:col-span-1 flex justify-center lg:justify-start">
                    <div class="relative">
                        <div class="w-48 h-48 rounded-full overflow-hidden border-4 border-white shadow-2xl">
                            <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/default-avatar.png') }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="absolute bottom-2 right-2 w-12 h-12 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Información Principal -->
                <div class="lg:col-span-2 text-white space-y-4">
                    <h2 class="text-4xl font-bold">{{ $user->name }}</h2>
                    <div class="space-y-2">
                        <p class="text-xl text-blue-100">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $user->email }}
                        </p>
                        <p class="text-xl text-blue-100">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            Empleado #{{ $user->num_empleado }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-3 mt-4">
                        <span class="px-4 py-2 rounded-full text-sm font-bold
                            @if($user->role === 'direccion') bg-purple-200 text-purple-900
                            @elseif($user->role === 'ti') bg-blue-200 text-blue-900
                            @elseif(str_contains($user->role, 'almacen')) bg-green-200 text-green-900
                            @else bg-white text-gray-900 @endif">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                        <span class="px-4 py-2 bg-green-200 text-green-900 text-sm font-bold rounded-full">
                            ✓ Activo
                        </span>
                    </div>
                </div>

                <!-- Firma Digital -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg p-4 shadow-lg">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 text-center">Firma Digital</h3>
                        <div class="h-32 bg-gray-50 rounded border-2 border-dashed border-gray-300 flex items-center justify-center">
                            @if($user->signature)
                                <img src="{{ asset('storage/' . $user->signature) }}" 
                                     alt="Firma de {{ $user->name }}" 
                                     class="max-w-full max-h-full object-contain">
                            @else
                                <div class="text-center text-gray-400">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    <p class="text-xs">Sin firma</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Detallada -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Datos del Usuario -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center border-b pb-3">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Información del Usuario
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="w-1/3">
                        <p class="text-sm font-medium text-gray-600">Nombre:</p>
                    </div>
                    <div class="w-2/3">
                        <p class="text-base font-semibold text-gray-900">{{ $user->name }}</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-1/3">
                        <p class="text-sm font-medium text-gray-600">Email:</p>
                    </div>
                    <div class="w-2/3">
                        <p class="text-base text-gray-900">{{ $user->email }}</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-1/3">
                        <p class="text-sm font-medium text-gray-600">N° Empleado:</p>
                    </div>
                    <div class="w-2/3">
                        <p class="text-base font-semibold text-blue-600">{{ $user->num_empleado }}</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-1/3">
                        <p class="text-sm font-medium text-gray-600">Registrado:</p>
                    </div>
                    <div class="w-2/3">
                        <p class="text-base text-gray-900">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-1/3">
                        <p class="text-sm font-medium text-gray-600">Actualizado:</p>
                    </div>
                    <div class="w-2/3">
                        <p class="text-base text-gray-900">{{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permisos y Accesos -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center border-b pb-3">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Permisos Asignados
            </h3>
            
            <ul class="space-y-3">
                @if($user->canManageUsers())
                    <li class="flex items-center text-green-700 bg-green-50 p-3 rounded-lg">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Gestionar Usuarios</span>
                    </li>
                @endif
                
                @if($user->canManageInventory())
                    <li class="flex items-center text-green-700 bg-green-50 p-3 rounded-lg">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Gestionar Inventario</span>
                    </li>
                @endif
                
                @if($user->canApproveRequests())
                    <li class="flex items-center text-green-700 bg-green-50 p-3 rounded-lg">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Aprobar Solicitudes</span>
                    </li>
                @endif
                
                <li class="flex items-center text-green-700 bg-green-50 p-3 rounded-lg">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">Crear Solicitudes y Requisiciones</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center border-b pb-3">
            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Actividad Reciente
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @if($user->canManageInventory())
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg text-center border-l-4 border-blue-500">
                    <p class="text-3xl font-bold text-blue-700">{{ \App\Models\Entrada::where('user_id', $user->id)->count() }}</p>
                    <p class="text-sm text-gray-700 font-medium mt-1">Entradas Registradas</p>
                </div>
                
                <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-lg text-center border-l-4 border-red-500">
                    <p class="text-3xl font-bold text-red-700">{{ \App\Models\Salida::where('user_id', $user->id)->count() }}</p>
                    <p class="text-sm text-gray-700 font-medium mt-1">Salidas Registradas</p>
                </div>
            @endif
            
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg text-center border-l-4 border-green-500">
                <p class="text-3xl font-bold text-green-700">{{ \App\Models\SolicitudMaterial::where('user_id', $user->id)->count() }}</p>
                <p class="text-sm text-gray-700 font-medium mt-1">Solicitudes Creadas</p>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg text-center border-l-4 border-purple-500">
                <p class="text-3xl font-bold text-purple-700">{{ \App\Models\Requisicion::where('user_id', $user->id)->count() }}</p>
                <p class="text-sm text-gray-700 font-medium mt-1">Requisiciones Creadas</p>
            </div>
        </div>
    </div>
</div>
@endsection