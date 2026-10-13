@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">

    @if(session('entrada_reciente'))
        <section class="rounded-xl border border-green-200 bg-green-50 p-5 dark:border-green-800 dark:bg-green-900/20">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-green-800 dark:text-green-300">
                        Entrada registrada correctamente
                    </h2>

                    <p class="mt-2 text-sm text-green-700 dark:text-green-400">
                        Factura #{{ session('entrada_reciente.numero_factura') }}
                        · Proveedor: {{ session('entrada_reciente.proveedor_nombre') }}
                        · {{ session('entrada_reciente.fecha') }}
                    </p>

                    <p class="mt-1 text-sm text-green-700 dark:text-green-400">
                        {{ session('entrada_reciente.cantidad_productos') }} producto(s)
                        · {{ session('entrada_reciente.cantidad_total') }} unidad(es)
                    </p>

                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-5">
                        <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Subtotal</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                ${{ number_format(session('entrada_reciente.subtotal'), 2) }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Descuento</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                − ${{ number_format(session('entrada_reciente.descuento_global'), 2) }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Base con descuento</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                ${{ number_format(session('entrada_reciente.subtotal_con_descuento'), 2) }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                            <p class="text-xs text-gray-500 dark:text-gray-400">IVA (16%)</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                ${{ number_format(session('entrada_reciente.iva'), 2) }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                            <p class="mt-1 font-bold text-green-600 dark:text-green-400">
                                ${{ number_format(session('entrada_reciente.total'), 2) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('entradas.create') }}"
                       class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Nueva entrada
                    </a>

                    <a href="{{ route('entradas.show', session('entrada_reciente.id')) }}"
                       class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                        Ver detalles
                    </a>
                </div>
            </div>
        </section>
    @endif

    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">
                Inventario / Entradas
            </p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                Nueva entrada de materiales
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Registra el proveedor, los materiales recibidos y el importe de la factura.
            </p>
        </div>

        <a href="{{ route('entradas.index') }}"
           class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            Volver a entradas
        </a>
    </header>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
            <p class="font-semibold text-red-800 dark:text-red-300">
                Revisa los datos del formulario
            </p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-400">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('entradas.store') }}"
        id="entradaForm"
        data-buscar-proveedores="{{ route('entradas.buscar-proveedores') }}"
        data-buscar-productos="{{ route('entradas.buscar-productos') }}"
    >
        @csrf

        <section class="overflow-visible rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 font-bold text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                        1
                    </span>
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Datos de la entrada
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Selecciona el proveedor y la fecha de recepción.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 sm:p-6">
                <div class="relative">
                    <label for="proveedor_search" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Proveedor <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="proveedor_search"
                        autocomplete="off"
                        placeholder="Escribe para buscar un proveedor..."
                        aria-autocomplete="list"
                        aria-controls="proveedor_resultados"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >

                    <input type="hidden" name="proveedor_id" id="proveedor_id" value="{{ old('proveedor_id') }}">

                    <div id="proveedor_resultados"
                         role="listbox"
                         class="absolute left-0 right-0 z-50 mt-1 hidden max-h-64 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800">
                    </div>

                    <p id="proveedor_seleccionado" class="mt-2 hidden text-sm text-green-600 dark:text-green-400"></p>

                    @error('proveedor_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_entrada" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Fecha de entrada <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="date"
                        name="fecha_entrada"
                        id="fecha_entrada"
                        required
                        value="{{ old('fecha_entrada', date('Y-m-d')) }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >

                    @error('fecha_entrada')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="observaciones" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Observaciones <span class="font-normal text-gray-400">(opcional)</span>
                    </label>

                    <textarea
                        name="observaciones"
                        id="observaciones"
                        rows="2"
                        maxlength="500"
                        placeholder="Agrega alguna observación sobre la recepción..."
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >{{ old('observaciones') }}</textarea>

                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="mt-6 overflow-visible rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-col gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 font-bold text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                        2
                    </span>
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Materiales recibidos
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Agrega cada producto con su cantidad y precio unitario.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    id="agregarMaterialBtn"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar material
                </button>
            </div>

            <div class="p-5 sm:p-6">
                <div id="materiales-container" class="space-y-4"></div>

                <div id="materiales-vacio" class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center dark:border-gray-600">
                    <p class="font-medium text-gray-900 dark:text-white">Aún no has agregado materiales</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Selecciona «Agregar material» para comenzar.
                    </p>
                </div>

                @error('materiales')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('materiales.*.inventario_id')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('materiales.*.cantidad')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('materiales.*.precio_unitario')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <section class="mt-6 rounded-xl border border-blue-200 bg-blue-50/70 p-5 dark:border-blue-900 dark:bg-blue-900/10 sm:p-6">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 font-bold text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                    3
                </span>
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">Resumen de importes</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        El descuento se aplica al subtotal antes de calcular el IVA.
                    </p>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="space-y-3 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Productos agregados
                        </span>
                        <span id="total-materiales" class="font-semibold text-blue-700 dark:text-blue-300">0</span>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span id="subtotal-total" class="font-semibold text-gray-900 dark:text-white">$0.00</span>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Descuento global</span>
                        <span id="descuento-total" class="font-semibold text-gray-900 dark:text-white">− $0.00</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 border-t border-gray-200 pt-3 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Base con descuento</span>
                        <span id="subtotal-con-descuento" class="font-semibold text-gray-900 dark:text-white">$0.00</span>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-600 dark:text-gray-400">IVA (16%)</span>
                        <span id="iva-total" class="font-semibold text-gray-900 dark:text-white">$0.00</span>
                    </div>
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-900 dark:bg-amber-900/20 lg:col-span-2">
                    <label for="descuento_global" class="block text-sm font-semibold text-gray-800 dark:text-gray-200">
                        Descuento global <span class="font-normal text-gray-500 dark:text-gray-400">(opcional, monto fijo)</span>
                    </label>

                    <div class="relative mt-2 max-w-sm">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-medium text-gray-500 dark:text-gray-400">$</span>
                        <input
                            type="number"
                            name="descuento_global"
                            id="descuento_global"
                            min="0"
                            step="0.01"
                            value="{{ old('descuento_global', '0.00') }}"
                            inputmode="decimal"
                            aria-describedby="descuento-ayuda descuento-global-error"
                            class="block w-full rounded-lg border border-amber-300 bg-white py-3 pl-8 pr-3 text-lg font-semibold text-gray-900 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-amber-700 dark:bg-gray-800 dark:text-white"
                        >
                    </div>

                    <p id="descuento-ayuda" class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                        Déjalo en cero si la factura no incluye descuento. El monto debe ser menor que el subtotal.
                    </p>

                    <p id="descuento-global-error" class="mt-2 hidden text-sm font-medium text-red-600 dark:text-red-400" role="alert">
                        El descuento debe ser menor que el subtotal de los productos.
                    </p>

                    @error('descuento_global')
                        <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-2 rounded-lg bg-green-700 px-5 py-4 text-white sm:flex-row sm:items-center sm:justify-between dark:bg-green-800">
                <div>
                    <p class="text-sm text-green-100">Total de la entrada</p>
                    <p class="text-xs text-green-100">Base con descuento + IVA del 16%</p>
                </div>
                <p id="total-general" class="text-3xl font-bold">$0.00</p>
            </div>

            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                Los importes son una estimación del formulario. El servidor recalcula y valida los totales al guardar.
            </p>
        </section>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <button
                type="button"
                id="limpiarFormularioBtn"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                Limpiar formulario
            </button>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('entradas.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    Cancelar
                </a>

                <button
                    type="submit"
                    id="registrarEntradaBtn"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Registrar entrada
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/inventario/entradas/create.js') }}" defer></script>
@endpush