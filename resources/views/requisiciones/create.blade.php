        @extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nueva Requisición de Material</h1>
        <a href="{{ route('requisiciones.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('requisiciones.store') }}" id="requisicionForm">
            @csrf
            
            <!-- SECCIÓN 1: INFORMACIÓN DEL SOLICITANTE -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    Información del Solicitante
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre_solicitante" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre del Solicitante <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre_solicitante" id="nombre_solicitante" required
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('nombre_solicitante', auth()->user()->name) }}">
                    </div>

                    <div>
    <label for="departamento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Departamento <span class="text-red-500">*</span>
    </label>
    <input type="text" 
           id="departamento"
           class="mt-1 block w-full bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-gray-900 dark:text-white transition-colors duration-200 uppercase font-semibold"
           value="{{ old('departamento', auth()->user()->departamento ?? auth()->user()->role) }}"
           readonly
           style="text-transform: uppercase; letter-spacing: 0.05em;">
    <input type="hidden" name="departamento" 
           value="{{ old('departamento', auth()->user()->departamento ?? auth()->user()->role) }}">
</div>
                </div>
            </div>

            <!-- SECCIÓN 2: INFORMACIÓN DEL PROYECTO -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    Información del Proyecto
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="proyecto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Proyecto
                        </label>
                        <input type="text" name="proyecto" id="proyecto"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('proyecto') }}" placeholder="Nombre del proyecto o N/A">
                    </div>

                    <div>
                        <label for="sit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            SIT (Sistema de Identificación de Trabajo)
                        </label>
                        <input type="text" name="sit" id="sit"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('sit') }}" placeholder="Código SIT o N/A">
                    </div>

                    <div>
                        <label for="partida" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Partida
                        </label>
                        <input type="text" name="partida" id="partida"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('partida') }}" placeholder="Número de partida o N/A">
                    </div>

                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Área
                        </label>
                        <input type="text" name="area" id="area"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('area') }}" placeholder="Área específica o N/A">
                    </div>

                    <div>
                        <label for="activo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Activo
                        </label>
                        <input type="text" name="activo" id="activo"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('activo') }}" placeholder="Número de activo o N/A">
                    </div>

                    <div>
                        <label for="plataforma" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Plataforma
                        </label>
                        <input type="text" name="plataforma" id="plataforma" 
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('plataforma') }}" placeholder="Ej: Plataforma A, Oficinas, etc. O N/A">
                    </div>

                    <div>
                        <label for="embarcacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Embarcación/Barco 
                        </label>
                        <input type="text" name="embarcacion" id="embarcacion" 
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('embarcacion') }}" placeholder="Nombre del barco o N/A">
                    </div>

                    <div>
                        <label for="contrato_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Contrato / Empresa <span class="text-red-500">*</span>
                        </label>
                        <select name="contrato_id" id="contrato_id" required
                                class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                            <option value="">Selecciona un contrato</option>
                            @foreach ($contratos as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->empresa_nombre }} — {{ $c->contrato }} — {{ $c->convenio }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tipo_requerimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tipo de Requerimiento <span class="text-red-500">*</span>
                        </label>
                        <select name="tipo_requerimiento" id="tipo_requerimiento" required
                                class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                            <option value="">Seleccionar tipo</option>
                            <option value="interno">Interno</option>
                            <option value="externo">Externo</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: MATERIALES SOLICITADOS -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Materiales Solicitados</h2>
                    <span class="text-sm text-gray-500 dark:text-gray-400" id="contador-materiales">0 materiales agregados</span>
                </div>

                <!-- Botón para agregar material -->
                <button type="button" 
                        onclick="agregarMaterial()"
                        class="mb-4 bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    + Agregar Material
                </button>

                <!-- Lista de materiales agregados -->
                <div id="materiales-agregados" class="space-y-3">
                    <!-- Los materiales se agregarán dinámicamente aquí -->
                </div>

                <!-- Mensaje inicial cuando no hay materiales -->
                <div id="mensaje-inicial" class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="font-medium">No hay materiales agregados</p>
                    <p class="text-sm">Haz clic en "Agregar Material" para añadir materiales a tu requisición</p>
                </div>
            </div>

            <!-- Comentario -->
            <div class="mb-6">
                <label for="comentario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Comentario/Justificación <span class="text-red-500">*</span>
                </label>
                <textarea name="comentario" id="comentario" rows="4" required
                          class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                          placeholder="Explica detalladamente la necesidad del material, uso específico, urgencia, etc.">{{ old('comentario') }}</textarea>
            </div>

            <!-- Resumen -->
            <div id="resumen-requisicion" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6 hidden transition-colors duration-200">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Resumen de la Requisición</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="total-materiales">0</div>
                        <div class="text-gray-600 dark:text-gray-400">Materiales Diferentes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="total-unidades">0</div>
                        <div class="text-gray-600 dark:text-gray-400">Total Unidades</div>
                    </div>
                </div>
            </div>

            <!-- Información -->
            <div class="mb-6 bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg transition-colors duration-200">
                <h3 class="text-lg font-medium text-purple-900 dark:text-purple-200 mb-2">Información sobre Requisiciones</h3>
                <div class="text-sm text-purple-700 dark:text-purple-300 space-y-1">
                    <p><strong>Interno:</strong> Material que se requiere de otros departamentos o almacén interno</p>
                    <p><strong>Externo:</strong> Material que debe ser comprado a proveedores externos</p>
                    <p><strong>Proceso:</strong> La requisición será revisada por Dirección y luego enviada a Almacén</p>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="limpiarRequisicion()"
                        class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Limpiar Todo
                </button>
                <button type="submit" 
                        id="btn-enviar"
                        class="bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 transition-colors duration-200"
                        disabled>
                    Enviar Requisición
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template para material -->
<template id="template-material">
    <div class="material-item bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-all duration-200">
        <div class="flex items-start justify-between mb-4">
            <h4 class="font-semibold text-gray-900 dark:text-white">Material #<span class="material-numero"></span></h4>
            <button type="button" 
                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 btn-eliminar transition-colors duration-200"
                    onclick="eliminarMaterial(this)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Material/Descripción <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="materiales[INDEX][material]"
                       class="material-descripcion w-full text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200" 
                       placeholder="Descripción del material"
                       required>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Cantidad <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="materiales[INDEX][cantidad]"
                       class="cantidad-input w-full text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200" 
                       min="1" 
                       value="1"
                       onchange="actualizarResumen()"
                       required>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Unidad <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="materiales[INDEX][unidad]"
                       class="unidad-input w-full text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200" 
                       placeholder="Ej: Piezas, Kg, Litros"
                       required>
            </div>
        </div>
    </div>
</template>

<script>
let contadorMateriales = 0;
let materialesAgregados = 0;

// Función para agregar material
function agregarMaterial() {
    const template = document.getElementById('template-material');
    const clone = template.content.cloneNode(true);
    
    const index = contadorMateriales++;
    
    // Actualizar número de material
    clone.querySelector('.material-numero').textContent = contadorMateriales;
    
    // Actualizar names con el índice correcto
    clone.querySelectorAll('[name*="INDEX"]').forEach(input => {
        input.name = input.name.replace('INDEX', index);
    });
    
    // Agregar al DOM
    document.getElementById('materiales-agregados').appendChild(clone);
    
    materialesAgregados++;
    actualizarContador();
    actualizarResumen();
    mostrarResumen();
}

// Función para eliminar material
function eliminarMaterial(boton) {
    const item = boton.closest('.material-item');
    item.remove();
    
    materialesAgregados--;
    actualizarContador();
    actualizarResumen();
    
    if (materialesAgregados === 0) {
        ocultarResumen();
    }
    
    // Renumerar materiales
    document.querySelectorAll('.material-numero').forEach((elem, index) => {
        elem.textContent = index + 1;
    });
}

// Función para actualizar contador
function actualizarContador() {
    const contador = document.getElementById('contador-materiales');
    const mensaje = document.getElementById('mensaje-inicial');
    const btnEnviar = document.getElementById('btn-enviar');
    
    contador.textContent = `${materialesAgregados} material${materialesAgregados !== 1 ? 'es' : ''} agregado${materialesAgregados !== 1 ? 's' : ''}`;
    
    if (materialesAgregados === 0) {
        mensaje.classList.remove('hidden');
        btnEnviar.disabled = true;
    } else {
        mensaje.classList.add('hidden');
        btnEnviar.disabled = false;
    }
}

// Función para actualizar resumen
function actualizarResumen() {
    let totalUnidades = 0;

    document.querySelectorAll('.material-item').forEach(item => {
        const cantidad = parseInt(item.querySelector('.cantidad-input').value) || 0;
        totalUnidades += cantidad;
    });

    document.getElementById('total-materiales').textContent = materialesAgregados;
    document.getElementById('total-unidades').textContent = totalUnidades;
}

// Función para mostrar resumen
function mostrarResumen() {
    document.getElementById('resumen-requisicion').classList.remove('hidden');
}

// Función para ocultar resumen
function ocultarResumen() {
    document.getElementById('resumen-requisicion').classList.add('hidden');
}

// Función para limpiar toda la requisición
function limpiarRequisicion() {
    if (materialesAgregados > 0 && !confirm('¿Estás seguro de que deseas limpiar toda la requisición?')) {
        return;
    }
    
    contadorMateriales = 0;
    materialesAgregados = 0;
    document.getElementById('materiales-agregados').innerHTML = '';
    document.getElementById('comentario').value = '';
    
    actualizarContador();
    ocultarResumen();
}
</script>
@endsection