@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Salidas de Material</h1>
        @if(auth()->user()->canManageInventory())
            <a href="{{ route('salidas.create') }}" 
               class="bg-red-500 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Salida
            </a>
        @endif
    </div>

    <!-- Filtros informativos -->
    @if(auth()->user()->canManageInventory())
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 transition-colors duration-200">
        <div class="flex flex-wrap gap-4 items-center">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Vista:</span>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                Todas las salidas
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                • Total: {{ $salidas->total() }} salidas
            </span>
        </div>
    </div>
    @endif

    <!-- Tabla principal -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md transition-colors duration-200">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Fecha
                            </th>
                            {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Producto
                            </th> --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Cantidad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total con IVA
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Usuario
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($salidas as $salida)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if($salida->created_at)
                                    {{ $salida->created_at->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                @endif
                            </td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap">
                                @if($salida->inventario)
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $salida->inventario->nombre_producto }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $salida->inventario->categoria }}
                                    </div>
                                @else
                                    <div class="text-sm font-medium text-red-600 dark:text-red-400">
                                        Producto eliminado
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        ID: {{ $salida->inventario_id }}
                                    </div>
                                @endif
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($salida->cliente)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-red-500 dark:bg-red-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ substr($salida->cliente->nombre, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $salida->cliente->nombre }}
                                            </div>
                                            @if($salida->cliente->area)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $salida->cliente->area }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm font-medium text-red-600 dark:text-red-400">
                                        Cliente eliminado
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H3"/>
                                    </svg>
                                    <span class="font-medium text-red-600 dark:text-red-400">{{ $salida->cantidad }}</span>
                                    @if($salida->inventario)
                                        {{ $salida->inventario->medida }}
                                    @else
                                        unidades
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    <span class="font-semibold text-green-600 dark:text-green-400">
                                        ${{ number_format($salida->total_con_iva, 2) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if($salida->user)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-6 w-6">
                                            <div class="h-6 w-6 rounded-full bg-gray-500 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-xs font-medium text-white">
                                                    {{ substr($salida->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-2">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $salida->user->name }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Usuario eliminado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- Botón Ver Detalles -->
                                    <a href="{{ route('salidas.show', $salida) }}" 
                                       class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-800 dark:text-red-300 text-xs font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Ver Detalles
                                    </a>

                                    <!-- Botón PDF -->
                                    <a href="{{ route('salidas.pdf', $salida) }}" 
                                       class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-800 dark:text-blue-300 text-xs font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H3M5 12h14"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay salidas</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No se encontraron salidas de material.</p>
                                @if(auth()->user()->canManageInventory())
                                <div class="mt-6">
                                    <a href="{{ route('salidas.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Nueva Salida
                                    </a>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $salidas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection