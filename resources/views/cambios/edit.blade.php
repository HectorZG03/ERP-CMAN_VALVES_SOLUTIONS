@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Editar Cambio de Puesto/Sueldo</h1>
        <a href="{{ route('cambios.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('cambios.update', $cambio) }}" id="cambioForm">
            @csrf
            @method('PUT')
            
            <!-- Información del Colaborador (Solo lectura) -->
            <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg transition-colors duration-200">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Colaborador</h3>
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-pink-500 dark:bg-pink-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-medium text-lg">
                            {{ substr($cambio->personal->nombre_completo, 0, 2) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $cambio->personal->nombre_completo }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $cambio->personal->area }} - {{ $cambio->personal->departamento }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información Original del Cambio -->
            <div class="mb-6 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg transition-colors duration-200">
                <h3 class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-3">Datos Anteriores del Cambio</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-blue-700 dark:text-blue-300 font-medium mb-1">Puesto Anterior Registrado</p>
                        <p class="text-lg font-bold text-blue-900 dark:text-blue-200">{{ $cambio->puesto_anterior }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 dark:text-blue-300 font-medium mb-1">Sueldo Anterior Registrado</p>
                        <p class="text-lg font-bold text-blue-900 dark:text-blue-200">${{ number_format($cambio->sueldo_anterior, 2) }}</p>
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
                           value="{{ old('puesto_nuevo', $cambio->puesto_nuevo) }}"
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
                               value="{{ old('sueldo_nuevo', $cambio->sueldo_nuevo) }}"
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
                           value="{{ old('fecha_cambio', $cambio->fecha_cambio->format('Y-m-d')) }}"
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
                          placeholder="Notas adicionales sobre el cambio...">{{ old('observaciones', $cambio->observaciones) }}</textarea>
                @error('observaciones')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información -->
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg transition-colors duration-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-900 dark:text-yellow-200">Atención</h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                            Al actualizar este cambio, se modificarán automáticamente el puesto y sueldo actual 
                            del colaborador <strong>{{ $cambio->personal->nombre_completo }}</strong> en el sistema.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('cambios.show', $cambio) }}" 
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Actualizar Cambio
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sueldoNuevoInput = document.getElementById('sueldo_nuevo');
    const diferenciaSueldo = document.getElementById('diferencia-sueldo');
    
    const sueldoAnterior = {{ $cambio->sueldo_anterior }};

    sueldoNuevoInput.addEventListener('input', calcularDiferencia);

    // Calcular diferencia inicial
    calcularDiferencia();

    function calcularDiferencia() {
        const sueldoNuevo = parseFloat(sueldoNuevoInput.value) || 0;
        
        if (sueldoAnterior > 0 && sueldoNuevo > 0) {
            const diferencia = sueldoNuevo - sueldoAnterior;
            const porcentaje = (diferencia / sueldoAnterior * 100).toFixed(1);
            
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