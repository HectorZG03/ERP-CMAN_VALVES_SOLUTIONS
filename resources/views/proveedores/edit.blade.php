@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Editar Proveedor</h1>
        <a href="{{ route('proveedores.index') }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Volver
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('proveedores.update', $proveedor) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label for="proveedor" class="block text-sm font-medium text-gray-700">
                        Nombre del Proveedor
                    </label>
                    <input type="text" name="proveedor" id="proveedor" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('proveedor', $proveedor->proveedor) }}">
                </div>

                <div>
                    <label for="direccion" class="block text-sm font-medium text-gray-700">
                        Dirección
                    </label>
                    <textarea name="direccion" id="direccion" rows="3" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('direccion', $proveedor->direccion) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Actualizar Proveedor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection