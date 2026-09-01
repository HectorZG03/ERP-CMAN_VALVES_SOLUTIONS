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
        <form method="POST"
            action="{{ route('salidas.store') }}"
            id="salidaForm"
            data-buscar-productos-url="{{ route('salidas.buscar-productos') }}">
            @csrf

            <!-- Información de la cabecera -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="cliente_id"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cliente *
                    </label>

                    <select name="cliente_id"
                            id="cliente_id"
                            required
                            class="block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200 p-2">
                        <option value="">Seleccionar cliente</option>

                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} - {{ $cliente->area }}
                            </option>
                        @endforeach
                    </select>

                    @error('cliente_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_salida"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Fecha de Salida *
                    </label>

                    <input type="date"
                        name="fecha_salida"
                        id="fecha_salida"
                        required
                        value="{{ old('fecha_salida', date('Y-m-d')) }}"
                        class="block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200 p-2">

                    @error('fecha_salida')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Observaciones -->
            <div class="mb-6">
                <label for="observaciones"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Observaciones (Opcional)
                </label>

                <textarea name="observaciones"
                        id="observaciones"
                        rows="3"
                        class="block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200 p-2"
                        placeholder="Observaciones adicionales sobre esta salida...">{{ old('observaciones') }}</textarea>

                @error('observaciones')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <hr class="my-6 border-gray-300 dark:border-gray-600">

            <!-- Productos -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Productos
                        </h3>

                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Agregue los productos que salieron para este cliente
                        </p>
                    </div>

                    <button type="button"
                            id="agregar-producto"
                            class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2"
                            fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z"
                                clip-rule="evenodd"/>
                        </svg>

                        Agregar Producto
                    </button>
                </div>

                <div id="productos-container" class="space-y-4">
                    <!-- Los productos se agregarán dinámicamente aquí -->
                </div>

                @error('productos')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ $message }}
                    </p>
                @enderror

                @error('productos.*.inventario_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ $message }}
                    </p>
                @enderror

                @error('productos.*.cantidad')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Resumen de totales -->
            <div class="mt-6 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 p-6 rounded-lg border border-red-200 dark:border-red-800 transition-colors duration-200">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Resumen de la Salida
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center bg-white dark:bg-gray-700 p-4 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Productos
                        </span>

                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400"
                            id="total-productos">
                            0
                        </div>
                    </div>

                    <div class="text-center bg-white dark:bg-gray-700 p-4 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Subtotal
                        </span>

                        <div class="text-2xl font-bold text-gray-900 dark:text-white"
                            id="subtotal-total">
                            $0.00
                        </div>
                    </div>

                    <div class="text-center bg-white dark:bg-gray-700 p-4 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            IVA (16%)
                        </span>

                        <div class="text-2xl font-bold text-gray-900 dark:text-white"
                            id="iva-total">
                            $0.00
                        </div>
                    </div>

                    <div class="text-center bg-white dark:bg-gray-700 p-4 rounded">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Total General
                        </span>

                        <div class="text-3xl font-bold text-red-600 dark:text-red-400"
                            id="total-general">
                            $0.00
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button"
                        id="limpiar-formulario"
                        class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-6 rounded transition-colors duration-200">
                    Limpiar
                </button>

                <a href="{{ route('salidas.index') }}"
                class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition-colors duration-200">
                    Cancelar
                </a>

                <button type="submit"
                        class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-2 px-6 rounded transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2"
                        fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"/>
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
<script src="{{ asset('js/salidas-create.js') }}" defer></script>

@endsection