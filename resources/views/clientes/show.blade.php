@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Detalle del Cliente</h1>
        <div class="space-x-2">
            <a href="{{ route('clientes.edit', $cliente) }}" 
               class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Editar
            </a>
            <a href="{{ route('clientes.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre del Cliente</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $cliente->nombre }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Área</label>
                    <span class="mt-1 px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                        {{ $cliente->area }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cédula/Identificación</label>
                    <p class="mt-1 text-gray-900">{{ $cliente->cedula }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-gray-900">{{ $cliente->email }}</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total de Salidas</label>
                    <p class="mt-1 text-2xl font-bold text-red-600">
                        {{ $cliente->salidas->count() }}
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor Total Adquirido</label>
                    <p class="mt-1 text-xl font-semibold text-green-600">
                        ${{ number_format($cliente->salidas->sum('total_con_iva'), 2) }}
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                    <p class="mt-1 text-gray-900">{{ $cliente->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Salidas -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Últimas Salidas</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Producto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Cantidad</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($cliente->salidas()->latest()->limit(10)->get() as $salida)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $salida->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $salida->inventario->nombre_producto }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $salida->cantidad }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">${{ number_format($salida->total_con_iva, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection