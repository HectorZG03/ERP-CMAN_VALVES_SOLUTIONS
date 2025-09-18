@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Crear Usuario</h1>
        <a href="{{ route('users.index') }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Volver
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nombre
                    </label>
                    <input type="text" name="name" id="name" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('name') }}">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <input type="email" name="email" id="email" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('email') }}">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">
                        Rol
                    </label>
                    <select name="role" id="role" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccionar rol</option>
                        <option value="direccion">Dirección</option>
                        <option value="ti">TI</option>
                        <option value="aux_ti">Auxiliar TI</option>
                        <option value="almacen">Almacén</option>
                        <option value="aux_almacen">Auxiliar Almacén</option>
                        <option value="calidad">Calidad</option>
                        <option value="aux_calidad">Auxiliar Calidad</option>
                        <option value="contabilidad">Contabilidad</option>
                        <option value="aux_contabilidad">Auxiliar Contabilidad</option>
                        <option value="estimaciones">Estimaciones</option>
                        <option value="aux_estimaciones">Auxiliar Estimaciones</option>
                        <option value="finanzas">Finanzas</option>
                        <option value="aux_finanzas">Auxiliar Finanzas</option>
                        <option value="logistica">Logística</option>
                        <option value="aux_logistica">Auxiliar Logística</option>
                        <option value="rh">Recursos Humanos</option>
                        <option value="aux_rh">Auxiliar RH</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Contraseña
                    </label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirmar Contraseña
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-blue-900 mb-2">Permisos por Rol</h3>
                <div class="text-sm text-blue-700 space-y-1">
                    <p><strong>Dirección:</strong> Puede aprobar solicitudes y requisiciones, gestionar usuarios e inventario</p>
                    <p><strong>TI:</strong> Puede gestionar usuarios y acceso completo al sistema</p>
                    <p><strong>Almacén/Aux_Almacén:</strong> Puede gestionar inventario, entradas y salidas</p>
                    <p><strong>Otros roles:</strong> Pueden crear solicitudes de material y requisiciones</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection