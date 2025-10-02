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
            Editar Proveedor #{{ str_pad($proveedor->id, 4, '0', STR_PAD_LEFT) }}
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('proveedores.update', $proveedor) }}">
                @csrf
                @method('PUT')

                <!-- Nombre del Proveedor -->
                <div class="mb-6">
                    <label for="proveedor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nombre del Proveedor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="proveedor" 
                           id="proveedor" 
                           value="{{ old('proveedor', $proveedor->proveedor) }}"
                           required
                           class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
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
                           value="{{ old('economico', $proveedor->economico) }}"
                           
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
                              
                              class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">{{ old('direccion', $proveedor->direccion) }}</textarea>
                    @error('direccion')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Información de actualización -->
                <div class="bg-amber-50 dark:bg-amber-900/30 p-4 rounded-lg mb-6 transition-colors duration-200">
                    <div class="flex">
                        <svg class="w-5 h-5 text-amber-400 dark:text-amber-300 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-amber-800 dark:text-amber-300">Importante</h4>
                            <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                                Los cambios realizados afectarán todos los registros relacionados con este proveedor.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Información de registro -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6 transition-colors duration-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Creado:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">
                                {{ $proveedor->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Última actualización:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">
                                {{ $proveedor->updated_at->format('d/m/Y H:i') }}
                            </span>
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
                            class="px-4 py-2 bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Actualizar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection