@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Mostrar resumen de última salida si existe -->
    @if(session('total_salida'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-6 transition-colors duration-200">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-300">
                        Salida registrada exitosamente!
                    </h3>
                </div>
                <p class="text-green-700 dark:text-green-400 mb-3">
                    Se registraron <span class="font-bold">{{ session('total_salida.cantidad_productos') }}</span> 
                    producto(s) para el cliente 
                    <span class="font-bold">{{ session('total_salida.cliente_nombre') }}</span> 
                    ({{ session('total_salida.cliente_area') }}) 
                    el {{ session('total_salida.fecha') }}
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-white dark:bg-gray-700 p-3 rounded">
                        <div class="text-gray-600 dark:text-gray-400">Subtotal</div>
                        <div class="font-semibold text-gray-900 dark:text-white text-lg">
                            ${{ number_format(session('total_salida.subtotal'), 2) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded">
                        <div class="text-gray-600 dark:text-gray-400">IVA (16%)</div>
                        <div class="font-semibold text-gray-900 dark:text-white text-lg">
                            ${{ number_format(session('total_salida.iva'), 2) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded">
                        <div class="text-gray-600 dark:text-gray-400">Total General</div>
                        <div class="font-semibold text-red-600 dark:text-red-400 text-xl">
                            ${{ number_format(session('total_salida.total'), 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="ml-4">
                <a href="{{ route('salidas.create') }}" 
                   class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Nueva Salida
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Mostrar advertencias si existen -->
    @if(session('warning'))
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300">
                    Advertencia
                </h3>
                <p class="text-yellow-700 dark:text-yellow-400 whitespace-pre-line">
                    {{ session('warning') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nueva Salida de Productos</h1>
        <a href="{{ route('salidas.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('salidas.store') }}" id="salidaForm">
            @csrf
            
            <!-- Información de la Cabecera -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="cliente_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cliente *
                    </label>
                    <select name="cliente_id" id="cliente_id" required
                            class="block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200 p-2">
                        <option value="">Seleccionar cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} - {{ $cliente->area }}
                            </option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- AGREGAR ESTE CAMPO: Fecha de Salida -->
                <div>
                    <label for="fecha_salida" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Fecha de Salida *
                    </label>
                    <input type="date" name="fecha_salida" id="fecha_salida" required
                           value="{{ old('fecha_salida', date('Y-m-d')) }}"
                           class="block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200 p-2">
                    @error('fecha_salida')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- AGREGAR ESTE CAMPO: Observaciones -->
            <div class="mb-6">
                <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Observaciones (Opcional)
                </label>
                <textarea name="observaciones" id="observaciones" rows="3"
                          class="block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200 p-2"
                          placeholder="Observaciones adicionales sobre esta salida...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-6 border-gray-300 dark:border-gray-600">

            <!-- Productos -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Productos</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Agregue los productos que salieron para este cliente
                        </p>
                    </div>
                    <button type="button" onclick="agregarProducto()" 
                            class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Agregar Producto
                    </button>
                </div>

                <div id="productos-container" class="space-y-4">
                    <!-- Los productos se agregarán dinámicamente aquí -->
                </div>

                @error('productos')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('productos.*.inventario_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('productos.*.cantidad')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Resumen de Totales -->
            <div class="mt-6 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 p-6 rounded-lg border border-red-200 dark:border-red-800 transition-colors duration-200">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen de la Salida</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center bg-white dark:bg-gray-700 p-4 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Productos</span>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="total-productos">0</div>
                    </div>
                    <div class="text-center bg-white dark:bg-gray-700 p-4 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal</span>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white" id="subtotal-total">$0.00</div>
                    </div>
                    <div class="text-center bg-white dark:bg-gray-700 p-4 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">IVA (16%)</span>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white" id="iva-total">$0.00</div>
                    </div>
                    <div class="text-center bg-white dark:bg-gray-700 p-4 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total General</span>
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400" id="total-general">$0.00</div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="resetForm()" 
                       class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-6 rounded transition-colors duration-200">
                    Limpiar
                </button>
                <a href="{{ route('salidas.index') }}" 
                   class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-2 px-6 rounded transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Registrar Salida
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.producto-item {
    transition: all 0.3s ease;
}
.producto-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.remove-producto {
    opacity: 0.7;
    transition: opacity 0.2s;
}
.remove-producto:hover {
    opacity: 1;
}
.stock-disponible {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
.stock-disponible.ok {
    color: #059669;
}
.stock-disponible.error {
    color: #dc2626;
}
</style>

<script>
let productoIndex = 0;
const inventarios = @json($inventarios);

// Función para obtener el precio promedio de un inventario
function getPrecioPromedio(inventarioId) {
    const inventario = inventarios.find(inv => inv.id == inventarioId);
    if (inventario && inventario.existencia > 0) {
        return parseFloat(inventario.precio_total / inventario.existencia);
    }
    return inventario ? parseFloat(inventario.precio_unitario || 0) : 0;
}

// Función para obtener la existencia de un inventario
function getExistencia(inventarioId) {
    const inventario = inventarios.find(inv => inv.id == inventarioId);
    return inventario ? parseInt(inventario.existencia) : 0;
}

// Agregar el primer producto automáticamente al cargar
document.addEventListener('DOMContentLoaded', function() {
    agregarProducto();
});

function agregarProducto() {
    const container = document.getElementById('productos-container');
    const productoDiv = document.createElement('div');
    productoDiv.className = 'producto-item bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 transition-colors duration-200';
    productoDiv.id = `producto-${productoIndex}`;
    
    productoDiv.innerHTML = `
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-red-500 dark:bg-red-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-medium">${productoIndex + 1}</span>
                </div>
            </div>
            
            <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Producto *
                    </label>
                    <select name="productos[${productoIndex}][inventario_id]" required
                            onchange="actualizarProducto(${productoIndex})"
                            class="producto-select block w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white text-sm p-2">
                        <option value="">Seleccionar producto</option>
                        ${inventarios.map(inv => `
                            <option value="${inv.id}" data-existencia="${inv.existencia}" data-precio="${getPrecioPromedio(inv.id)}">
                                ${inv.nombre_producto} ${inv.categoria ? '(' + inv.categoria + ')' : ''} - Disponible: ${inv.existencia || 0}
                            </option>
                        `).join('')}
                    </select>
                    <div class="stock-disponible" id="stock-info-${productoIndex}"></div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Cantidad *
                    </label>
                    <input type="number" 
                           name="productos[${productoIndex}][cantidad]" 
                           required 
                           min="1" 
                           value="1"
                           oninput="actualizarProducto(${productoIndex})"
                           class="cantidad-input block w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white text-sm p-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Precio Unitario
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">$</span>
                        <input type="text" 
                               readonly
                               id="precio-${productoIndex}"
                               class="block w-full pl-8 bg-gray-100 dark:bg-gray-500 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm text-gray-900 dark:text-white text-sm p-2"
                               value="$0.00">
                    </div>
                </div>
            </div>
            
            <div class="flex-shrink-0">
                <button type="button" 
                        onclick="eliminarProducto(${productoIndex})"
                        class="mt-6 remove-producto bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white p-2 rounded transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Subtotales del producto -->
        <div class="mt-3 grid grid-cols-3 gap-4 text-sm bg-white dark:bg-gray-600 p-3 rounded">
            <div>
                <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                <span class="font-semibold text-gray-900 dark:text-white ml-2" id="subtotal-${productoIndex}">$0.00</span>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">IVA (16%):</span>
                <span class="font-semibold text-gray-900 dark:text-white ml-2" id="iva-${productoIndex}">$0.00</span>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Total:</span>
                <span class="font-semibold text-red-600 dark:text-red-400 ml-2" id="total-${productoIndex}">$0.00</span>
            </div>
        </div>
    `;
    
    container.appendChild(productoDiv);
    productoIndex++;
    calcularTotales();
}

function actualizarProducto(index) {
    const productoDiv = document.getElementById(`producto-${index}`);
    const select = productoDiv.querySelector('.producto-select');
    const cantidadInput = productoDiv.querySelector('.cantidad-input');
    const precioInput = document.getElementById(`precio-${index}`);
    const stockInfo = document.getElementById(`stock-info-${index}`);
    
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const existencia = parseInt(selectedOption.dataset.existencia) || 0;
        const precio = parseFloat(selectedOption.dataset.precio) || 0;
        const cantidad = parseInt(cantidadInput.value) || 0;
        
        // Actualizar precio unitario
        precioInput.value = '$' + precio.toFixed(2);
        
        // Validar stock
        if (cantidad > existencia) {
            stockInfo.textContent = `Stock insuficiente! Disponible: ${existencia}`;
            stockInfo.className = 'stock-disponible error';
            cantidadInput.setCustomValidity('Stock insuficiente');
        } else {
            stockInfo.textContent = `Disponible: ${existencia}`;
            stockInfo.className = 'stock-disponible ok';
            cantidadInput.setCustomValidity('');
        }
        
        // Calcular y actualizar subtotales
        const subtotal = cantidad * precio;
        const iva = subtotal * 0.16;
        const total = subtotal + iva;
        
        document.getElementById(`subtotal-${index}`).textContent = '$' + subtotal.toFixed(2);
        document.getElementById(`iva-${index}`).textContent = '$' + iva.toFixed(2);
        document.getElementById(`total-${index}`).textContent = '$' + total.toFixed(2);
    } else {
        precioInput.value = '$0.00';
        stockInfo.textContent = '';
        stockInfo.className = 'stock-disponible';
        cantidadInput.setCustomValidity('');
        
        document.getElementById(`subtotal-${index}`).textContent = '$0.00';
        document.getElementById(`iva-${index}`).textContent = '$0.00';
        document.getElementById(`total-${index}`).textContent = '$0.00';
    }
    
    calcularTotales();
}

function eliminarProducto(index) {
    const productoDiv = document.getElementById(`producto-${index}`);
    if (productoDiv) {
        // Verificar que haya al menos un producto
        const container = document.getElementById('productos-container');
        if (container.children.length <= 1) {
            alert('Debe haber al menos un producto en la salida');
            return;
        }
        
        // Animación de eliminación
        productoDiv.style.opacity = '0';
        productoDiv.style.transform = 'translateX(20px)';
        
        setTimeout(() => {
            productoDiv.remove();
            calcularTotales();
            renumerarProductos();
        }, 300);
    }
}

function renumerarProductos() {
    const container = document.getElementById('productos-container');
    const productos = container.children;
    
    for (let i = 0; i < productos.length; i++) {
        const producto = productos[i];
        const productoId = producto.id.split('-')[1];
        const numeroSpan = producto.querySelector('.w-10.h-10 span');
        
        if (numeroSpan) {
            numeroSpan.textContent = i + 1;
        }
        
        // Actualizar índices en los inputs
        const select = producto.querySelector('select[name*="[inventario_id]"]');
        const cantidadInput = producto.querySelector('input[name*="[cantidad]"]');
        
        if (select) {
            select.name = `productos[${i}][inventario_id]`;
            select.onchange = () => actualizarProducto(i);
        }
        if (cantidadInput) {
            cantidadInput.name = `productos[${i}][cantidad]`;
            cantidadInput.oninput = () => actualizarProducto(i);
        }
        
        // Actualizar IDs de elementos
        const precioInput = document.getElementById(`precio-${productoId}`);
        const stockInfo = document.getElementById(`stock-info-${productoId}`);
        const subtotalSpan = document.getElementById(`subtotal-${productoId}`);
        const ivaSpan = document.getElementById(`iva-${productoId}`);
        const totalSpan = document.getElementById(`total-${productoId}`);
        
        if (precioInput) {
            precioInput.id = `precio-${i}`;
        }
        if (stockInfo) {
            stockInfo.id = `stock-info-${i}`;
        }
        if (subtotalSpan) {
            subtotalSpan.id = `subtotal-${i}`;
        }
        if (ivaSpan) {
            ivaSpan.id = `iva-${i}`;
        }
        if (totalSpan) {
            totalSpan.id = `total-${i}`;
        }
        
        // Actualizar el ID del div
        producto.id = `producto-${i}`;
    }
    
    productoIndex = productos.length;
}

function calcularTotales() {
    const container = document.getElementById('productos-container');
    const productos = container.children;
    
    let subtotalGeneral = 0;
    let ivaGeneral = 0;
    let totalGeneral = 0;
    let contadorProductos = 0;
    let stockValido = true;
    
    for (let i = 0; i < productos.length; i++) {
        const producto = productos[i];
        const select = producto.querySelector('.producto-select');
        const cantidadInput = producto.querySelector('.cantidad-input');
        
        if (select && cantidadInput) {
            const selectedOption = select.options[select.selectedIndex];
            const cantidad = parseFloat(cantidadInput.value) || 0;
            
            if (selectedOption && selectedOption.value && cantidad > 0) {
                const existencia = parseInt(selectedOption.dataset.existencia) || 0;
                const precio = parseFloat(selectedOption.dataset.precio) || 0;
                
                if (cantidad > existencia) {
                    stockValido = false;
                }
                
                const subtotal = cantidad * precio;
                const iva = subtotal * 0.16;
                const total = subtotal + iva;
                
                // Acumular totales generales
                subtotalGeneral += subtotal;
                ivaGeneral += iva;
                totalGeneral += total;
                contadorProductos++;
            }
        }
    }
    
    // Actualizar resumen general
    document.getElementById('total-productos').textContent = contadorProductos;
    document.getElementById('subtotal-total').textContent = '$' + subtotalGeneral.toFixed(2);
    document.getElementById('iva-total').textContent = '$' + ivaGeneral.toFixed(2);
    document.getElementById('total-general').textContent = '$' + totalGeneral.toFixed(2);
    
    // Mostrar advertencia de stock si es necesario
    const submitButton = document.querySelector('button[type="submit"]');
    if (!stockValido) {
        submitButton.disabled = true;
        submitButton.title = 'Hay productos con stock insuficiente';
        submitButton.className = 'bg-gray-400 dark:bg-gray-500 text-white font-bold py-2 px-6 rounded opacity-50 cursor-not-allowed';
    } else {
        submitButton.disabled = false;
        submitButton.title = '';
        submitButton.className = 'bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-2 px-6 rounded transition-colors duration-200 flex items-center';
    }
}

function resetForm() {
    if (confirm('¿Está seguro de que desea limpiar todos los productos?')) {
        const container = document.getElementById('productos-container');
        container.innerHTML = '';
        productoIndex = 0;
        agregarProducto();
        calcularTotales();
        document.getElementById('cliente_id').selectedIndex = 0;
        document.getElementById('fecha_salida').value = new Date().toISOString().split('T')[0];
        document.getElementById('observaciones').value = '';
    }
}

// Validación antes de enviar
document.getElementById('salidaForm').addEventListener('submit', function(e) {
    const container = document.getElementById('productos-container');
    const cliente = document.getElementById('cliente_id').value;
    const fechaSalida = document.getElementById('fecha_salida').value;
    
    if (container.children.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un producto a la salida');
        return false;
    }
    
    if (!cliente) {
        e.preventDefault();
        alert('Debe seleccionar un cliente');
        return false;
    }
    
    if (!fechaSalida) {
        e.preventDefault();
        alert('Debe seleccionar una fecha de salida');
        return false;
    }
    
    // Validar que todos los productos tengan datos válidos
    let productosValidos = 0;
    for (let i = 0; i < container.children.length; i++) {
        const producto = container.children[i];
        const inventario = producto.querySelector('select[name*="[inventario_id]"]');
        const cantidad = producto.querySelector('input[name*="[cantidad]"]');
        
        if (inventario && inventario.value && 
            cantidad && parseFloat(cantidad.value) > 0) {
            productosValidos++;
        }
    }
    
    if (productosValidos === 0) {
        e.preventDefault();
        alert('Debe completar correctamente al menos un producto');
        return false;
    }
    
    // Validar stock antes de enviar
    let stockValido = true;
    let mensajeStock = '';
    
    for (let i = 0; i < container.children.length; i++) {
        const producto = container.children[i];
        const select = producto.querySelector('.producto-select');
        const cantidadInput = producto.querySelector('.cantidad-input');
        
        if (select && cantidadInput && select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const existencia = parseInt(selectedOption.dataset.existencia) || 0;
            const cantidad = parseInt(cantidadInput.value) || 0;
            const productoNombre = selectedOption.text.split(' - ')[0];
            
            if (cantidad > existencia) {
                stockValido = false;
                mensajeStock += `• ${productoNombre}: Solicitado ${cantidad}, Disponible ${existencia}\n`;
            }
        }
    }
    
    if (!stockValido) {
        e.preventDefault();
        alert('Hay productos con stock insuficiente:\n\n' + mensajeStock);
        return false;
    }
    
    // Opcional: Mostrar confirmación
    const totalGeneral = document.getElementById('total-general').textContent;
    if (!confirm(`¿Confirmar salida por ${totalGeneral}?`)) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection