@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nueva Salida de Material</h1>
        <a href="{{ route('salidas.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('salidas.store') }}" id="salidaForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="cliente_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Cliente
                    </label>
                    <select name="cliente_id" id="cliente_id" required
                            class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                        <option value="">Seleccionar cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }} - {{ $cliente->area }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="inventario_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Producto
                    </label>
                    <select name="inventario_id" id="inventario_id" required
                            class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                            onchange="actualizarDisponible()">
                        <option value="">Seleccionar producto</option>
                        @foreach($inventarios as $inventario)
                            <option value="{{ $inventario->id }}" 
                                    data-existencia="{{ $inventario->existencia }}"
                                    data-precio="{{ $inventario->getPrecioPromedio() }}">
                                {{ $inventario->nombre_producto }} - Disponible: {{ $inventario->existencia }}
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
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" id="disponible-info">
                        Selecciona un producto para ver la disponibilidad
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Precio Unitario (Automático)
                    </label>
                    <input type="text" id="precio_mostrar" readonly
                           class="mt-1 block w-full bg-gray-50 dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-gray-900 dark:text-gray-300 transition-colors duration-200"
                           value="$0.00">
                </div>
            </div>

            <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg transition-colors duration-200">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Resumen de la Salida</h3>
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
                        <div class="text-xl font-bold text-red-600 dark:text-red-400" id="total">$0.00</div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" 
                        class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Registrar Salida
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let precioUnitarioActual = 0;

function actualizarDisponible() {
    const select = document.getElementById('inventario_id');
    const option = select.options[select.selectedIndex];
    const info = document.getElementById('disponible-info');
    const precioMostrar = document.getElementById('precio_mostrar');
    const cantidadInput = document.getElementById('cantidad');
    
    if (option.value) {
        const existencia = option.dataset.existencia;
        const precio = parseFloat(option.dataset.precio);
        
        info.textContent = `Disponible: ${existencia} unidades`;
        cantidadInput.max = existencia;
        precioUnitarioActual = precio;
        precioMostrar.value = '$' + precio.toFixed(2);
        
        calcularTotales();
    } else {
        info.textContent = 'Selecciona un producto para ver la disponibilidad';
        cantidadInput.max = '';
        precioUnitarioActual = 0;
        precioMostrar.value = '$0.00';
        calcularTotales();
    }
}

function calcularTotales() {
    const cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
    
    const subtotal = cantidad * precioUnitarioActual;
    const iva = subtotal * 0.16;
    const total = subtotal + iva;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('iva').textContent = '$' + iva.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}
</script>
@endsection