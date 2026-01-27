@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Editar Baja</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Baja #{{ str_pad($baja->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <a href="{{ route('bajas.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver a Bajas
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('bajas.update', $baja) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Seleccionar Colaborador -->
                <div>
                    <label for="personal_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Colaborador <span class="text-red-500">*</span>
                    </label>
                    <select name="personal_id" id="personal_id" required
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                        <option value="">Seleccione un colaborador</option>
                        @foreach($personalActivo as $persona)
                            <option value="{{ $persona->id }}" 
                                    {{ old('personal_id', $baja->personal_id) == $persona->id ? 'selected' : '' }}
                                    {{ $persona->id == $baja->personal_id ? '' : ($persona->estatus == 'baja' ? 'disabled' : '') }}>
                                {{ $persona->nombre_completo }} - {{ $persona->area }}
                                @if($persona->estatus == 'baja' && $persona->id != $baja->personal_id)
                                    (Inactivo)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('personal_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Baja -->
                <div>
                    <label for="fecha_baja" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Fecha de Baja <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha_baja" id="fecha_baja" required
                           value="{{ old('fecha_baja', $baja->fecha_baja->format('Y-m-d')) }}"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    @error('fecha_baja')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Motivo de Baja -->
                <div class="md:col-span-2">
                    <label for="motivo_baja" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Motivo de Baja <span class="text-red-500">*</span>
                    </label>
                    <textarea name="motivo_baja" id="motivo_baja" rows="4" required
                              class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                              placeholder="Describa el motivo de la baja...">{{ old('motivo_baja', $baja->motivo_baja) }}</textarea>
                    @error('motivo_baja')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Información del Colaborador Actual -->
            <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Colaborador Actual</h3>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-500 dark:bg-red-600 rounded-full flex items-center justify-center mr-3">
                        <span class="text-white font-medium">
                            {{ substr($baja->personal->nombre_completo, 0, 2) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $baja->personal->nombre_completo }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $baja->personal->area }} - {{ $baja->personal->departamento }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">Ingreso: {{ $baja->personal->fecha_ingreso->format('d/m/Y') }}</p>
                    </div>
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
                            • Si cambias el colaborador, el anterior será reactivado automáticamente.<br>
                            • El nuevo colaborador seleccionado será marcado como "baja".<br>
                            • Esta acción actualizará los registros de ambos colaboradores.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('bajas.show', $baja) }}" 
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Actualizar Baja
                </button>
            </div>
        </form>
    </div>

    <!-- Información adicional -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Información de la Baja -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información de la Baja</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrada el</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $baja->created_at->format('d/m/Y H:i:s') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrada por</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $baja->user->name ?? 'Sistema' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última actualización</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $baja->updated_at->format('d/m/Y H:i:s') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID de Baja</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ str_pad($baja->id, 4, '0', STR_PAD_LEFT) }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Acciones rápidas -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h3>
            <div class="space-y-3">
                <a href="{{ route('personal.show', $baja->personal_id) }}" 
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Ver Colaborador
                </a>

                <a href="{{ route('personal.edit', $baja->personal_id) }}" 
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar Colaborador
                </a>

                <form method="POST" action="{{ route('bajas.destroy', $baja) }}" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors duration-200"
                            onclick="return confirm('¿Eliminar esta baja? El colaborador será reactivado automáticamente.')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar Baja
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const personalSelect = document.getElementById('personal_id');
    
    personalSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.disabled && selectedOption.value !== '' && selectedOption.value !== '{{ $baja->personal_id }}') {
            alert('Este colaborador ya está inactivo. Solo puedes seleccionar colaboradores activos o el colaborador actual de la baja.');
            this.value = '{{ $baja->personal_id }}';
        }
    });
});
</script>
@endsection