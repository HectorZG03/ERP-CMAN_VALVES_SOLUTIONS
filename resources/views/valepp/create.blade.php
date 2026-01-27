@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nuevo Vale de EPP</h1>
        <a href="{{ route('valepp.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('valepp.store') }}" id="valeForm">
            @csrf
            
            <!-- Información del Vale -->
            <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-emerald-900 dark:text-emerald-200">Número de Vale</h3>
                        <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $numeroVale }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-emerald-700 dark:text-emerald-300">Fecha</p>
                        <p class="text-lg font-semibold text-emerald-900 dark:text-emerald-200">{{ date('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

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

            <!-- Fecha de Solicitud -->
            <div class="mb-6">
                <label for="fecha_solicitud" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Fecha de Solicitud <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha_solicitud" id="fecha_solicitud" required
                       value="{{ old('fecha_solicitud', date('Y-m-d')) }}"
                       class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                @error('fecha_solicitud')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Materiales / Equipos -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Materiales de EPP</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400" id="contador-materiales">0 materiales agregados</span>
                </div>

                <!-- Buscador de Materiales -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-4 transition-colors duration-200">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Buscar Material
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="buscador-material" 
                               class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="Buscar casco, guantes, botas...">
                        
                        <div id="resultados-materiales" 
                             class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg mt-1 hidden max-h-60 overflow-y-auto transition-colors duration-200">
                        </div>
                    </div>
                </div>

                <!-- Lista de materiales agregados -->
                <div id="materiales-agregados" class="space-y-3">
                    <!-- Los materiales se agregarán dinámicamente aquí -->
                </div>

                <div id="mensaje-inicial" class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <p class="font-medium">No hay materiales agregados</p>
                    <p class="text-sm">Busca y agrega los equipos de protección personal</p>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="mb-6">
                <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Observaciones
                </label>
                <textarea name="observaciones" id="observaciones" rows="3"
                          class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                          placeholder="Notas adicionales...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información -->
            <div class="mb-6 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg transition-colors duration-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-900 dark:text-blue-200">Información importante</h3>
                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                            Al guardar el vale, los materiales se descontarán automáticamente del inventario.<br>
                            Este proceso no se puede revertir.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('valepp.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        id="btn-enviar"
                        class="bg-emerald-500 hover:bg-emerald-600 dark:bg-emerald-600 dark:hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 transition-colors duration-200"
                        disabled>
                    Crear y Entregar Vale
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template para material agregado -->
<template id="template-material">
    <div class="material-item bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-all duration-200">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-900 dark:text-white material-nombre"></h4>
                    <button type="button" 
                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 btn-eliminar transition-colors duration-200"
                            onclick="eliminarMaterial(this)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Categoría: <span class="material-categoria"></span></p>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Cantidad</label>
                        <input type="number" 
                               class="cantidad-input w-full text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200" 
                               min="1" 
                               value="1"
                               required>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-xs text-gray-600 dark:text-gray-400">Disponible</p>
                        <p class="font-semibold text-green-600 dark:text-green-400 material-stock"></p>
                    </div>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="inventario_id[]" class="inventario-id">
        <input type="hidden" name="cantidad[]" class="cantidad-hidden">
    </div>
</template>

<script>
let materialesAgregados = [];
let contadorMateriales = 0;
let timeoutBusqueda;

const inventarios = @json($inventarios);

document.addEventListener('DOMContentLoaded', function() {
    const buscador = document.getElementById('buscador-material');
    
    buscador.addEventListener('input', function() {
        clearTimeout(timeoutBusqueda);
        timeoutBusqueda = setTimeout(() => {
            buscarMateriales(this.value);
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#buscador-material') && !e.target.closest('#resultados-materiales')) {
            document.getElementById('resultados-materiales').classList.add('hidden');
        }
    });
});

function buscarMateriales(termino) {
    const resultados = document.getElementById('resultados-materiales');
    
    if (termino.length < 2) {
        resultados.classList.add('hidden');
        return;
    }

    const materiales = inventarios.filter(item => 
        item.nombre_producto.toLowerCase().includes(termino.toLowerCase()) ||
        item.categoria.toLowerCase().includes(termino.toLowerCase())
    );

    mostrarResultados(materiales);
}

function mostrarResultados(materiales) {
    const resultados = document.getElementById('resultados-materiales');
    
    if (materiales.length === 0) {
        resultados.innerHTML = '<div class="p-3 text-gray-500 dark:text-gray-400 text-center">No se encontraron materiales</div>';
        resultados.classList.remove('hidden');
        return;
    }

    let html = '';
    materiales.forEach(material => {
        const yaAgregado = materialesAgregados.some(m => m.id === material.id);
        const claseDisabled = yaAgregado ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer';
        
        html += `
            <div class="p-3 border-b border-gray-100 dark:border-gray-600 ${claseDisabled} transition-colors duration-200" 
                 ${!yaAgregado ? `onclick='agregarMaterial(${JSON.stringify(material)})'` : ''}>
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">${material.nombre_producto}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">${material.categoria}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium ${material.existencia > 5 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'}">
                            Stock: ${material.existencia}
                        </div>
                        ${yaAgregado ? '<span class="text-xs text-gray-400 dark:text-gray-500">Ya agregado</span>' : ''}
                    </div>
                </div>
            </div>
        `;
    });

    resultados.innerHTML = html;
    resultados.classList.remove('hidden');
}

function agregarMaterial(material) {
    if (materialesAgregados.some(m => m.id === material.id)) return;

    const template = document.getElementById('template-material');
    const clone = template.content.cloneNode(true);

    clone.querySelector('.material-nombre').textContent = material.nombre_producto;
    clone.querySelector('.material-categoria').textContent = material.categoria;
    clone.querySelector('.material-stock').textContent = material.existencia;
    clone.querySelector('.inventario-id').value = material.id;
    clone.querySelector('.cantidad-hidden').value = 1;

    const inputCantidad = clone.querySelector('.cantidad-input');
    const cantidadHidden = clone.querySelector('.cantidad-hidden');
    
    inputCantidad.max = material.existencia;
    inputCantidad.addEventListener('input', function() {
        cantidadHidden.value = this.value;
    });

    document.getElementById('materiales-agregados').appendChild(clone);
    materialesAgregados.push(material);

    actualizarContador();
    limpiarBuscador();
}

function eliminarMaterial(boton) {
    const item = boton.closest('.material-item');
    const inventarioId = parseInt(item.querySelector('.inventario-id').value);
    
    materialesAgregados = materialesAgregados.filter(m => m.id !== inventarioId);
    item.remove();
    
    actualizarContador();
}

function actualizarContador() {
    const contador = document.getElementById('contador-materiales');
    const mensaje = document.getElementById('mensaje-inicial');
    const btnEnviar = document.getElementById('btn-enviar');
    
    contador.textContent = `${materialesAgregados.length} material${materialesAgregados.length !== 1 ? 'es' : ''} agregado${materialesAgregados.length !== 1 ? 's' : ''}`;
    
    if (materialesAgregados.length === 0) {
        mensaje.classList.remove('hidden');
        btnEnviar.disabled = true;
    } else {
        mensaje.classList.add('hidden');
        btnEnviar.disabled = false;
    }
}

function limpiarBuscador() {
    document.getElementById('buscador-material').value = '';
    document.getElementById('resultados-materiales').classList.add('hidden');
}
</script>
@endsection