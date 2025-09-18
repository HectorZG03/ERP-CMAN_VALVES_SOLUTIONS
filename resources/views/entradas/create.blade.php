@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nueva Entrada de Material</h1>
        <a href="{{ route('entradas.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('entradas.store') }}" id="entradaForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="proveedor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Proveedor
                    </label>
                    <select name="proveedor_id" id="proveedor_id" required
                            class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                        <option value="">Seleccionar proveedor</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}">{{ $proveedor->proveedor }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="inventario_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Producto
                    </label>
                    <select name="inventario_id" id="inventario_id" required
                            class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                        <option value="">Seleccionar producto</option>
                        @foreach($inventarios as $inventario)
                            <option value="{{ $inventario->id }}">
                                {{ $inventario->nombre_producto }} ({{ $inventario->categoria }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="cantidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Cantidad
                    </label>
                    <input type="number" name="cantidad" id="cantidad" required min="1"
                           class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                           value="{{ old('cantidad') }}" onchange="calcularTotales()">
                </div>

                <div>
                    <label for="precio_unitario" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Precio Unitario
                    </label>
                    <input type="number" name="precio_unitario" id="precio_unitario" required min="0" step="0.01"
                           class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                           value="{{ old('precio_unitario') }}" onchange="calcularTotales()">
                </div>
            </div>

            <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg transition-colors duration-200">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Resumen de la Entrada</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white" id="subtotal">$0.00</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">IVA (16%):</span>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white" id="iva">$0.00</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total:</span>
                        <div class="text-xl font-bold text-green-600 dark:text-green-400" id="total">$0.00</div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" 
                        class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Registrar Entrada
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function calcularTotales() {
    const cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
    const precioUnitario = parseFloat(document.getElementById('precio_unitario').value) || 0;
    
    const subtotal = cantidad * precioUnitario;
    const iva = subtotal * 0.16;
    const total = subtotal + iva;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('iva').textContent = '$' + iva.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}
</script>
@endsection