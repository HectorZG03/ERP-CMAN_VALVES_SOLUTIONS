@forelse($inventarios as $inventario)

    @php

        $stock = (int) $inventario->existencia;

        if ($stock > 10) {

            $stockClass = 'text-green-600 dark:text-green-400';
            $stockDot = 'bg-green-500';
            $stockLabel = 'Disponible';
            $stockBadge = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';

        } elseif ($stock > 0) {

            $stockClass = 'text-yellow-600 dark:text-yellow-400';
            $stockDot = 'bg-yellow-500';
            $stockLabel = 'Stock bajo';
            $stockBadge = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300';

        } else {

            $stockClass = 'text-red-600 dark:text-red-400';
            $stockDot = 'bg-red-500';
            $stockLabel = 'Agotado';
            $stockBadge = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';

        }

        $nombreProducto = trim($inventario->nombre_producto ?? 'Producto');

        $avatar = strtoupper(substr($nombreProducto, 0, 2));

    @endphp


    <tr class="inventory-row transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40"
        data-stock="{{ $stock > 0 ? 'with-stock' : 'without-stock' }}">


        {{-- ========================================================
             PRODUCTO
        ========================================================= --}}
        <td class="px-5 py-4">

            <div class="flex min-w-0 items-center gap-3">

                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-xs font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-300">

                    {{ $avatar }}

                </div>


                <div class="min-w-0">

                    <a href="{{ route('inventario.show', $inventario) }}"
                       class="block truncate text-sm font-semibold text-gray-900 transition hover:text-blue-600 dark:text-white dark:hover:text-blue-400"
                       title="{{ $inventario->nombre_producto }}">

                        {{ $inventario->nombre_producto }}

                    </a>


                    <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">

                        ID #{{ str_pad($inventario->id, 4, '0', STR_PAD_LEFT) }}

                    </div>

                </div>

            </div>

        </td>


        {{-- ========================================================
             ECONÓMICO
        ========================================================= --}}
        <td class="px-3 py-4">

            <span class="block max-w-full truncate rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300"
                  title="{{ $inventario->economico }}">

                {{ $inventario->economico ?: 'Sin asignar' }}

            </span>

        </td>


        {{-- ========================================================
             CATEGORÍA
        ========================================================= --}}
        <td class="px-3 py-4">

            <span class="block max-w-full truncate rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-300"
                  title="{{ $inventario->categoria }}">

                {{ $inventario->categoria }}

            </span>

        </td>


        {{-- ========================================================
             MEDIDA
        ========================================================= --}}
        <td class="px-3 py-4">

            <div class="flex min-w-0 items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300">

                <svg class="h-4 w-4 flex-shrink-0 text-gray-400"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2zM9 7h6M9 11h6M9 15h4"/>

                </svg>

                <span class="truncate">
                    {{ $inventario->medida }}
                </span>

            </div>

        </td>


        {{-- ========================================================
             EXISTENCIA
        ========================================================= --}}
        <td class="px-3 py-4">

            <div class="flex items-center gap-2">

                <span class="h-2 w-2 flex-shrink-0 rounded-full {{ $stockDot }}"></span>

                <span class="font-bold {{ $stockClass }}">
                    {{ $stock }}
                </span>

                <span class="hidden rounded-md px-2 py-0.5 text-[10px] font-semibold xl:inline-flex {{ $stockBadge }}">
                    {{ $stockLabel }}
                </span>

            </div>

        </td>


        {{-- ========================================================
             PRECIO UNITARIO
        ========================================================= --}}
        <td class="px-3 py-4 text-right whitespace-nowrap">

            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                ${{ number_format($inventario->getPrecioPromedio(), 2) }}
            </span>

        </td>


        {{-- ========================================================
             ACCIONES
        ========================================================= --}}
        <td class="px-3 py-4">

            <div class="flex items-center justify-end gap-1">

                {{-- Ver --}}
                <a href="{{ route('inventario.show', $inventario) }}"
                   title="Ver producto"
                   aria-label="Ver producto"
                   class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-gray-500 transition hover:bg-blue-50 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-blue-900/20 dark:hover:text-blue-400">

                    <svg class="h-4 w-4"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>

                    </svg>

                </a>


                @if(auth()->user()->canManageInventory())

                    {{-- Editar --}}
                    <a href="{{ route('inventario.edit', $inventario) }}"
                       title="Editar producto"
                       aria-label="Editar producto"
                       class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-gray-500 transition hover:bg-yellow-50 hover:text-yellow-600 dark:text-gray-400 dark:hover:bg-yellow-900/20 dark:hover:text-yellow-400">

                        <svg class="h-4 w-4"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h5m4-14l2.5 2.5M14 4l6 6-8 8H9v-3l8-8z"/>

                        </svg>

                    </a>


                    {{-- Eliminar --}}
                    <form method="POST"
                          action="{{ route('inventario.destroy', $inventario) }}"
                          class="inline"
                          onsubmit="return confirm('¿Estás seguro de eliminar este producto?\n\nEsta acción no se puede deshacer.')">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                title="Eliminar producto"
                                aria-label="Eliminar producto"
                                class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-gray-500 transition hover:bg-red-50 hover:text-red-600 dark:text-gray-400 dark:hover:bg-red-900/20 dark:hover:text-red-400">

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v-5m-1-3V4a1 1 0 011-1h2a1 1 0 011 1v2m-8 0h10"/>

                            </svg>

                        </button>

                    </form>

                @endif

            </div>

        </td>

    </tr>


@empty

    <tr>

        <td colspan="7"
            class="px-6 py-16 text-center">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500">

                <svg class="h-7 w-7"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="1.7"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>

                </svg>

            </div>

            <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
                No se encontraron productos
            </h3>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Intenta modificar los términos de búsqueda o los filtros seleccionados.
            </p>

        </td>

    </tr>

@endforelse