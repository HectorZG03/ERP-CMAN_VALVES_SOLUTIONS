@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('proveedores.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Proveedores
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Nuevo Proveedor
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('proveedores.store') }}">
                @csrf

                <!-- Nombre del Proveedor -->
                <div class="mb-6">
                    <label for="proveedor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nombre del Proveedor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="proveedor" 
                           id="proveedor" 
                           value="{{ old('proveedor') }}"
                           required
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                           placeholder="Ej: Distribuidora ABC S.A. de C.V.">
                    @error('proveedor')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Economico -->
                <div class="mb-6">
                    <label for="economico" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Economico <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="economico" 
                           id="economico" 
                           value="{{ old('economico') }}"
                           required
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    @error('economico')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dirección -->
                <div class="mb-6">
                    <label for="direccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Dirección <span class="text-red-500">*</span>
                    </label>
                    <textarea name="direccion" 
                              id="direccion" 
                              rows="4"
                              required
                              class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                              placeholder="Calle, número, colonia, ciudad, estado, código postal...">{{ old('direccion') }}</textarea>
                    @error('direccion')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Información -->
                <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg mb-6 transition-colors duration-200">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-400 dark:text-blue-300 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Información</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                                Asegúrate de proporcionar información completa y precisa del proveedor. Esta información será utilizada para gestionar las entradas de inventario.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('proveedores.index') }}" 
                       class="px-4 py-2 bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection