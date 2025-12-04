@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Detalle del Usuario</h1>
        <div class="space-x-2">
            <a href="{{ route('users.edit', $user) }}" 
               class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Editar
            </a>
            <a href="{{ route('users.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol y Número de Empleado</label>
                    <span class="mt-1 px-3 py-1 rounded-full text-sm font-medium
                        @if($user->role === 'direccion') bg-purple-100 text-purple-800
                        @elseif($user->role === 'ti') bg-blue-100 text-blue-800
                        @elseif(str_contains($user->role, 'almacen')) bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>

                    <span class="mt-1 px-3 py-1 rounded-full text-sm font-medium
                        @if($user->role === 'direccion') bg-purple-100 text-purple-800
                        @elseif($user->role === 'ti') bg-blue-100 text-blue-800
                        @elseif(str_contains($user->role, 'almacen')) bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $user->num_empleado }}
                    </span>
                </div>

                
                
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                    <p class="mt-1 text-gray-900">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                    <p class="mt-1 text-gray-900">{{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <span class="mt-1 px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                        Activo
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Permisos del Usuario -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Permisos y Accesos</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Permisos Asignados</h4>
                <ul class="space-y-2">
                    @if($user->canManageUsers())
                        <li class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Gestionar Usuarios
                        </li>
                    @endif
                    
                    @if($user->canManageInventory())
                        <li class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Gestionar Inventario
                        </li>
                    @endif
                    
                    @if($user->canApproveRequests())
                        <li class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Aprobar Solicitudes
                        </li>
                    @endif
                    
                    <li class="flex items-center text-green-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Crear Solicitudes y Requisiciones
                    </li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Actividad Reciente</h4>
                <div class="space-y-2">
                    @if($user->canManageInventory())
                        <p class="text-sm text-gray-600">
                            <strong>Entradas registradas:</strong> 
                            {{ \App\Models\Entrada::where('user_id', $user->id)->count() }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <strong>Salidas registradas:</strong> 
                            {{ \App\Models\Salida::where('user_id', $user->id)->count() }}
                        </p>
                    @endif
                    
                    <p class="text-sm text-gray-600">
                        <strong>Solicitudes creadas:</strong> 
                        {{ \App\Models\SolicitudMaterial::where('user_id', $user->id)->count() }}
                    </p>
                    
                    <p class="text-sm text-gray-600">
                        <strong>Requisiciones creadas:</strong> 
                        {{ \App\Models\Requisicion::where('user_id', $user->id)->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection