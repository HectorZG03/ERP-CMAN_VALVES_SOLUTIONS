@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Mostrar resumen de última entrada si existe -->
    @if(session('entrada_reciente'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-6 transition-colors duration-200">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-300">
                        Entrada registrada exitosamente!
                    </h3>
                </div>
                <div class="mb-4">
                    <p class="text-green-700 dark:text-green-400">
                        <span class="font-bold">Factura #{{ session('entrada_reciente.numero_factura') }}</span> - 
                        Proveedor: <span class="font-bold">{{ session('entrada_reciente.proveedor_nombre') }}</span> - 
                        {{ session('entrada_reciente.fecha') }}
                    </p>
                    <p class="text-green-700 dark:text-green-400 mt-1">
                        {{ session('entrada_reciente.cantidad_productos') }} producto(s) - 
                        {{ session('entrada_reciente.cantidad_total') }} unidad(es)
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-white dark:bg-gray-700 p-3 rounded">
                        <div class="text-gray-600 dark:text-gray-400">Subtotal</div>
                        <div class="font-semibold text-gray-900 dark:text-white text-lg">
                            ${{ number_format(session('entrada_reciente.subtotal'), 2) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded">
                        <div class="text-gray-600 dark:text-gray-400">IVA (16%)</div>
                        <div class="font-semibold text-gray-900 dark:text-white text-lg">
                            ${{ number_format(session('entrada_reciente.iva'), 2) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded">
                        <div class="text-gray-600 dark:text-gray-400">Total General</div>
                        <div class="font-semibold text-green-600 dark:text-green-400 text-xl">
                            ${{ number_format(session('entrada_reciente.total'), 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="ml-4 flex flex-col space-y-2">
                <a href="{{ route('entradas.create') }}" 
                   class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 text-center">
                    Nueva Entrada
                </a>
                <a href="{{ route('entradas.show', session('entrada_reciente.id')) }}" 
                   class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 text-center">
                    Ver Detalles
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nueva Entrada de Materiales</h1>
        <a href="{{ route('entradas.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('entradas.store') }}" id="entradaForm">
            @csrf
            
            <!-- Información de la Cabecera -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="proveedor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Proveedor *
                    </label>
                    <select name="proveedor_id" id="proveedor_id" required
                            class="block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200 p-2">
                        <option value="">Seleccionar proveedor</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                {{ $proveedor->proveedor }}
                            </option>
                        @endforeach
                    </select>
                    @error('proveedor_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="fecha_entrada" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Fecha de Entrada *
                    </label>
                    <input type="date" name="fecha_entrada" id="fecha_entrada" required
                           value="{{ old('fecha_entrada', date('Y-m-d')) }}"
                           class="block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200 p-2">
                    @error('fecha_entrada')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Observaciones (Opcional)
                </label>
                <textarea name="observaciones" id="observaciones" rows="3"
                          class="block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200 p-2"
                          placeholder="Observaciones adicionales sobre esta entrada...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-6 border-gray-300 dark:border-gray-600">

            <!-- Materiales -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Materiales</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Agregue los materiales que ingresaron con este proveedor
                        </p>
                    </div>
                    <button type="button" onclick="agregarMaterial()" 
                            class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Agregar Material
                    </button>
                </div>

                <div id="materiales-container" class="space-y-4">
                    <!-- Los materiales se agregarán dinámicamente aquí -->
                </div>

                @error('materiales')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('materiales.*.inventario_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('materiales.*.cantidad')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('materiales.*.precio_unitario')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Resumen de Totales -->
            <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-6 rounded-lg border border-blue-200 dark:border-blue-800 transition-colors duration-200">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen de la Entrada</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center bg-white dark:bg-gray-700 p-4 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Materiales</span>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="total-materiales">0</div>
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
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400" id="total-general">$0.00</div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="resetForm()" 
                       class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-6 rounded transition-colors duration-200">
                    Limpiar
                </button>
                <a href="{{ route('entradas.index') }}" 
                   class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-2 px-6 rounded transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Registrar Entrada
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.material-item {
    transition: all 0.3s ease;
}
.material-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.remove-material {
    opacity: 0.7;
    transition: opacity 0.2s;
}
.remove-material:hover {
    opacity: 1;
}
</style>

<script>
let materialIndex = 0;
const inventarios = @json($inventarios);

// Agregar el primer material automáticamente al cargar
document.addEventListener('DOMContentLoaded', function() {
    agregarMaterial();
});

function agregarMaterial() {
    const container = document.getElementById('materiales-container');
    const materialDiv = document.createElement('div');
    materialDiv.className = 'material-item bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 transition-colors duration-200';
    materialDiv.id = `material-${materialIndex}`;
    
    materialDiv.innerHTML = `
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-blue-500 dark:bg-blue-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-medium">${materialIndex + 1}</span>
                </div>
            </div>
            
            <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Producto *
                    </label>
                    <select name="materiales[${materialIndex}][inventario_id]" required
                            onchange="calcularTotales()"
                            class="block w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white text-sm p-2">
                        <option value="">Seleccionar producto</option>
                        ${inventarios.map(inv => `
                            <option value="${inv.id}">${inv.nombre_producto} ${inv.categoria ? '(' + inv.categoria + ')' : ''} - Stock: ${inv.existencia || 0}</option>
                        `).join('')}
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Cantidad *
                    </label>
                    <input type="number" 
                           name="materiales[${materialIndex}][cantidad]" 
                           required 
                           min="1" 
                           value="1"
                           oninput="calcularTotales()"
                           class="block w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white text-sm p-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Precio Unitario *
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">$</span>
                        <input type="number" 
                               name="materiales[${materialIndex}][precio_unitario]" 
                               required 
                               min="0" 
                               step="0.01"
                               value="0"
                               oninput="calcularTotales()"
                               class="block w-full pl-8 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white text-sm p-2">
                    </div>
                </div>
            </div>
            
            <div class="flex-shrink-0">
                <button type="button" 
                        onclick="eliminarMaterial(${materialIndex})"
                        class="mt-6 remove-material bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white p-2 rounded transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Subtotales del material -->
        <div class="mt-3 grid grid-cols-3 gap-4 text-sm bg-white dark:bg-gray-600 p-3 rounded">
            <div>
                <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                <span class="font-semibold text-gray-900 dark:text-white ml-2" id="subtotal-${materialIndex}">$0.00</span>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">IVA (16%):</span>
                <span class="font-semibold text-gray-900 dark:text-white ml-2" id="iva-${materialIndex}">$0.00</span>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Total:</span>
                <span class="font-semibold text-green-600 dark:text-green-400 ml-2" id="total-${materialIndex}">$0.00</span>
            </div>
        </div>
    `;
    
    container.appendChild(materialDiv);
    materialIndex++;
    calcularTotales();
}

function eliminarMaterial(index) {
    const materialDiv = document.getElementById(`material-${index}`);
    if (materialDiv) {
        // Verificar que haya al menos un material
        const container = document.getElementById('materiales-container');
        if (container.children.length <= 1) {
            alert('Debe haber al menos un material en la entrada');
            return;
        }
        
        // Animación de eliminación
        materialDiv.style.opacity = '0';
        materialDiv.style.transform = 'translateX(20px)';
        
        setTimeout(() => {
            materialDiv.remove();
            calcularTotales();
            renumerarMateriales();
        }, 300);
    }
}

function renumerarMateriales() {
    const container = document.getElementById('materiales-container');
    const materiales = container.children;
    
    for (let i = 0; i < materiales.length; i++) {
        const material = materiales[i];
        const materialId = material.id.split('-')[1];
        const numeroSpan = material.querySelector('.w-10.h-10 span');
        
        if (numeroSpan) {
            numeroSpan.textContent = i + 1;
        }
        
        // Actualizar índices en los inputs
        const select = material.querySelector('select[name*="[inventario_id]"]');
        const cantidadInput = material.querySelector('input[name*="[cantidad]"]');
        const precioInput = material.querySelector('input[name*="[precio_unitario]"]');
        
        if (select) {
            select.name = `materiales[${i}][inventario_id]`;
        }
        if (cantidadInput) {
            cantidadInput.name = `materiales[${i}][cantidad]`;
        }
        if (precioInput) {
            precioInput.name = `materiales[${i}][precio_unitario]`;
        }
        
        // Actualizar IDs de subtotales
        const subtotalSpan = document.getElementById(`subtotal-${materialId}`);
        const ivaSpan = document.getElementById(`iva-${materialId}`);
        const totalSpan = document.getElementById(`total-${materialId}`);
        
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
        material.id = `material-${i}`;
    }
    
    materialIndex = materiales.length;
}

function calcularTotales() {
    const container = document.getElementById('materiales-container');
    const materiales = container.children;
    
    let subtotalGeneral = 0;
    let ivaGeneral = 0;
    let totalGeneral = 0;
    let contadorMateriales = 0;
    
    for (let i = 0; i < materiales.length; i++) {
        const material = materiales[i];
        const cantidadInput = material.querySelector('input[name*="[cantidad]"]');
        const precioInput = material.querySelector('input[name*="[precio_unitario]"]');
        
        if (cantidadInput && precioInput) {
            const cantidad = parseFloat(cantidadInput.value) || 0;
            const precio = parseFloat(precioInput.value) || 0;
            
            if (cantidad > 0 && precio >= 0) {
                const subtotal = cantidad * precio;
                const iva = subtotal * 0.16;
                const total = subtotal + iva;
                
                // Obtener el ID actual del material
                const materialId = material.id.split('-')[1];
                
                // Actualizar subtotales del material individual
                const subtotalSpan = document.getElementById(`subtotal-${materialId}`);
                const ivaSpan = document.getElementById(`iva-${materialId}`);
                const totalSpan = document.getElementById(`total-${materialId}`);
                
                if (subtotalSpan) subtotalSpan.textContent = '$' + subtotal.toFixed(2);
                if (ivaSpan) ivaSpan.textContent = '$' + iva.toFixed(2);
                if (totalSpan) totalSpan.textContent = '$' + total.toFixed(2);
                
                // Acumular totales generales
                subtotalGeneral += subtotal;
                ivaGeneral += iva;
                totalGeneral += total;
                contadorMateriales++;
            }
        }
    }
    
    // Actualizar resumen general
    document.getElementById('total-materiales').textContent = contadorMateriales;
    document.getElementById('subtotal-total').textContent = '$' + subtotalGeneral.toFixed(2);
    document.getElementById('iva-total').textContent = '$' + ivaGeneral.toFixed(2);
    document.getElementById('total-general').textContent = '$' + totalGeneral.toFixed(2);
}

function resetForm() {
    if (confirm('¿Está seguro de que desea limpiar todos los materiales?')) {
        const container = document.getElementById('materiales-container');
        container.innerHTML = '';
        materialIndex = 0;
        agregarMaterial();
        calcularTotales();
        document.getElementById('proveedor_id').selectedIndex = 0;
        document.getElementById('fecha_entrada').value = new Date().toISOString().split('T')[0];
        document.getElementById('observaciones').value = '';
    }
}

// Validación antes de enviar
document.getElementById('entradaForm').addEventListener('submit', function(e) {
    const container = document.getElementById('materiales-container');
    const proveedor = document.getElementById('proveedor_id').value;
    const fechaEntrada = document.getElementById('fecha_entrada').value;
    
    if (container.children.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un material a la entrada');
        return false;
    }
    
    if (!proveedor) {
        e.preventDefault();
        alert('Debe seleccionar un proveedor');
        return false;
    }
    
    if (!fechaEntrada) {
        e.preventDefault();
        alert('Debe seleccionar una fecha de entrada');
        return false;
    }
    
    // Validar que todos los materiales tengan datos válidos
    let materialesValidos = 0;
    for (let i = 0; i < container.children.length; i++) {
        const material = container.children[i];
        const inventario = material.querySelector('select[name*="[inventario_id]"]');
        const cantidad = material.querySelector('input[name*="[cantidad]"]');
        const precio = material.querySelector('input[name*="[precio_unitario]"]');
        
        if (inventario && inventario.value && 
            cantidad && parseFloat(cantidad.value) > 0 && 
            precio && parseFloat(precio.value) > 0) {
            materialesValidos++;
        }
    }
    
    if (materialesValidos === 0) {
        e.preventDefault();
        alert('Debe completar correctamente al menos un material');
        return false;
    }
    
    // Opcional: Mostrar confirmación
    const totalGeneral = document.getElementById('total-general').textContent;
    if (!confirm(`¿Confirmar entrada por ${totalGeneral}?`)) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection