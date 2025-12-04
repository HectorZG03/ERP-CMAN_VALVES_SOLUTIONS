@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nueva Solicitud de Material</h1>
        <a href="{{ route('solicitudes.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('solicitudes.store') }}" id="solicitudForm">
            @csrf
            
            <!-- Sección de Productos -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Productos Solicitados</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400" id="contador-productos">0 productos agregados</span>
                </div>

                <!-- Buscador de Productos -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-4 transition-colors duration-200">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Buscar Producto
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="buscador-producto" 
                               class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="Escribe el nombre del producto, categoría o medida...">
                        
                        <!-- Resultados de búsqueda -->
                        <div id="resultados-busqueda" 
                             class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg mt-1 hidden max-h-60 overflow-y-auto transition-colors duration-200">
                        </div>
                    </div>
                </div>

                <!-- Lista de productos agregados -->
                <div id="productos-agregados" class="space-y-3">
                    <!-- Los productos se agregarán dinámicamente aquí -->
                </div>

                <!-- Botón para agregar producto (solo visible cuando no hay productos) -->
                <div id="mensaje-inicial" class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="font-medium">No hay productos agregados</p>
                    <p class="text-sm">Utiliza el buscador para agregar productos a tu solicitud</p>
                </div>
            </div>

            {{-- ubicacion de destino --}}

            <!-- Selección de Ubicación de Destino -->
            <div class="mb-6">
                <label for="ubicacion_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ubicación de Destino
                </label>
                <select name="destino" id="destino" required
                        class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    <option value="">Seleccione una ubicación</option>
                    <option value="Capitan America" {{ old('ubicacion_id') == 'Capitan America' ? 'selected' : '' }}>BMS Capitan America</option>
                    <option value="Base Operativa" {{ old('ubicacion_id') == 'Base Operativa' ? 'selected' : '' }}>Base Operativa</option>
                    <option value="BMS MAYA" {{ old('ubicacion_id') == 'BMS MAYA' ? 'selected' : '' }}>BMS Maya</option>
                </select>
            </div>

            <!-- operador y categoria -->

            <div>
                <label for="operador" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Operador (Opcional)
                </label>
                <input type="text" name="operador" id="operador"
                    class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200" placeholder=" N/A">
            </div>

            <div>
                <label for="categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Categoría (Opcional)
                </label>
                <input type="text" name="categoria" id="categoria"
                    class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200 " placeholder=" N/A">
            </div>

            

            <!-- Comentario -->
            <div class="mb-6">
                <label for="comentario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Comentario (Opcional)
                </label>
                <textarea name="comentario" id="comentario" rows="3"
                          class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                          placeholder="Especifica el motivo de la solicitud...">{{ old('comentario') }}</textarea>
            </div>

            <!-- Información del proceso -->
            <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg mb-6 transition-colors duration-200">
                <h3 class="text-lg font-medium text-blue-900 dark:text-blue-200 mb-2">Proceso de Aprobación</h3>
                <div class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                    <p>• Tu solicitud será enviada a Dirección para aprobación</p>
                    <p>• Una vez aprobada, será enviada al área de Almacén</p>
                    <p>• Recibirás notificación del estatus de tu solicitud</p>
                </div>
            </div>

            <!-- Resumen de la solicitud -->
            <div id="resumen-solicitud" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6 hidden transition-colors duration-200">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Resumen de la Solicitud</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="total-productos">0</div>
                        <div class="text-gray-600 dark:text-gray-400">Productos Diferentes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="total-unidades">0</div>
                        <div class="text-gray-600 dark:text-gray-400">Total Unidades</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="valor-estimado">$0.00</div>
                        <div class="text-gray-600 dark:text-gray-400">Valor Estimado</div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="limpiarSolicitud()"
                        class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Limpiar Todo
                </button>
                <button type="submit" 
                        id="btn-enviar"
                        class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 transition-colors duration-200"
                        disabled>
                    Enviar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template para producto agregado -->
<template id="template-producto">
    <div class="producto-item bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-all duration-200">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-900 dark:text-white producto-nombre"></h4>
                    <button type="button" 
                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 btn-eliminar transition-colors duration-200"
                            onclick="eliminarProducto(this)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Categoría: <span class="producto-categoria"></span></p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Medida: <span class="producto-medida"></span></p>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Cantidad</label>
                        <input type="number" 
                               class="cantidad-input w-full text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200" 
                               min="1" 
                               value="1"
                               onchange="actualizarResumen()"
                               required>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-xs text-gray-600 dark:text-gray-400">Disponible</p>
                        <p class="font-semibold text-green-600 dark:text-green-400 producto-stock"></p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-xs text-gray-600 dark:text-gray-400">Precio Unit.</p>
                        <p class="font-semibold text-blue-600 dark:text-blue-400 producto-precio">$0.00</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Campos hidden para envío -->
        <input type="hidden" name="productos[INDEX][inventario_id]" class="inventario-id">
        <input type="hidden" name="productos[INDEX][cantidad_solicitada]" class="cantidad-hidden">
    </div>
</template>

<script>
let productosAgregados = [];
let contadorProductos = 0;
let timeoutBusqueda;

// Configurar eventos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const buscador = document.getElementById('buscador-producto');
    
    buscador.addEventListener('input', function() {
        clearTimeout(timeoutBusqueda);
        timeoutBusqueda = setTimeout(() => {
            buscarProductos(this.value);
        }, 300);
    });

    // Ocultar resultados cuando se hace clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#buscador-producto') && !e.target.closest('#resultados-busqueda')) {
            document.getElementById('resultados-busqueda').classList.add('hidden');
        }
    });
});

// Función para buscar productos
function buscarProductos(termino) {
    const resultados = document.getElementById('resultados-busqueda');
    
    if (termino.length < 2) {
        resultados.classList.add('hidden');
        return;
    }

    fetch(`{{ route('solicitudes.buscar-productos') }}?q=${encodeURIComponent(termino)}`)
        .then(response => response.json())
        .then(productos => {
            mostrarResultados(productos);
        })
        .catch(error => {
            console.error('Error:', error);
            resultados.classList.add('hidden');
        });
}

// Función para mostrar resultados de búsqueda
function mostrarResultados(productos) {
    const resultados = document.getElementById('resultados-busqueda');
    
    if (productos.length === 0) {
        resultados.innerHTML = '<div class="p-3 text-gray-500 dark:text-gray-400 text-center">No se encontraron productos</div>';
        resultados.classList.remove('hidden');
        return;
    }

    let html = '';
    productos.forEach(producto => {
        // Verificar si el producto ya está agregado
        const yaAgregado = productosAgregados.some(p => p.id === producto.id);
        const claseDisabled = yaAgregado ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer';
        
        html += `
            <div class="p-3 border-b border-gray-100 dark:border-gray-600 ${claseDisabled} transition-colors duration-200" 
                 ${!yaAgregado ? `onclick="agregarProducto(${JSON.stringify(producto).replace(/"/g, '&quot;')})"` : ''}>
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">${producto.nombre_producto}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">${producto.categoria} - ${producto.medida}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium ${producto.existencia > 10 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'}">
                            Stock: ${producto.existencia}
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

// Función para agregar producto a la lista
function agregarProducto(producto) {
    // Verificar si ya está agregado
    if (productosAgregados.some(p => p.id === producto.id)) {
        return;
    }

    // Obtener detalles completos del producto
    fetch(`{{ url('solicitudes/producto') }}/${producto.id}`)
        .then(response => response.json())
        .then(productoCompleto => {
            const template = document.getElementById('template-producto');
            const clone = template.content.cloneNode(true);

            // Rellenar datos del producto
            clone.querySelector('.producto-nombre').textContent = productoCompleto.nombre_producto;
            clone.querySelector('.producto-categoria').textContent = productoCompleto.categoria;
            clone.querySelector('.producto-medida').textContent = productoCompleto.medida;
            clone.querySelector('.producto-stock').textContent = productoCompleto.existencia;
            clone.querySelector('.producto-precio').textContent = `$${parseFloat(productoCompleto.precio_promedio || 0).toFixed(2)}`;

            // Configurar campos hidden
            const index = contadorProductos++;
            clone.querySelector('.inventario-id').name = `productos[${index}][inventario_id]`;
            clone.querySelector('.inventario-id').value = productoCompleto.id;
            clone.querySelector('.cantidad-hidden').name = `productos[${index}][cantidad_solicitada]`;
            clone.querySelector('.cantidad-hidden').value = 1;

            // Configurar input de cantidad
            const inputCantidad = clone.querySelector('.cantidad-input');
            inputCantidad.max = productoCompleto.existencia;
            inputCantidad.addEventListener('input', function() {
                clone.querySelector('.cantidad-hidden').value = this.value;
                actualizarResumen();
            });

            // Agregar al DOM
            document.getElementById('productos-agregados').appendChild(clone);

            // Agregar a la lista de productos
            productosAgregados.push({
                ...productoCompleto,
                cantidad: 1,
                index: index
            });

            // Actualizar UI
            actualizarContador();
            actualizarResumen();
            limpiarBuscador();
            mostrarResumen();
            
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al obtener los detalles del producto');
        });
}

// Función para eliminar producto
function eliminarProducto(boton) {
    const item = boton.closest('.producto-item');
    const inventarioId = parseInt(item.querySelector('.inventario-id').value);
    
    // Remover del array
    productosAgregados = productosAgregados.filter(p => p.id !== inventarioId);
    
    // Remover del DOM
    item.remove();
    
    // Actualizar UI
    actualizarContador();
    actualizarResumen();
    
    if (productosAgregados.length === 0) {
        ocultarResumen();
    }
}

// Función para actualizar contador
function actualizarContador() {
    const contador = document.getElementById('contador-productos');
    const mensaje = document.getElementById('mensaje-inicial');
    const btnEnviar = document.getElementById('btn-enviar');
    
    contador.textContent = `${productosAgregados.length} producto${productosAgregados.length !== 1 ? 's' : ''} agregado${productosAgregados.length !== 1 ? 's' : ''}`;
    
    if (productosAgregados.length === 0) {
        mensaje.classList.remove('hidden');
        btnEnviar.disabled = true;
    } else {
        mensaje.classList.add('hidden');
        btnEnviar.disabled = false;
    }
}

// Función para actualizar resumen
function actualizarResumen() {
    let totalProductos = productosAgregados.length;
    let totalUnidades = 0;
    let valorEstimado = 0;

    document.querySelectorAll('.producto-item').forEach((item, index) => {
        const cantidad = parseInt(item.querySelector('.cantidad-input').value) || 0;
        const precio = parseFloat(item.querySelector('.producto-precio').textContent.replace('$', '')) || 0;
        
        totalUnidades += cantidad;
        valorEstimado += cantidad * precio;
        
        // Actualizar el producto en el array
        if (productosAgregados[index]) {
            productosAgregados[index].cantidad = cantidad;
        }
    });

    document.getElementById('total-productos').textContent = totalProductos;
    document.getElementById('total-unidades').textContent = totalUnidades;
    document.getElementById('valor-estimado').textContent = `$${valorEstimado.toFixed(2)}`;
}

// Función para mostrar resumen
function mostrarResumen() {
    document.getElementById('resumen-solicitud').classList.remove('hidden');
}

// Función para ocultar resumen
function ocultarResumen() {
    document.getElementById('resumen-solicitud').classList.add('hidden');
}

// Función para limpiar buscador
function limpiarBuscador() {
    document.getElementById('buscador-producto').value = '';
    document.getElementById('resultados-busqueda').classList.add('hidden');
}

// Función para limpiar toda la solicitud
function limpiarSolicitud() {
    if (productosAgregados.length > 0 && !confirm('¿Estás seguro de que deseas limpiar toda la solicitud?')) {
        return;
    }
    
    productosAgregados = [];
    contadorProductos = 0;
    document.getElementById('productos-agregados').innerHTML = '';
    document.getElementById('comentario').value = '';
    
    actualizarContador();
    ocultarResumen();
    limpiarBuscador();
}
</script>
@endsection