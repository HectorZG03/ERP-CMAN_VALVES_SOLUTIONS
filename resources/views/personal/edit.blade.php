@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Editar Colaborador</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $personal->nombre_completo }}</p>
        </div>
        <a href="{{ route('personal.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('personal.update', $personal) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre Completo -->
                <div class="md:col-span-2">
                    <label for="nombre_completo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre_completo" id="nombre_completo" required
                           value="{{ old('nombre_completo', $personal->nombre_completo) }}"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                           placeholder="Juan Pérez García">
                    @error('nombre_completo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Área -->
                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Área <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="area" id="area" required
                           value="{{ old('area', $personal->area) }}"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                           placeholder="Recursos Humanos">
                    @error('area')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Departamento -->
                <div>
                    <label for="departamento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Departamento <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="departamento" id="departamento" required
                           value="{{ old('departamento', $personal->departamento) }}"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                           placeholder="Administración">
                    @error('departamento')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Ingreso -->
                <div>
                    <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Fecha de Ingreso <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha_ingreso" id="fecha_ingreso" required
                           value="{{ old('fecha_ingreso', $personal->fecha_ingreso->format('Y-m-d')) }}"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    @error('fecha_ingreso')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sueldo -->
                <div>
                    <label for="sueldo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sueldo <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">$</span>
                        <input type="number" step="0.01" name="sueldo" id="sueldo" required
                               value="{{ old('sueldo', $personal->sueldo) }}"
                               class="w-full pl-7 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="15000.00">
                    </div>
                    @error('sueldo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grado -->
                <div class="md:col-span-2">
                    <label for="grado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Grado / Puesto
                    </label>
                    <input type="text" name="grado" id="grado"
                           value="{{ old('grado', $personal->grado) }}"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                           placeholder="Coordinador">
                    @error('grado')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estatus -->
                <div class="md:col-span-2">
                    <label for="estatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Estatus <span class="text-red-500">*</span>
                    </label>
                    <select name="estatus" id="estatus" required
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                        <option value="activo" {{ old('estatus', $personal->estatus) === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="baja" {{ old('estatus', $personal->estatus) === 'baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                    @error('estatus')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Información -->
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg transition-colors duration-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-900 dark:text-yellow-200">Importante</h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                            Cambiar el estatus a "Baja" solo actualiza el estado del colaborador. 
                            Para registrar una baja formal con motivo y fecha específica, debes usar la sección de "Bajas".
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('personal.show', $personal) }}" 
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Actualizar Colaborador
                </button>
            </div>
        </form>
    </div>

    <!-- Información adicional -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Información del colaborador -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Actual</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Antigüedad</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $personal->fecha_ingreso->diffForHumans(now(), true) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado el</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $personal->created_at->format('d/m/Y H:i:s') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última actualización</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $personal->updated_at->format('d/m/Y H:i:s') }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Acciones rápidas -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h3>
            <div class="space-y-3">
                @if($personal->estatus === 'activo')
                <a href="{{ route('bajas.create') }}?personal_id={{ $personal->id }}" 
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                    Registrar Baja Formal
                </a>

                <a href="{{ route('cambios.create') }}?personal_id={{ $personal->id }}" 
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    Registrar Cambio de Puesto/Sueldo
                </a>
                @endif

                <a href="{{ route('valepp.create') }}?personal_id={{ $personal->id }}" 
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Crear Vale de EPP
                </a>
            </div>
        </div>
    </div>
</div>
@endsection