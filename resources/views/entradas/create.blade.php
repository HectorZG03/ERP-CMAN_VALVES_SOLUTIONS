
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Resumen de la última entrada --}}
    @if(session('entrada_reciente'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-5 dark:border-green-800 dark:bg-green-900/20">
            <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
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

                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Subtotal</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                ${{ number_format(session('entrada_reciente.subtotal'), 2) }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">IVA (16%)</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                ${{ number_format(session('entrada_reciente.iva'), 2) }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                            <p class="mt-1 text-lg font-bold text-green-600 dark:text-green-400">
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
        </div>
    @endif

    {{-- Encabezado --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                INVENTARIO / ENTRADAS
            </p>

            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                Nueva entrada de materiales
            </h1>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Registra los materiales recibidos, su proveedor y sus costos.
            </p>
        </div>

        <a href="{{ route('entradas.index') }}"
           class="inline-flex items-center justify-center rounded-lg border border-gray-300
                  bg-white px-4 py-2.5 text-sm font-medium text-gray-700
                  transition hover:bg-gray-50
                  dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            Volver a entradas
        </a>
    </div>

    {{-- Errores generales --}}
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

        {{-- Información general --}}
        <section class="overflow-visible rounded-xl border border-gray-200 bg-white shadow-sm
                        dark:border-gray-700 dark:bg-gray-800">

            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    1. Información de la entrada
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Indica quién suministra los materiales y cuándo se reciben.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 p-5 sm:grid-cols-2 sm:p-6">

                {{-- Buscador de proveedores --}}
                <div class="relative">
                    <label for="proveedor_search"
                           class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Proveedor <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="proveedor_search"
                        autocomplete="off"
                        value="{{ old('proveedor_id') ? '' : '' }}"
                        placeholder="Escribe para buscar un proveedor..."
                        aria-autocomplete="list"
                        aria-controls="proveedor_resultados"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3
                               text-sm text-gray-900 outline-none transition
                               placeholder:text-gray-400 focus:border-blue-500 focus:ring-2
                               focus:ring-blue-500/20
                               dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >

                    <input
                        type="hidden"
                        name="proveedor_id"
                        id="proveedor_id"
                        value="{{ old('proveedor_id') }}"
                    >

                    <div
                        id="proveedor_resultados"
                        role="listbox"
                        class="absolute left-0 right-0 z-50 mt-1 hidden max-h-64
                               overflow-y-auto rounded-lg border border-gray-200
                               bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800"
                    ></div>

                    <p id="proveedor_seleccionado"
                       class="mt-2 hidden text-sm text-green-600 dark:text-green-400"></p>

                    @error('proveedor_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Fecha --}}
                <div>
                    <label for="fecha_entrada"
                           class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Fecha de entrada <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="date"
                        name="fecha_entrada"
                        id="fecha_entrada"
                        required
                        value="{{ old('fecha_entrada', date('Y-m-d')) }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3
                               text-sm text-gray-900 outline-none transition focus:border-blue-500
                               focus:ring-2 focus:ring-blue-500/20
                               dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >

                    @error('fecha_entrada')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Observaciones --}}
                <div class="sm:col-span-2">
                    <label for="observaciones"
                           class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Observaciones
                        <span class="font-normal text-gray-400">(opcional)</span>
                    </label>

                    <textarea
                        name="observaciones"
                        id="observaciones"
                        rows="3"
                        maxlength="500"
                        placeholder="Número de factura, condiciones de entrega u otras observaciones..."
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3
                               text-sm text-gray-900 outline-none transition
                               placeholder:text-gray-400 focus:border-blue-500 focus:ring-2
                               focus:ring-blue-500/20
                               dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >{{ old('observaciones') }}</textarea>

                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Productos --}}
        <section class="mt-6 overflow-visible rounded-xl border border-gray-200 bg-white shadow-sm
                        dark:border-gray-700 dark:bg-gray-800">

            <div class="flex flex-col gap-4 border-b border-gray-200 px-5 py-4
                        dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        2. Materiales recibidos
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Busca cada producto y registra la cantidad y el precio unitario.
                    </p>
                </div>

                <button
                    type="button"
                    id="agregarMaterialBtn"
                    class="inline-flex items-center justify-center gap-2 rounded-lg
                           bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white
                           transition hover:bg-blue-700 focus:outline-none focus:ring-2
                           focus:ring-blue-500 focus:ring-offset-2"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar material
                </button>
            </div>

            <div class="p-5 sm:p-6">
                <div id="materiales-container" class="space-y-4">
                    {{-- create.js agregará las líneas de materiales --}}
                </div>

                <div id="materiales-vacio"
                     class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center
                            dark:border-gray-600">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full
                                bg-gray-100 dark:bg-gray-700">
                        <svg class="h-6 w-6 text-gray-500 dark:text-gray-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>

                    <p class="mt-3 font-medium text-gray-900 dark:text-white">
                        Aún no has agregado materiales
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Selecciona «Agregar material» para comenzar.
                    </p>
                </div>

                @error('materiales')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">
                        {{ $message }}
                    </p>
                @enderror

                @error('materiales.*.inventario_id')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">
                        {{ $message }}
                    </p>
                @enderror

                @error('materiales.*.cantidad')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">
                        {{ $message }}
                    </p>
                @enderror

                @error('materiales.*.precio_unitario')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </section>

        {{-- Resumen de importes --}}
        <section class="mt-6 rounded-xl border border-blue-200 bg-blue-50/70 p-5
                        dark:border-blue-900 dark:bg-blue-900/10 sm:p-6">

            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                3. Resumen de la entrada
            </h2>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Productos</p>
                    <p id="total-materiales" class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-400">
                        0
                    </p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Subtotal</p>
                    <p id="subtotal-total" class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        $0.00
                    </p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">IVA (16%)</p>
                    <p id="iva-total" class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        $0.00
                    </p>
                </div>

                <div class="rounded-lg border border-green-200 bg-white p-4 dark:border-green-900 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total general</p>
                    <p id="total-general" class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">
                        $0.00
                    </p>
                </div>
            </div>

            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                Los importes mostrados son una estimación del formulario. El servidor debe
                validar y calcular los importes definitivos al guardar.
            </p>
        </section>

        {{-- Acciones --}}
        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5
                    dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">

            <button
                type="button"
                id="limpiarFormularioBtn"
                class="inline-flex items-center justify-center rounded-lg border
                       border-gray-300 bg-white px-5 py-2.5 text-sm font-medium
                       text-gray-700 transition hover:bg-gray-50
                       dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                Limpiar formulario
            </button>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a
                    href="{{ route('entradas.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border
                           border-gray-300 bg-white px-5 py-2.5 text-sm font-medium
                           text-gray-700 transition hover:bg-gray-50
                           dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    id="registrarEntradaBtn"
                    class="inline-flex items-center justify-center gap-2 rounded-lg
                           bg-green-600 px-6 py-2.5 text-sm font-semibold text-white
                           transition hover:bg-green-700 focus:outline-none focus:ring-2
                           focus:ring-green-500 focus:ring-offset-2"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
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
