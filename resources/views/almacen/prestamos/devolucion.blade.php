@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('prestamos.show', $prestamo) }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Devolución de Préstamo #{{ str_pad($prestamo->id, 5, '0', STR_PAD_LEFT) }}
        </h1>
    </div>

    <!-- Información del préstamo -->
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg p-6 border border-purple-200 dark:border-purple-800">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Solicitante</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $prestamo->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Destino</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $prestamo->destino }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Fecha Préstamo</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $prestamo->fecha_prestamo->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Devolución Esperada</p>
                <p class="text-lg font-bold {{ $prestamo->esta_vencido ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                    {{ $prestamo->fecha_devolucion_esperada->format('d/m/Y') }}
                    @if($prestamo->esta_vencido)
                        <span class="block text-sm">Vencido hace {{ $prestamo->dias_vencido }} días</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Formulario de devolución -->
    <form method="POST" action="{{ route('prestamos.procesarDevolucion', $prestamo) }}" id="devolucionForm">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Productos a Devolver</h2>
            
            @if($prestamo->detalles && $prestamo->detalles->count() > 0)
                <div class="space-y-4">
                    @foreach($prestamo->detalles as $index => $detalle)
                    <div class="producto-devolucion bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                            <!-- Información del producto -->
                            <div class="lg:col-span-4">
                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $detalle->inventario->nombre_producto }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $detalle->inventario->categoria }} - {{ $detalle->inventario->medida }}
                                </p>
                                <div class="mt-2 flex space-x-4 text-sm">
                                    <span class="text-blue-600 dark:text-blue-400">
                                        Prestado: <strong>{{ $detalle->cantidad_prestada }}</strong>
                                    </span>
                                    <span class="text-green-600 dark:text-green-400">
                                        Ya devuelto: <strong>{{ $detalle->cantidad_devuelta }}</strong>
                                    </span>
                                    <span class="text-orange-600 dark:text-orange-400">
                                        Pendiente: <strong class="cantidad-pendiente" data-cantidad="{{ $detalle->cantidad_pendiente }}">{{ $detalle->cantidad_pendiente }}</strong>
                                    </span>
                                </div>
                            </div>

                            <!-- Cantidad a devolver -->
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Cantidad a Devolver
                                </label>
                                <input type="hidden" name="productos[{{ $index }}][detalle_id]" value="{{ $detalle->id }}">
                                <div class="flex items-center space-x-2">
                                    <button type="button" 
                                            class="btn-decrementar px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500"
                                            onclick="cambiarCantidad(this, -1)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <input type="number" 
                                           name="productos[{{ $index }}][cantidad_devuelta]" 
                                           class="cantidad-devolver w-20 text-center bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md"
                                           min="0" 
                                           max="{{ $detalle->cantidad_pendiente }}"
                                           value="{{ $detalle->cantidad_pendiente }}"
                                           data-pendiente="{{ $detalle->cantidad_pendiente }}"
                                           onchange="actualizarResumenDevolucion()"
                                           required>
                                    <button type="button" 
                                            class="btn-incrementar px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500"
                                            onclick="cambiarCantidad(this, 1)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                    <button type="button" 
                                            class="px-3 py-1 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-sm rounded-md"
                                            onclick="marcarTodo(this)">
                                        Todo
                                    </button>
                                </div>
                            </div>

                            <!-- Estado del producto -->
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Condición del Material
                                </label>
                                <select name="productos[{{ $index }}][condicion]" 
                                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md"
                                        required>
                                    <option value="bueno">✅ Bueno (regresa a inventario)</option>
                                    <option value="dañado">⚠️ Dañado (no regresa)</option>
                                    <option value="perdido">❌ Perdido (no regresa)</option>
                                </select>
                            </div>

                            <!-- Indicador visual -->
                            <div class="lg:col-span-2 flex items-center justify-center">
                                <div class="estado-indicador text-center">
                                    <span class="estado-texto px-3 py-1 text-xs font-medium rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300">
                                        Pendiente
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Observaciones -->
                <div class="mt-6">
                    <label for="observaciones_devolucion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Observaciones de la Devolución
                    </label>
                    <textarea name="observaciones_devolucion" 
                              id="observaciones_devolucion" 
                              rows="3"
                              class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md"
                              placeholder="Describe el estado de los materiales, algún daño, pérdida o comentario relevante..."></textarea>
                </div>

                <!-- Resumen de devolución -->
                <div class="mt-6 bg-gradient-to-r from-blue-50 to-green-50 dark:from-blue-900/20 dark:to-green-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Resumen de Devolución</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total a Devolver</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="total-devolver">0</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Buenos</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400" id="total-buenos">0</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Dañados</p>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="total-dañados">0</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Perdidos</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400" id="total-perdidos">0</p>
                        </div>
                    </div>
                </div>

                <!-- Advertencia si hay productos dañados o perdidos -->
                <div id="advertencia-condicion" class="mt-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 hidden">
                    <div class="flex">
                        <svg class="w-5 h-5 text-amber-500 dark:text-amber-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-amber-800 dark:text-amber-300">Importante</h4>
                            <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                                Los productos marcados como dañados o perdidos NO regresarán al inventario.
                                Se aplicarán las políticas correspondientes al solicitante.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('prestamos.show', $prestamo) }}" 
                       class="px-4 py-2 bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200">
                        Cancelar
                    </a>
                    <button type="button" 
                            onclick="marcarDevolucionCompleta()"
                            class="px-4 py-2 bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200">
                        Marcar Todo Como Devuelto
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200"
                            onclick="return confirm('¿Confirmar la devolución de estos productos?')">
                        Procesar Devolución
                    </button>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>No hay productos pendientes de devolución</p>
                </div>
            @endif
        </div>
    </form>
