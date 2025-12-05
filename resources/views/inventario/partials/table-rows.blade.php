@forelse($inventarios as $inventario)
<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 inventory-row" 
    data-stock="{{ $inventario->existencia > 0 ? 'with-stock' : 'without-stock' }}">
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="flex-shrink-0 h-10 w-10">
                <div class="h-10 w-10 rounded-lg bg-blue-500 dark:bg-blue-600 flex items-center justify-center shadow-sm">
                    <span class="text-sm font-medium text-white">
                        {{ substr($inventario->nombre_producto, 0, 2) }}
                    </span>
                </div>
            </div>
            <div class="ml-4">
                <div class="text-sm font-medium text-gray-800 dark:text-white">
                    {{ $inventario->nombre_producto }}
                </div>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
            {{ $inventario->economico }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
            {{ $inventario->categoria }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            {{ $inventario->medida }}
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            @if($inventario->existencia > 10)
                <div class="flex-shrink-0 h-2 w-2 bg-green-400 dark:bg-green-500 rounded-full mr-2"></div>
                <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $inventario->existencia }}</span>
            @elseif($inventario->existencia > 0)
                <div class="flex-shrink-0 h-2 w-2 bg-yellow-400 dark:bg-yellow-500 rounded-full mr-2"></div>
                <span class="text-sm font-semibold text-yellow-600 dark:text-yellow-400">{{ $inventario->existencia }}</span>
            @else
                <div class="flex-shrink-0 h-2 w-2 bg-red-400 dark:bg-red-500 rounded-full mr-2"></div>
                <span class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $inventario->existencia }}</span>
            @endif
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-1 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
            <span class="font-medium">${{ number_format($inventario->getPrecioPromedio(), 2) }}</span>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
        <span class="font-semibold text-blue-600 dark:text-blue-400">
            ${{ number_format($inventario->precio_total, 2) }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex items-center space-x-2">
            <!-- Botón Ver -->
            <a href="{{ route('inventario.show', $inventario) }}" 
               class="inline-flex items-center px-2 py-1 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 text-blue-800 dark:text-blue-300 text-xs font-medium rounded-md transition-colors duration-200 shadow-sm">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                </svg>
                Ver
            </a>

            @if(auth()->user()->canManageInventory())
                <!-- Botón Editar -->
                <a href="{{ route('inventario.edit', $inventario) }}" 
                   class="inline-flex items-center px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 hover:bg-yellow-200 dark:hover:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 text-xs font-medium rounded-md transition-colors duration-200 shadow-sm">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>

                <!-- Botón Eliminar -->
                <form method="POST" action="{{ route('inventario.destroy', $inventario) }}" 
                      class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto?\n\nEsta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-2 py-1 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-800 dark:text-red-300 text-xs font-medium rounded-md transition-colors duration-200 shadow-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-800 dark:text-white">No se encontraron productos</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Intenta con otros términos de búsqueda o filtros.</p>
    </td>
</tr>
@endforelse