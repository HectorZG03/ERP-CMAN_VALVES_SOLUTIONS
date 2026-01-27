@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nueva Baja de Colaborador</h1>
        <a href="{{ route('bajas.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('bajas.store') }}" id="bajaForm">
            @csrf
            
            <!-- Seleccionar Colaborador -->
            <div class="mb-6">
                <label for="personal_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Colaborador <span class="text-red-500">*</span>
                </label>
                <select name="personal_id" id="personal_id" required
                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    <option value="">Seleccione un colaborador</option>
                    @foreach($personalActivo as $persona)
                        <option value="{{ $persona->id }}" {{ old('personal_id') == $persona->id ? 'selected' : '' }}>
                            {{ $persona->nombre_completo }} - {{ $persona->area }}
                        </option>
                    @endforeach
                </select>
                @error('personal_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información del Colaborador (se muestra cuando se selecciona) -->
            <div id="info-colaborador" class="hidden mb-6 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg transition-colors duration-200">
                <h3 class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-3">Información del Colaborador</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-blue-700 dark:text-blue-300 font-medium">Área</p>
                        <p class="text-blue-600 dark:text-blue-400" id="info-area">-</p>
                    </div>
                    <div>
                        <p class="text-blue-700 dark:text-blue-300 font-medium">Departamento</p>
                        <p class="text-blue-600 dark:text-blue-400" id="info-departamento">-</p>
                    </div>
                    <div>
                        <p class="text-blue-700 dark:text-blue-300 font-medium">Puesto/Grado</p>
                        <p class="text-blue-600 dark:text-blue-400" id="info-grado">-</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Fecha de Baja -->
                <div>
                    <label for="fecha_baja" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Fecha de Baja <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha_baja" id="fecha_baja" required
                           value="{{ old('fecha_baja', date('Y-m-d')) }}"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    @error('fecha_baja')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Motivo de Baja -->
            <div class="mt-6">
                <label for="motivo_baja" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Motivo de Baja <span class="text-red-500">*</span>
                </label>
                <textarea name="motivo_baja" id="motivo_baja" rows="4" required
                          class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                          placeholder="Describe el motivo de la baja...">{{ old('motivo_baja') }}</textarea>
                @error('motivo_baja')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Advertencia -->
            <div class="mt-6 bg-red-50 dark:bg-red-900/30 p-4 rounded-lg transition-colors duration-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-900 dark:text-red-200">Advertencia</h3>
                        <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                            Al registrar esta baja, el colaborador cambiará automáticamente a estado "Baja" 
                            y ya no aparecerá en la lista de personal activo.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('bajas.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Registrar Baja
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectPersonal = document.getElementById('personal_id');
    const infoColaborador = document.getElementById('info-colaborador');
    
    // Datos del personal para mostrar información
    const personalData = {
        @foreach($personalActivo as $persona)
            {{ $persona->id }}: {
                area: "{{ $persona->area }}",
                departamento: "{{ $persona->departamento }}",
                grado: "{{ $persona->grado ?? 'N/A' }}"
            },
        @endforeach
    };

    selectPersonal.addEventListener('change', function() {
        if (this.value && personalData[this.value]) {
            const data = personalData[this.value];
            document.getElementById('info-area').textContent = data.area;
            document.getElementById('info-departamento').textContent = data.departamento;
            document.getElementById('info-grado').textContent = data.grado;
            infoColaborador.classList.remove('hidden');
        } else {
            infoColaborador.classList.add('hidden');
        }
    });
});
</script>
@endsection