</div>

<script>
// Función para cambiar cantidad con botones +/-
function cambiarCantidad(boton, cambio) {
    const contenedor = boton.closest('.producto-devolucion');
    const input = contenedor.querySelector('.cantidad-devolver');
    const max = parseInt(input.max);
    const valorActual = parseInt(input.value) || 0;
    const nuevoValor = Math.max(0, Math.min(max, valorActual + cambio));
    
    input.value = nuevoValor;
    actualizarEstadoProducto(contenedor, nuevoValor);
    actualizarResumenDevolucion();
}

// Función para marcar todo el producto
function marcarTodo(boton) {
    const contenedor = boton.closest('.producto-devolucion');
    const input = contenedor.querySelector('.cantidad-devolver');
    const max = parseInt(input.max);
    
    input.value = max;
    actualizarEstadoProducto(contenedor, max);
    actualizarResumenDevolucion();
}

// Función para marcar toda la devolución como completa
function marcarDevolucionCompleta() {
    document.querySelectorAll('.cantidad-devolver').forEach(input => {
        input.value = input.max;
        const contenedor = input.closest('.producto-devolucion');
        actualizarEstadoProducto(contenedor, parseInt(input.max));
    });
    
    // Marcar todos como "bueno"
    document.querySelectorAll('select[name*="condicion"]').forEach(select => {
        select.value = 'bueno';
    });
    
    actualizarResumenDevolucion();
}

// Función para actualizar el estado visual del producto
function actualizarEstadoProducto(contenedor, cantidadDevolver) {
    const pendiente = parseInt(contenedor.querySelector('.cantidad-pendiente').dataset.cantidad);
    const estadoTexto = contenedor.querySelector('.estado-texto');
    
    if (cantidadDevolver === 0) {
        estadoTexto.className = 'estado-texto px-3 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300';
        estadoTexto.textContent = 'Sin Devolución';
    } else if (cantidadDevolver < pendiente) {
        estadoTexto.className = 'estado-texto px-3 py-1 text-xs font-medium rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300';
        estadoTexto.textContent = 'Parcial';
    } else {
        estadoTexto.className = 'estado-texto px-3 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300';
        estadoTexto.textContent = 'Completo';
    }
}

// Función para actualizar el resumen
function actualizarResumenDevolucion() {
    let totalDevolver = 0;
    let totalBuenos = 0;
    let totalDañados = 0;
    let totalPerdidos = 0;
    let hayProblemas = false;

    document.querySelectorAll('.producto-devolucion').forEach(producto => {
        const cantidad = parseInt(producto.querySelector('.cantidad-devolver').value) || 0;
        const condicion = producto.querySelector('select[name*="condicion"]').value;
        
        totalDevolver += cantidad;
        
        if (cantidad > 0) {
            switch(condicion) {
                case 'bueno':
                    totalBuenos += cantidad;
                    break;
                case 'dañado':
                    totalDañados += cantidad;
                    hayProblemas = true;
                    break;
                case 'perdido':
                    totalPerdidos += cantidad;
                    hayProblemas = true;
                    break;
            }
        }
    });

    document.getElementById('total-devolver').textContent = totalDevolver;
    document.getElementById('total-buenos').textContent = totalBuenos;
    document.getElementById('total-dañados').textContent = totalDañados;
    document.getElementById('total-perdidos').textContent = totalPerdidos;

    // Mostrar/ocultar advertencia
    const advertencia = document.getElementById('advertencia-condicion');
    if (hayProblemas) {
        advertencia.classList.remove('hidden');
    } else {
        advertencia.classList.add('hidden');
    }
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar estado inicial de cada producto
    document.querySelectorAll('.producto-devolucion').forEach(contenedor => {
        const cantidad = parseInt(contenedor.querySelector('.cantidad-devolver').value) || 0;
        actualizarEstadoProducto(contenedor, cantidad);
    });
    
    // Actualizar resumen inicial
    actualizarResumenDevolucion();
    
    // Agregar listener para cambios en selects de condición
    document.querySelectorAll('select[name*="condicion"]').forEach(select => {
        select.addEventListener('change', actualizarResumenDevolucion);
    });
});
</script>
@endsection