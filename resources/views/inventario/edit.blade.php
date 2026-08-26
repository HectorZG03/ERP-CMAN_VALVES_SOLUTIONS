@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Editar producto</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $inventario->nombre_producto }} · {{ $inventario->economico }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('inventario.ajustes.index', ['inventario_id' => $inventario->id]) }}"
               class="inline-flex items-center rounded-md bg-purple-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-purple-700">
                Ver historial de ajustes
            </a>
            <a href="{{ route('inventario.index') }}"
               class="inline-flex items-center rounded-md bg-gray-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-700">
                Volver
            </a>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Información del producto</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Este formulario solamente modifica los datos descriptivos.
            </p>
        </div>

        <form method="POST" action="{{ route('inventario.update', $inventario) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="categoria" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Categoría</label>
                    <input type="text" name="categoria" id="categoria" required
                           value="{{ old('categoria', $inventario->categoria) }}"
                           class="block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('categoria')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="economico" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Económico</label>
                    <input type="text" name="economico" id="economico" required
                           value="{{ old('economico', $inventario->economico) }}"
                           class="block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('economico')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="nombre_producto" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del producto</label>
                    <input type="text" name="nombre_producto" id="nombre_producto" required
                           value="{{ old('nombre_producto', $inventario->nombre_producto) }}"
                           class="block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('nombre_producto')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="medida" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Unidad de medida</label>
                    <input type="text" name="medida" id="medida" required
                           value="{{ old('medida', $inventario->medida) }}"
                           class="block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('medida')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="ubicacion" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Ubicación</label>
                    <input type="text" name="ubicacion" id="ubicacion"
                           value="{{ old('ubicacion', $inventario->ubicacion) }}"
                           placeholder="Ingrese ubicación o déjelo como N/A"
                           class="block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('ubicacion')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('inventario.index') }}"
                   class="rounded-md bg-gray-300 px-4 py-2 font-medium text-gray-700 transition-colors hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-md bg-blue-600 px-4 py-2 font-bold text-white transition-colors hover:bg-blue-700">
                    Actualizar producto
                </button>
            </div>
        </form>
    </div>

    <div id="ajuste-inventario"
         data-existencia="{{ $inventario->existencia }}"
         data-valor-total="{{ $inventario->precio_total }}"
         class="rounded-lg border border-amber-200 bg-white p-6 shadow dark:border-amber-800 dark:bg-gray-800">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Ajustar inventario</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Registra una corrección de existencias o una revaluación. La operación quedará en el historial.
                </p>
            </div>
            <span class="inline-flex self-start rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                Solo almacén
            </span>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-700">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Existencia actual</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $inventario->existencia }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $inventario->medida }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-700">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Costo promedio</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($inventario->getPrecioPromedio(), 2) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">por {{ $inventario->medida }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-700">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Valor total</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($inventario->precio_total, 2) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">valor registrado</p>
            </div>
        </div>

        <form id="form-ajuste-inventario" method="POST" action="{{ route('inventario.ajustes.store', $inventario) }}">
            @csrf

            <fieldset>
                <legend class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de operación</legend>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-300 p-4 transition-colors hover:border-blue-500 dark:border-gray-600">
                        <input type="radio" name="operacion" value="stock"
                               {{ old('operacion', 'stock') === 'stock' ? 'checked' : '' }}
                               class="mt-1 text-blue-600 focus:ring-blue-500">
                        <span>
                            <span class="block font-medium text-gray-900 dark:text-white">Corregir existencia</span>
                            <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">Establece la cantidad física real.</span>
                        </span>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-300 p-4 transition-colors hover:border-purple-500 dark:border-gray-600">
                        <input type="radio" name="operacion" value="revaluacion"
                               {{ old('operacion') === 'revaluacion' ? 'checked' : '' }}
                               class="mt-1 text-purple-600 focus:ring-purple-500">
                        <span>
                            <span class="block font-medium text-gray-900 dark:text-white">Revaluar costo</span>
                            <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">Cambia el costo de todas las existencias sin modificar la cantidad.</span>
                        </span>
                    </label>
                </div>
                @error('operacion')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </fieldset>

            <div id="campos-ajuste-stock" class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="nueva_existencia" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva existencia física</label>
                    <input type="number" name="nueva_existencia" id="nueva_existencia" min="0" step="1"
                           value="{{ old('nueva_existencia', $inventario->existencia) }}"
                           class="block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <p id="texto-diferencia" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Indica el resultado del conteo físico.</p>
                    @error('nueva_existencia')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div id="contenedor-costo-ajuste">
                    <label for="costo_unitario_ajuste" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Costo unitario de las unidades agregadas</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                        <input type="number" name="costo_unitario_ajuste" id="costo_unitario_ajuste" min="0.01" step="0.01"
                               value="{{ old('costo_unitario_ajuste', number_format($inventario->getPrecioPromedio(), 2, '.', '')) }}"
                               class="block w-full rounded-md border border-gray-300 bg-white pl-7 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Solo se aplica a la cantidad que se incorpora.</p>
                    @error('costo_unitario_ajuste')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div id="campos-revaluacion" class="mt-6 hidden">
                <label for="nuevo_costo_unitario" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nuevo costo unitario para todas las existencias</label>
                <div class="relative md:max-w-md">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                    <input type="number" name="nuevo_costo_unitario" id="nuevo_costo_unitario" min="0.01" step="0.01"
                           value="{{ old('nuevo_costo_unitario', number_format($inventario->getPrecioPromedio(), 2, '.', '')) }}"
                           class="block w-full rounded-md border border-gray-300 bg-white pl-7 text-gray-900 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">El nuevo valor total será existencia actual × nuevo costo unitario.</p>
                @error('nuevo_costo_unitario')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mt-6">
                <label for="motivo" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo del ajuste</label>
                <textarea name="motivo" id="motivo" rows="3" required maxlength="1000"
                          placeholder="Ejemplo: Diferencia encontrada durante el conteo físico"
                          class="block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('motivo') }}</textarea>
                @error('motivo')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200">Vista previa</h3>
                <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs text-blue-700 dark:text-blue-300">Diferencia de stock</p>
                        <p id="preview-diferencia" class="font-bold text-blue-900 dark:text-blue-100">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 dark:text-blue-300">Nuevo costo promedio</p>
                        <p id="preview-promedio" class="font-bold text-blue-900 dark:text-blue-100">${{ number_format($inventario->getPrecioPromedio(), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 dark:text-blue-300">Nuevo valor total</p>
                        <p id="preview-total" class="font-bold text-blue-900 dark:text-blue-100">${{ number_format($inventario->precio_total, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="rounded-md bg-amber-600 px-5 py-2.5 font-bold text-white transition-colors hover:bg-amber-700">
                    Registrar ajuste
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/inventario-ajustes.js') }}" defer></script>
@endsection
