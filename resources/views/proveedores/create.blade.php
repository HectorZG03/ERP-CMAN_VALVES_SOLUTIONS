
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Cabecera -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('proveedores.index') }}"
               class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
               title="Volver a proveedores"
               aria-label="Volver a proveedores">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <div>
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <a href="{{ route('proveedores.index') }}"
                       class="transition hover:text-blue-600 dark:hover:text-blue-400">
                        Proveedores
                    </a>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5l7 7-7 7"/>
                    </svg>
                    <span>Nuevo registro</span>
                </div>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                    Nuevo proveedor
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Registra los datos generales y la clasificación del proveedor.
                </p>
            </div>
        </div>

        <div class="hidden items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 dark:border-blue-900 dark:bg-blue-900/30 dark:text-blue-300 sm:flex">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Alta de proveedor
        </div>
    </div>

    <!-- Errores de validación -->
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/60 dark:bg-red-900/20">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600 dark:text-red-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 2.57h16.94A2 2 0 0022.18 18L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-red-800 dark:text-red-300">
                        Revisa la información ingresada
                    </p>
                    <ul class="mt-1 list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-400">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Formulario -->
    <form method="POST"
          action="{{ route('proveedores.store') }}"
          class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @csrf

        <!-- Encabezado de la tarjeta -->
        <div class="border-b border-gray-200 bg-gray-50/80 px-5 py-5 dark:border-gray-700 dark:bg-gray-800/80 sm:px-8">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m6-10a4 4 0 100-8 4 4 0 000 8zm10 1v6m3-3h-6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                        Información del proveedor
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Completa los campos correspondientes.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-8 p-5 sm:p-8">

            <!-- Sección: Datos generales -->
            <section>
                <div class="mb-5 flex items-center gap-2">
                    <span class="h-5 w-1 rounded-full bg-blue-600"></span>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                        Datos generales
                    </h3>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <!-- Nombre -->
                    <div class="md:col-span-2">
                        <label for="proveedor"
                               class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre del proveedor
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="proveedor"
                            id="proveedor"
                            value="{{ old('proveedor') }}"
                            maxlength="255"
                            required
                            autofocus
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-400"
                            placeholder="Ej. Distribuidora ABC S.A. de C.V."
                        >
                        @error('proveedor')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label for="categoria"
                               class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Categoría
                            <span class="ml-1 text-xs font-normal text-gray-400">Opcional</span>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M7 7h.01M3 3h7l11 11-7 7L3 10V3z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="categoria"
                                id="categoria"
                                value="{{ old('categoria') }}"
                                maxlength="100"
                                class="block w-full rounded-xl border border-gray-300 bg-white py-3 pl-11 pr-4 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-400"
                                placeholder="Ej. Servicios, Productos..."
                            >
                        </div>
                        <p class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-400">
                            Escribe la categoría que corresponda. Puedes agregar nuevas categorías libremente.
                        </p>
                        @error('categoria')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Económico -->
                    <div>
                        <label for="economico"
                               class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Número económico
                            <span class="ml-1 text-xs font-normal text-gray-400">Opcional</span>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M7 7h10M7 12h10M7 17h10M4 4h16v16H4z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="economico"
                                id="economico"
                                value="{{ old('economico') }}"
                                maxlength="255"
                                class="block w-full rounded-xl border border-gray-300 bg-white py-3 pl-11 pr-4 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-400"
                                placeholder="Ej. PROV-001"
                            >
                        </div>
                        @error('economico')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Separador -->
            <div class="border-t border-gray-100 dark:border-gray-700"></div>

            <!-- Sección: Ubicación -->
            <section>
                <div class="mb-5 flex items-center gap-2">
                    <span class="h-5 w-1 rounded-full bg-blue-600"></span>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                        Ubicación y contacto
                    </h3>
                </div>

                <div>
                    <label for="direccion"
                           class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Dirección
                        <span class="ml-1 text-xs font-normal text-gray-400">Opcional</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute left-3.5 top-3.5">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <textarea
                            name="direccion"
                            id="direccion"
                            rows="3"
                            maxlength="255"
                            class="block w-full resize-y rounded-xl border border-gray-300 bg-white py-3 pl-11 pr-4 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-400"
                            placeholder="Calle, número, colonia, ciudad, estado y código postal..."
                        >{{ old('direccion') }}</textarea>
                    </div>
                    @error('direccion')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <!-- Nota informativa -->
            <div class="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/80 p-4 dark:border-blue-900/60 dark:bg-blue-900/20">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M12 22a10 10 0 110-20 10 10 0 010 20z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-blue-900 dark:text-blue-200">
                        Consideraciones
                    </p>
                    <p class="mt-1 text-sm leading-6 text-blue-800 dark:text-blue-300">
                        La información registrada permitirá identificar al proveedor y facilitar la gestión de las entradas de inventario. Verifica los datos antes de guardar.
                    </p>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-5 py-5 dark:border-gray-700 dark:bg-gray-800/80 sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                <span class="text-red-500">*</span> Campo obligatorio
            </p>

            <div class="flex flex-col-reverse gap-3 sm:flex-row">
                <a href="{{ route('proveedores.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelar
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30 active:scale-[0.98] dark:bg-blue-500 dark:hover:bg-blue-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar proveedor
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
