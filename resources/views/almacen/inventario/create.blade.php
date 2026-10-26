@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- ============================================================
        ENCABEZADO
    ============================================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Agregar producto
                    </h1>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Registra un nuevo producto en el catálogo de inventario.
                    </p>
                </div>
            </div>
        </div>

        <a href="{{ route('inventario.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300
                  bg-white px-4 py-2.5 text-sm font-medium text-gray-700
                  shadow-sm transition hover:bg-gray-50
                  dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200
                  dark:hover:bg-gray-700">

            <svg class="h-4 w-4"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>

            Volver al inventario
        </a>
    </div>


    {{-- ============================================================
        FORMULARIO
    ============================================================ --}}
    <form method="POST" action="{{ route('inventario.store') }}">
        @csrf

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm
                    dark:border-gray-700 dark:bg-gray-800">

            {{-- ====================================================
                SECCIÓN: INFORMACIÓN DEL PRODUCTO
            ==================================================== --}}
            <div class="border-b border-gray-200 p-6 dark:border-gray-700">

                <div class="mb-6 flex items-start gap-3">

                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg
                                bg-gray-100 dark:bg-gray-700">

                        <svg class="h-5 w-5 text-gray-600 dark:text-gray-300"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                            Información del producto
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Define los datos que identifican y describen el producto.
                        </p>
                    </div>

                </div>


                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    {{-- Categoría --}}
                    <div>
                        <label for="categoria"
                               class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">

                            Categoría
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="text"
                               name="categoria"
                               id="categoria"
                               value="{{ old('categoria') }}"
                               required
                               maxlength="255"
                               placeholder="Ej. Herramientas, EPP, Consumibles"
                               class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5
                                      text-sm text-gray-900 shadow-sm outline-none transition
                                      placeholder:text-gray-400
                                      focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                      dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                      dark:placeholder:text-gray-500
                                      dark:focus:border-blue-400 dark:focus:ring-blue-400/20">

                        @error('categoria')
                            <p class="mt-2 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                <svg class="h-4 w-4 flex-shrink-0"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>

                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Económico --}}
                    <div>
                        <label for="economico"
                               class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">

                            Económico
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="text"
                               name="economico"
                               id="economico"
                               value="{{ old('economico') }}"
                               required
                               maxlength="255"
                               placeholder="Ej. ECO-001"
                               class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5
                                      text-sm text-gray-900 shadow-sm outline-none transition
                                      placeholder:text-gray-400
                                      focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                      dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                      dark:placeholder:text-gray-500
                                      dark:focus:border-blue-400 dark:focus:ring-blue-400/20">

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Identificador económico o código interno del producto.
                        </p>

                        @error('economico')
                            <p class="mt-2 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                <svg class="h-4 w-4 flex-shrink-0"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>

                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Nombre --}}
                    <div class="md:col-span-2">
                        <label for="nombre_producto"
                               class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">

                            Nombre del producto
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="text"
                               name="nombre_producto"
                               id="nombre_producto"
                               value="{{ old('nombre_producto') }}"
                               required
                               maxlength="255"
                               placeholder="Ej. Guantes de protección mecánica"
                               class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5
                                      text-sm text-gray-900 shadow-sm outline-none transition
                                      placeholder:text-gray-400
                                      focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                      dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                      dark:placeholder:text-gray-500
                                      dark:focus:border-blue-400 dark:focus:ring-blue-400/20">

                        @error('nombre_producto')
                            <p class="mt-2 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                <svg class="h-4 w-4 flex-shrink-0"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>

                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Unidad de medida --}}
                    <div>
                        <label for="medida"
                               class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">

                            Unidad de medida
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="text"
                               name="medida"
                               id="medida"
                               value="{{ old('medida') }}"
                               required
                               maxlength="255"
                               placeholder="Ej. Piezas, Kg, Litros"
                               class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5
                                      text-sm text-gray-900 shadow-sm outline-none transition
                                      placeholder:text-gray-400
                                      focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                      dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                      dark:placeholder:text-gray-500
                                      dark:focus:border-blue-400 dark:focus:ring-blue-400/20">

                        @error('medida')
                            <p class="mt-2 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                                <svg class="h-4 w-4 flex-shrink-0"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>

                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>
            </div>


            {{-- ====================================================
                SECCIÓN: UBICACIÓN
            ==================================================== --}}
            <div class="border-b border-gray-200 p-6 dark:border-gray-700">

                <div class="mb-6 flex items-start gap-3">

                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg
                                bg-gray-100 dark:bg-gray-700">

                        <svg class="h-5 w-5 text-gray-600 dark:text-gray-300"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M17.657 16.657L13.414 21a2 2 0 01-2.828 0l-4.243-4.343a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>

                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                            Ubicación
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Indica dónde se encuentra físicamente el producto.
                        </p>
                    </div>

                </div>


                <div class="max-w-2xl">

                    <label for="ubicacion"
                           class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">

                        Ubicación
                        <span class="text-xs font-normal text-gray-400 dark:text-gray-500">
                            (opcional)
                        </span>
                    </label>

                    <input type="text"
                           name="ubicacion"
                           id="ubicacion"
                           value="{{ old('ubicacion') }}"
                           maxlength="255"
                           placeholder="Ej. Almacén principal, Rack A-01"
                           class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5
                                  text-sm text-gray-900 shadow-sm outline-none transition
                                  placeholder:text-gray-400
                                  focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                  dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                  dark:placeholder:text-gray-500
                                  dark:focus:border-blue-400 dark:focus:ring-blue-400/20">

                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Si se deja vacío, el sistema registrará la ubicación como
                        <span class="font-medium text-gray-700 dark:text-gray-300">N/A</span>.
                    </p>

                    @error('ubicacion')
                        <p class="mt-2 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                            <svg class="h-4 w-4 flex-shrink-0"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>

                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            {{-- ====================================================
                INFORMACIÓN DEL INVENTARIO
            ==================================================== --}}
            <div class="bg-blue-50/70 p-6 dark:bg-blue-900/10">

                <div class="flex items-start gap-3">

                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg
                                bg-blue-100 dark:bg-blue-900/30">

                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-sm font-semibold text-blue-900 dark:text-blue-300">
                            Control de inventario
                        </h2>

                        <div class="mt-2 space-y-1.5 text-sm text-blue-800 dark:text-blue-200">

                            <p>
                                El producto se registrará inicialmente con
                                <strong>existencia de 0</strong>.
                            </p>

                            <p>
                                Para agregar mercancía al inventario utiliza el módulo de
                                <strong>Entradas</strong>.
                            </p>

                            <p>
                                Los movimientos de inventario quedarán registrados en el
                                historial correspondiente.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ====================================================
                ACCIONES
            ==================================================== --}}
            <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50
                        p-6 sm:flex-row sm:items-center sm:justify-end
                        dark:border-gray-700 dark:bg-gray-800/80">

                <a href="{{ route('inventario.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300
                          bg-white px-5 py-2.5 text-sm font-medium text-gray-700
                          shadow-sm transition hover:bg-gray-100
                          dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200
                          dark:hover:bg-gray-600">

                    Cancelar
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg
                               bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white
                               shadow-sm transition hover:bg-blue-700
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                               dark:focus:ring-offset-gray-800">

                    <svg class="h-4 w-4"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>

                    Guardar producto
                </button>

            </div>

        </div>

    </form>

</div>
@endsection