@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Editar Usuario</h1>
        <a href="{{ route('users.index') }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Volver
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nombre
                    </label>
                    <input type="text" name="name" id="name" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('name', $user->name) }}">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <input type="email" name="email" id="email" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('email', $user->email) }}">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">
                        Rol
                    </label>
                    <select name="role" id="role" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="direccion" {{ $user->role === 'direccion' ? 'selected' : '' }}>Dirección</option>
                        <option value="ti" {{ $user->role === 'ti' ? 'selected' : '' }}>TI</option>
                        <option value="aux_ti" {{ $user->role === 'aux_ti' ? 'selected' : '' }}>Auxiliar TI</option>
                        <option value="almacen" {{ $user->role === 'almacen' ? 'selected' : '' }}>Almacén</option>
                        <option value="aux_almacen" {{ $user->role === 'aux_almacen' ? 'selected' : '' }}>Auxiliar Almacén</option>
                        <option value="calidad" {{ $user->role === 'calidad' ? 'selected' : '' }}>Calidad</option>
                        <option value="aux_calidad" {{ $user->role === 'aux_calidad' ? 'selected' : '' }}>Auxiliar Calidad</option>
                        <option value="contabilidad" {{ $user->role === 'contabilidad' ? 'selected' : '' }}>Contabilidad</option>
                        <option value="aux_contabilidad" {{ $user->role === 'aux_contabilidad' ? 'selected' : '' }}>Auxiliar Contabilidad</option>
                        <option value="estimaciones" {{ $user->role === 'estimaciones' ? 'selected' : '' }}>Estimaciones</option>
                        <option value="aux_estimaciones" {{ $user->role === 'aux_estimaciones' ? 'selected' : '' }}>Auxiliar Estimaciones</option>
                        <option value="finanzas" {{ $user->role === 'finanzas' ? 'selected' : '' }}>Finanzas</option>
                        <option value="aux_finanzas" {{ $user->role === 'aux_finanzas' ? 'selected' : '' }}>Auxiliar Finanzas</option>
                        <option value="logistica" {{ $user->role === 'logistica' ? 'selected' : '' }}>Logística</option>
                        <option value="aux_logistica" {{ $user->role === 'aux_logistica' ? 'selected' : '' }}>Auxiliar Logística</option>
                        <option value="rh" {{ $user->role === 'rh' ? 'selected' : '' }}>Recursos Humanos</option>
                        <option value="aux_rh" {{ $user->role === 'aux_rh' ? 'selected' : '' }}>Auxiliar RH</option>
                        <option value="operaciones" {{ $user->role === 'operaciones' ? 'selected' : '' }}>Operaciones</option>
                        <option value="aux_operaciones" {{ $user->role === 'aux_operaciones' ? 'selected' : '' }}>Auxiliar Operaciones</option>
                        <option value="hse" {{ $user->role === 'hse' ? 'selected' : '' }}>HSE</option>
                        <option value="aux_hse" {{ $user->role === 'aux_hse' ? 'selected' : '' }}>Auxiliar HSE</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Nueva Contraseña (Opcional)
                    </label>
                    <input type="password" name="password" id="password"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Dejar en blanco para mantener la actual">
                </div>

                <div class="md:col-span-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirmar Nueva Contraseña
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>


            <!-- NUMERO DE EMPLEADO -->
            <div class="mt-6">
                <label for="num_empleado" class="block text-sm font-medium text-gray-700">
                    Número de Empleado
                </label>
                <input type="text" name="num_empleado" id="num_empleado" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('num_empleado', $user->num_empleado) }}">
            </div>

            <div class="mt-6 bg-yellow-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-yellow-900 mb-2">Información Actual</h3>
                <div class="text-sm text-yellow-700 space-y-1">
                    <p><strong>Rol Actual:</strong> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                    <p><strong>Registrado:</strong> {{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                    <p><strong>Última Actualización:</strong> {{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection