@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Editar Cliente</h1>
        <a href="{{ route('clientes.index') }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Volver
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('clientes.update', $cliente) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">
                        Nombre del Cliente
                    </label>
                    <input type="text" name="nombre" id="nombre" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('nombre', $cliente->nombre) }}">
                </div>

                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700">
                        Área
                    </label>
                    <input type="text" name="area" id="area" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('area', $cliente->area) }}">
                </div>

                <div>
                    <label for="cedula" class="block text-sm font-medium text-gray-700">
                        Cédula/Identificación
                    </label>
                    <input type="text" name="cedula" id="cedula" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('cedula', $cliente->cedula) }}">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <input type="email" name="email" id="email" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('email', $cliente->email) }}">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" 
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Actualizar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection