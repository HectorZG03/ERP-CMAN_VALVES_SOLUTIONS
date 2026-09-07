@extends('layouts.app')

@section('content')
@php
    $tipoInicial = old(
        'tipo_solicitud',
        \App\Models\SolicitudMaterial::TIPO_ESTANDAR
    );

    $destinos = [
        'Capitan America' => 'BMS Capitán América',
        'Base Operativa' => 'Base Operativa',
        'BMS MAYA' => 'BMS Maya',
        'BMS IRON HORSE' => 'BMS Iron Horse',
        'BMS GRAND CANYON' => 'BMS Grand Canyon',
        'BMS OCEAN INTREPID' => 'BMS Ocean Intrepid',
        'BMS STIM STAR' => 'BMS Stim Star',
        'ONEL A' => 'Onel A',
    ];
@endphp

<div class="mx-auto w-full max-w-[1700px] space-y-5">
    <header class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Nueva solicitud de material
                </h1>

                <span
                    id="indicador-tipo"
                    class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"
                >
                    {{ $tipoInicial === 'epp' ? 'EPP' : 'Estándar' }}
                </span>
            </div>

            <p
                id="descripcion-tipo"
                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
            >
                Registra los materiales que necesitas solicitar al almacén.
            </p>
        </div>

        <a
            href="{{ route('solicitudes.index') }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
        >
            <svg
                class="h-5 w-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 19l-7-7 7-7"
                />
            </svg>

            Volver al listado
        </a>
    </header>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-5 py-4 dark:border-red-800 dark:bg-red-900/30">
            <div class="flex items-start gap-3">
                <svg
                    class="mt-0.5 h-5 w-5 shrink-0 text-red-600 dark:text-red-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z"
                    />
                </svg>

                <div>
                    <h2 class="font-semibold text-red-800 dark:text-red-200">
                        No fue posible registrar la solicitud
                    </h2>

                    <ul class="mt-1 list-inside list-disc text-sm text-red-700 dark:text-red-300">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div
        id="contenedor-formulario"
        class="rounded-xl border border-transparent bg-white p-5 shadow-sm transition-all duration-200 dark:bg-gray-800"
    >
        <form
            method="POST"
            action="{{ route('solicitudes.store') }}"
            id="solicitudForm"
            data-buscar-productos-url="{{ route('solicitudes.buscar-productos') }}"
            data-producto-base-url="{{ url('solicitudes/producto') }}"
        >
            @csrf

            @if($puedeCrearEpp ?? false)
                <section class="mb-5 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/30">
                    <div class="grid grid-cols-[210px_1fr] items-center gap-5">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Paso 1
                            </p>

                            <h2 class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                                Tipo de solicitud
                            </h2>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Determina los productos disponibles.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <label
                                id="opcion-estandar"
                                class="cursor-pointer rounded-lg border border-blue-200 bg-white px-4 py-3 transition hover:border-blue-500 hover:shadow-sm dark:border-blue-800 dark:bg-gray-800"
                            >
                                <div class="flex items-center gap-3">
                                    <input
                                        type="radio"
                                        name="tipo_solicitud"
                                        value="estandar"
                                        class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                        @checked($tipoInicial === 'estandar')
                                    >

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-900 dark:text-white">
                                                Estándar
                                            </span>

                                            <span class="rounded bg-blue-100 px-2 py-0.5 text-[11px] font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                                GENERAL
                                            </span>
                                        </div>

                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                            Materiales de cualquier categoría.
                                        </p>
                                    </div>
                                </div>
                            </label>

                            <label
                                id="opcion-epp"
                                class="cursor-pointer rounded-lg border border-orange-300 bg-orange-50 px-4 py-3 transition hover:border-orange-500 hover:shadow-sm dark:border-orange-700 dark:bg-orange-900/20"
                            >
                                <div class="flex items-center gap-3">
                                    <input
                                        type="radio"
                                        name="tipo_solicitud"
                                        value="epp"
                                        class="h-4 w-4 border-orange-300 text-orange-600 focus:ring-orange-500"
                                        @checked($tipoInicial === 'epp')
                                    >

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-orange-900 dark:text-orange-200">
                                                Equipo de protección personal
                                            </span>

                                            <span class="rounded bg-orange-200 px-2 py-0.5 text-[11px] font-semibold text-orange-800 dark:bg-orange-800 dark:text-orange-100">
                                                EPP
                                            </span>
                                        </div>

                                        <p class="mt-0.5 text-xs text-orange-700 dark:text-orange-300">
                                            Solo productos de categoría SEGURIDAD.
                                        </p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    @error('tipo_solicitud')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </section>
            @else
                <input
                    type="hidden"
                    name="tipo_solicitud"
                    value="estandar"
                >
            @endif

            <div class="grid grid-cols-12 items-start gap-5">
                <section class="col-span-8 overflow-visible rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Paso 2
                            </p>

                            <h2 class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                                Materiales solicitados
                            </h2>
                        </div>

                        <span
                            id="contador-productos"
                            class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                            aria-live="polite"
                        >
                            0 productos agregados
                        </span>
                    </div>

                    <div class="p-5">
                        <div
                            id="panel-buscador"
                            class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4 transition-colors duration-200 dark:border-gray-600 dark:bg-gray-700"
                        >
                            <div class="flex items-end gap-4">
                                <div class="min-w-0 flex-1">
                                    <label
                                        for="buscador-producto"
                                        id="etiqueta-buscador"
                                        class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                    >
                                        Buscar producto
                                    </label>

                                    <div class="relative">
                                        <svg
                                            class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m21 21-4.35-4.35M19 11a8 8 0 11-16 0 8 8 0 0116 0z"
                                            />
                                        </svg>

                                        <input
                                            type="text"
                                            id="buscador-producto"
                                            autocomplete="off"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-white pl-10 pr-4 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-blue-500 dark:border-gray-500 dark:bg-gray-800 dark:text-white"
                                            placeholder="Nombre, número económico, categoría o medida..."
                                        >

                                        <div
                                            id="resultados-busqueda"
                                            class="absolute z-30 mt-1 hidden max-h-80 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-xl dark:border-gray-600 dark:bg-gray-700"
                                        ></div>
                                    </div>
                                </div>

                                <div class="w-52">
                                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        Filtro activo
                                    </p>

                                    <div class="flex h-11 items-center rounded-lg border border-gray-200 bg-white px-3 text-sm font-medium text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                        <span
                                            id="filtro-productos"
                                            class="truncate"
                                        >
                                            Todas las categorías
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <p
                                id="ayuda-buscador"
                                class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                            >
                                Escribe al menos dos caracteres para buscar.
                            </p>
                        </div>

                        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full table-fixed">
                                <colgroup>
                                    <col class="w-[35%]">
                                    <col class="w-[13%]">
                                    <col class="w-[10%]">
                                    <col class="w-[10%]">
                                    <col class="w-[12%]">
                                    <col class="w-[15%]">
                                    <col class="w-[5%]">
                                </colgroup>

                                <thead class="bg-gray-100 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Producto
                                        </th>

                                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Categoría
                                        </th>

                                        <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Medida
                                        </th>

                                        <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Existencia
                                        </th>

                                        <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Cantidad
                                        </th>

                                        <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Precio unitario
                                        </th>

                                        <th class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                            Acción
                                        </th>
                                    </tr>
                                </thead>

                                <tbody
                                    id="productos-agregados"
                                    class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800"
                                ></tbody>
                            </table>

                            <div
                                id="mensaje-inicial"
                                class="bg-white px-6 py-16 text-center text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                            >
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                    <svg
                                        class="h-7 w-7 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                        />
                                    </svg>
                                </div>

                                <p class="mt-3 font-semibold text-gray-700 dark:text-gray-200">
                                    Sin materiales agregados
                                </p>

                                <p class="mt-1 text-sm">
                                    Utiliza el buscador superior para agregar productos.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="col-span-4 space-y-5">
                    <section class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Paso 3
                            </p>

                            <h2 class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                                Datos de la solicitud
                            </h2>
                        </div>

                        <div class="space-y-5 p-5">
                            <div>
                                <label
                                    for="destino"
                                    class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                >
                                    Ubicación de destino
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    name="destino"
                                    id="destino"
                                    required
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="">
                                        Seleccione una ubicación
                                    </option>

                                    @foreach($destinos as $valor => $etiqueta)
                                        <option
                                            value="{{ $valor }}"
                                            @selected(old('destino') === $valor)
                                        >
                                            {{ $etiqueta }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('destino')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="personal_id"
                                    class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                >
                                    Operador / Personal asignado
                                    <span class="text-xs font-normal text-gray-400">
                                        (Opcional)
                                    </span>
                                </label>

                                <select
                                    name="personal_id"
                                    id="personal_id"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="">— N/A —</option>

                                    @foreach($personal as $persona)
                                        <option
                                            value="{{ $persona->id }}"
                                            @selected(old('personal_id') == $persona->id)
                                        >
                                            {{ $persona->nombre_completo }}

                                            @if(
                                                $persona->employee_id
                                                && $persona->employee_id !== 'N/A'
                                            )
                                                ({{ $persona->employee_id }})
                                            @endif

                                            @if($persona->area)
                                                — {{ $persona->area }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                @error('personal_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="comentario"
                                    class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                                >
                                    Comentario
                                    <span class="text-xs font-normal text-gray-400">
                                        (Opcional)
                                    </span>
                                </label>

                                <textarea
                                    name="comentario"
                                    id="comentario"
                                    rows="4"
                                    class="w-full resize-none rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    placeholder="Motivo o información adicional..."
                                >{{ old('comentario') }}</textarea>
                            </div>
                        </div>
                    </section>

                    <section
                        id="resumen-solicitud"
                        class="hidden rounded-lg border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-700"
                    >
                        <h2 class="mb-4 font-bold text-gray-900 dark:text-white">
                            Resumen
                        </h2>

                        <div class="grid grid-cols-3 divide-x divide-gray-200 dark:divide-gray-600">
                            <div class="px-2 text-center">
                                <p
                                    id="total-productos"
                                    class="text-2xl font-bold text-blue-600 dark:text-blue-400"
                                >
                                    0
                                </p>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Productos
                                </p>
                            </div>

                            <div class="px-2 text-center">
                                <p
                                    id="total-unidades"
                                    class="text-2xl font-bold text-green-600 dark:text-green-400"
                                >
                                    0
                                </p>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Unidades
                                </p>
                            </div>

                            <div class="px-2 text-center">
                                <p
                                    id="valor-estimado"
                                    class="text-xl font-bold text-purple-600 dark:text-purple-400"
                                >
                                    $0.00
                                </p>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Estimado
                                </p>
                            </div>
                        </div>
                    </section>

                    <section
                        id="panel-proceso"
                        class="rounded-lg border border-blue-200 bg-blue-50 p-4 transition-colors duration-200 dark:border-blue-800 dark:bg-blue-900/30"
                    >
                        <h2
                            id="titulo-proceso"
                            class="mb-2 font-bold text-blue-900 dark:text-blue-200"
                        >
                            Proceso de aprobación
                        </h2>

                        <div
                            id="texto-proceso"
                            class="space-y-1 text-sm text-blue-700 dark:text-blue-300"
                        >
                            <p>• La solicitud será enviada a Dirección.</p>
                            <p>• Almacén podrá hacer entregas parciales.</p>
                            <p>• El inventario se descontará en la salida.</p>
                        </div>
                    </section>

                    <div class="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            id="btn-limpiar"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                            </svg>

                            Limpiar
                        </button>

                        <button
                            type="submit"
                            id="btn-enviar"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 font-bold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                            disabled
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>

                            Enviar solicitud
                        </button>
                    </div>
                </aside>
            </div>
        </form>
    </div>
</div>

<template id="template-producto">
    <tr
        class="producto-item transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50"
    >
        <td class="px-4 py-3 align-middle">
            <p class="producto-nombre line-clamp-2 text-sm font-semibold leading-5 text-gray-900 dark:text-white"></p>

            <p class="producto-economico mt-1 truncate text-xs text-gray-500 dark:text-gray-400"></p>

            <input
                type="hidden"
                class="inventario-id"
            >
        </td>

        <td class="px-3 py-3 align-middle">
            <span class="producto-categoria block truncate text-xs font-medium text-gray-600 dark:text-gray-300"></span>
        </td>

        <td class="px-3 py-3 text-center align-middle">
            <span class="producto-medida text-xs text-gray-600 dark:text-gray-300"></span>
        </td>

        <td class="px-3 py-3 text-center align-middle">
            <span class="producto-stock inline-flex min-w-12 justify-center rounded-full bg-green-100 px-2 py-1 text-xs font-bold text-green-700 dark:bg-green-900/40 dark:text-green-300"></span>
        </td>

        <td class="px-3 py-3 text-center align-middle">
            <input
                type="number"
                class="cantidad-input h-9 w-full rounded-md border border-gray-300 bg-white px-2 text-center text-sm font-semibold text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-500 dark:bg-gray-700 dark:text-white"
                min="1"
                value="1"
                required
            >
        </td>

        <td class="px-3 py-3 text-right align-middle">
            <span class="producto-precio whitespace-nowrap text-sm font-semibold text-blue-600 dark:text-blue-400"></span>
        </td>

        <td class="px-2 py-3 text-center align-middle">
            <button
                type="button"
                class="btn-eliminar inline-flex h-8 w-8 items-center justify-center rounded-md text-red-600 transition hover:bg-red-50 hover:text-red-800 dark:text-red-400 dark:hover:bg-red-900/30"
                aria-label="Eliminar producto"
                title="Eliminar producto"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                </svg>
            </button>
        </td>
    </tr>
</template>

<script
    id="productos-anteriores"
    type="application/json"
>
    @json(old('productos', []))
</script>

<script
    src="{{ asset('js/solicitudes/create.js') }}"
    defer
></script>
@endsection