@extends('layouts.app')

@section('content')

@php
    $existencia = (int) $inventario->existencia;
    $precioPromedio = $inventario->getPrecioPromedio();
    $valorTotal = $inventario->precio_total ?? 0;
@endphp

<div class="mx-auto max-w-6xl space-y-6">

    {{-- Encabezado --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventario.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Inventario
                </a>

                <span class="text-gray-300 dark:text-gray-600">/</span>

                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Editar
                </span>
            </div>

            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                Editar producto
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $inventario->nombre_producto }}
                · Económico {{ $inventario->economico }}
            </p>
        </div>

        <a href="{{ route('inventario.ajustes.index', ['inventario_id' => $inventario->id]) }}"
           class="inline-flex items-center justify-center rounded-lg border border-gray-300
                  bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm
                  hover:bg-gray-50
                  dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            Ver historial
        </a>

    </div>


    {{-- ============================================================
         INFORMACIÓN DEL PRODUCTO
    ============================================================ --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm
                dark:border-gray-700 dark:bg-gray-800">

        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">
                Información del producto
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Modifica los datos descriptivos del producto.
            </p>
        </div>


        <form method="POST" action="{{ route('inventario.update', $inventario) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                {{-- Categoría --}}
                <div>
                    <label for="categoria"
                           class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Categoría
                    </label>

                    <input type="text"
                           name="categoria"
                           id="categoria"
                           value="{{ old('categoria', $inventario->categoria) }}"
                           required
                           maxlength="255"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5
                                  text-sm text-gray-900 outline-none
                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                  dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                    @error('categoria')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Económico --}}
                <div>
                    <label for="economico"
                           class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Económico
                    </label>

                    <input type="text"
                           name="economico"
                           id="economico"
                           value="{{ old('economico', $inventario->economico) }}"
                           required
                           maxlength="255"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5
                                  text-sm text-gray-900 outline-none
                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                  dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                    @error('economico')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Nombre --}}
                <div class="md:col-span-2">
                    <label for="nombre_producto"
                           class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nombre del producto
                    </label>

                    <input type="text"
                           name="nombre_producto"
                           id="nombre_producto"
                           value="{{ old('nombre_producto', $inventario->nombre_producto) }}"
                           required
                           maxlength="255"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5
                                  text-sm text-gray-900 outline-none
                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                  dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                    @error('nombre_producto')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Unidad --}}
                <div>
                    <label for="medida"
                           class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Unidad de medida
                    </label>

                    <input type="text"
                           name="medida"
                           id="medida"
                           value="{{ old('medida', $inventario->medida) }}"
                           required
                           maxlength="255"
                           placeholder="Ej. Piezas, Kg, Litros"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5
                                  text-sm text-gray-900 outline-none
                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                  dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                    @error('medida')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Ubicación --}}
                <div>
                    <label for="ubicacion"
                           class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Ubicación
                    </label>

                    <input type="text"
                           name="ubicacion"
                           id="ubicacion"
                           value="{{ old('ubicacion', $inventario->ubicacion) }}"
                           maxlength="255"
                           placeholder="Ej. Almacén principal"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5
                                  text-sm text-gray-900 outline-none
                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                  dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                    @error('ubicacion')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>


            <div class="flex justify-end gap-2 border-t border-gray-200 px-5 py-4
                        dark:border-gray-700">

                <a href="{{ route('inventario.index') }}"
                   class="rounded-lg border border-gray-300 bg-white px-4 py-2.5
                          text-sm font-medium text-gray-700 hover:bg-gray-50
                          dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200
                          dark:hover:bg-gray-600">
                    Cancelar
                </a>

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold
                               text-white hover:bg-blue-700 focus:outline-none
                               focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                               dark:focus:ring-offset-gray-800">
                    Guardar cambios
                </button>

            </div>

        </form>
    </div>


    {{-- ============================================================
         AJUSTE DE INVENTARIO
    ============================================================ --}}
    <div id="ajuste-inventario"
         data-existencia="{{ $existencia }}"
         data-valor-total="{{ $valorTotal }}"
         class="rounded-xl border border-gray-200 bg-white shadow-sm
                dark:border-gray-700 dark:bg-gray-800">

        <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4
                    sm:flex-row sm:items-center sm:justify-between
                    dark:border-gray-700">

            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Ajustar inventario
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Las modificaciones de existencia quedan registradas en el historial.
                </p>
            </div>

            <span class="w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-medium
                         text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                Control de almacén
            </span>

        </div>


        <div class="p-5">

            {{-- Resumen --}}
            <div class="mb-5 grid grid-cols-1 divide-y divide-gray-200 rounded-lg border
                        border-gray-200 sm:grid-cols-3 sm:divide-x sm:divide-y-0
                        dark:divide-gray-700 dark:border-gray-700">

                <div class="px-4 py-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Existencia
                    </p>

                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ number_format($existencia) }}
                        <span class="text-sm font-normal text-gray-500">
                            {{ $inventario->medida }}
                        </span>
                    </p>
                </div>

                <div class="px-4 py-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Costo promedio
                    </p>

                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                        ${{ number_format($precioPromedio, 2) }}
                    </p>
                </div>

                <div class="px-4 py-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Valor total
                    </p>

                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                        ${{ number_format($valorTotal, 2) }}
                    </p>
                </div>

            </div>


            {{-- Formulario --}}
            <form id="form-ajuste-inventario"
                  method="POST"
                  action="{{ route('inventario.ajustes.store', $inventario) }}">

                @csrf


                {{-- Operación --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Operación
                    </label>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border
                                      border-gray-300 p-3 transition
                                      hover:border-blue-400
                                      dark:border-gray-600 dark:hover:border-blue-500">

                            <input type="radio"
                                   name="operacion"
                                   value="stock"
                                   {{ old('operacion', 'stock') === 'stock' ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500">

                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    Ajustar existencia
                                </p>

                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Corregir la cantidad física.
                                </p>
                            </div>

                        </label>


                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border
                                      border-gray-300 p-3 transition
                                      hover:border-purple-400
                                      dark:border-gray-600 dark:hover:border-purple-500">

                            <input type="radio"
                                   name="operacion"
                                   value="revaluacion"
                                   {{ old('operacion') === 'revaluacion' ? 'checked' : '' }}
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500">

                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    Revaluar costo
                                </p>

                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Cambiar el costo unitario.
                                </p>
                            </div>

                        </label>

                    </div>

                    @error('operacion')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Ajuste de existencia --}}
                <div id="campos-ajuste-stock"
                     class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <div>
                        <label for="nueva_existencia"
                               class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nueva existencia
                        </label>

                        <input type="number"
                               name="nueva_existencia"
                               id="nueva_existencia"
                               min="0"
                               step="1"
                               value="{{ old('nueva_existencia', $existencia) }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5
                                      text-sm text-gray-900 outline-none
                                      focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                      dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                        <p id="texto-diferencia"
                           class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        </p>

                        @error('nueva_existencia')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    <div id="contenedor-costo-ajuste">
                        <label for="costo_unitario_ajuste"
                               class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Costo unitario de entrada
                        </label>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-sm text-gray-500">
                                $
                            </span>

                            <input type="number"
                                   name="costo_unitario_ajuste"
                                   id="costo_unitario_ajuste"
                                   min="0.01"
                                   step="0.01"
                                   value="{{ old('costo_unitario_ajuste', number_format($precioPromedio, 2, '.', '')) }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-7 pr-3
                                          text-sm text-gray-900 outline-none
                                          focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                          dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                        </div>

                        @error('costo_unitario_ajuste')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>


                {{-- Revaluación --}}
                <div id="campos-revaluacion"
                     class="mt-5 hidden">

                    <label for="nuevo_costo_unitario"
                           class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nuevo costo unitario
                    </label>

                    <div class="relative sm:max-w-md">

                        <span class="absolute inset-y-0 left-3 flex items-center text-sm text-gray-500">
                            $
                        </span>

                        <input type="number"
                               name="nuevo_costo_unitario"
                               id="nuevo_costo_unitario"
                               min="0.01"
                               step="0.01"
                               value="{{ old('nuevo_costo_unitario', number_format($precioPromedio, 2, '.', '')) }}"
                               class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-7 pr-3
                                      text-sm text-gray-900 outline-none
                                      focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20
                                      dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                    </div>

                    @error('nuevo_costo_unitario')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Motivo --}}
                <div class="mt-5">

                    <label for="motivo"
                           class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Motivo
                    </label>

                    <textarea name="motivo"
                              id="motivo"
                              rows="3"
                              maxlength="1000"
                              required
                              placeholder="Describe brevemente el motivo del ajuste..."
                              class="w-full resize-y rounded-lg border border-gray-300 bg-white px-3 py-2.5
                                     text-sm text-gray-900 outline-none
                                     focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20
                                     dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('motivo') }}</textarea>

                    @error('motivo')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Vista previa --}}
                <div class="mt-5 rounded-lg bg-gray-50 p-4 dark:bg-gray-700/40">

                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">

                        <div>
                            <span class="text-gray-500 dark:text-gray-400">
                                Diferencia:
                            </span>

                            <strong id="preview-diferencia"
                                    class="ml-1 text-gray-900 dark:text-white">
                                0
                            </strong>
                        </div>

                        <div>
                            <span class="text-gray-500 dark:text-gray-400">
                                Costo promedio:
                            </span>

                            <strong id="preview-promedio"
                                    class="ml-1 text-gray-900 dark:text-white">
                                ${{ number_format($precioPromedio, 2) }}
                            </strong>
                        </div>

                        <div>
                            <span class="text-gray-500 dark:text-gray-400">
                                Valor total:
                            </span>

                            <strong id="preview-total"
                                    class="ml-1 text-gray-900 dark:text-white">
                                ${{ number_format($valorTotal, 2) }}
                            </strong>
                        </div>

                    </div>

                </div>


                {{-- Botón --}}
                <div class="mt-5 flex justify-end">

                    <button type="submit"
                            class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold
                                   text-white hover:bg-amber-700
                                   focus:outline-none focus:ring-2 focus:ring-amber-500
                                   focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        Registrar ajuste
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script src="{{ asset('js/inventario-ajustes.js') }}" defer></script>

@endsection