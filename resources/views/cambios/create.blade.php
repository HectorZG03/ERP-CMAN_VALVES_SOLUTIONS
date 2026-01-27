@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nuevo Cambio de Puesto/Sueldo</h1>
        <a href="{{ route('cambios.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('cambios.store') }}" id="cambioForm">
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
                        <option value="{{ $persona->id }}" 
                                data-puesto="{{ $persona->grado ?? 'N/A' }}"
                                data-sueldo="{{ $persona->sueldo }}"
                                {{ old('personal_id') == $persona->id ? 'selected' : '' }}>
                            {{ $persona->nombre_completo }} - {{ $persona->area }}
                        </option>
                    @endforeach
                </select>
                @error('personal_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información Actual del Colaborador -->
            <div id="info-actual" class="hidden mb-6 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg transition-colors duration-200">
                <h3 class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-3">Información Actual</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-blue-700 dark:text-blue-300 font-medium mb-1">Puesto Actual</p>
                        <p class="text-lg font-bold text-blue-900 dark:text-blue-200" id="puesto-actual-display">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 dark:text-blue-300 font-medium mb-1">Sueldo Actual</p>
                        <p class="text-lg font-bold text-blue-900 dark:text-blue-200" id="sueldo-actual-display">-</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Puesto Nuevo -->
                <div>
                    <label for="puesto_nuevo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Puesto Nuevo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="puesto_nuevo" id="puesto_nuevo" required
                           value="{{ old('puesto_nuevo') }}"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                           placeholder="Gerente">
                    @error('puesto_nuevo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sueldo Nuevo -->
                <div>
                    <label for="sueldo_nuevo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sueldo Nuevo <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">$</span>
                        <input type="number" step="0.01" name="sueldo_nuevo" id="sueldo_nuevo" required
                               value="{{ old('sueldo_nuevo') }}"
                               class="w-full pl-7 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="20000.00">
                    </div>
                    @error('sueldo_nuevo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p id="diferencia-sueldo" class="mt-1 text-sm hidden"></p>
                </div>

                <!-- Fecha de Cambio -->
                <div>
                    <label for="fecha_cambio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Fecha del Cambio <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha_cambio" id="fecha_cambio" required
                           value="{{ old('fecha_cambio', date('Y-m-d')) }}"
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    @error('fecha_cambio')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Observaciones -->
            <div class="mt-6">
                <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Observaciones
                </label>
                <textarea name="observaciones" id="observaciones" rows="3"
                          class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                          placeholder="Notas adicionales sobre el cambio...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información -->
            <div class="mt-6 bg-green-50 dark:bg-green-900/30 p-4 rounded-lg transition-colors duration-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-green-900 dark:text-green-200">Información Importante</h3>
                        <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                            Al registrar este cambio, se guardará el historial y se actualizarán automáticamente 
                            el puesto y sueldo del colaborador en el sistema.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('cambios.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Registrar Cambio
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectPersonal = document.getElementById('personal_id');
    const infoActual = document.getElementById('info-actual');
    const sueldoNuevoInput = document.getElementById('sueldo_nuevo');
    const diferenciaSueldo = document.getElementById('diferencia-sueldo');
    
    let sueldoActual = 0;

    selectPersonal.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        
        if (this.value) {
            const puesto = option.dataset.puesto;
            sueldoActual = parseFloat(option.dataset.sueldo);
            
            document.getElementById('puesto-actual-display').textContent = puesto;
            document.getElementById('sueldo-actual-display').textContent = '$' + sueldoActual.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            infoActual.classList.remove('hidden');
            
            calcularDiferencia();
        } else {
            infoActual.classList.add('hidden');
            diferenciaSueldo.classList.add('hidden');
        }
    });

    sueldoNuevoInput.addEventListener('input', calcularDiferencia);

    function calcularDiferencia() {
        const sueldoNuevo = parseFloat(sueldoNuevoInput.value) || 0;
        
        if (sueldoActual > 0 && sueldoNuevo > 0) {
            const diferencia = sueldoNuevo - sueldoActual;
            const porcentaje = (diferencia / sueldoActual * 100).toFixed(1);
            
            diferenciaSueldo.classList.remove('hidden');
            
            if (diferencia > 0) {
                diferenciaSueldo.className = 'mt-1 text-sm text-green-600 dark:text-green-400';
                diferenciaSueldo.textContent = `Aumento: $${diferencia.toFixed(2)} (+${porcentaje}%)`;
            } else if (diferencia < 0) {
                diferenciaSueldo.className = 'mt-1 text-sm text-red-600 dark:text-red-400';
                diferenciaSueldo.textContent = `Reducción: $${Math.abs(diferencia).toFixed(2)} (${porcentaje}%)`;
            } else {
                diferenciaSueldo.className = 'mt-1 text-sm text-gray-600 dark:text-gray-400';
                diferenciaSueldo.textContent = 'Sin cambio en el sueldo';
            }
        } else {
            diferenciaSueldo.classList.add('hidden');
        }
    }
});
</script>
@endsection