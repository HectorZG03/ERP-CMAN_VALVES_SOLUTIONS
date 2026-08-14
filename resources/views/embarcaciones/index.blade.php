@extends('layouts.app')

@section('content')
<div
    id="embarcacionesModulo"
    class="space-y-6"
    data-store-url="{{ route('embarcaciones.store') }}"
>
    {{-- Encabezado --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Destinos y embarcaciones
            </h1>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Administra el catálogo utilizado en solicitudes y requisiciones.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="inline-flex items-center rounded-lg bg-blue-50 px-4 py-2 dark:bg-blue-900/30">
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                    Total: {{ $embarcaciones->total() }}
                </span>
            </div>

            <button
                type="button"
                id="btnNuevaEmbarcacion"
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-offset-gray-900"
            >
                <svg
                    class="mr-2 h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                Nueva embarcación
            </button>
        </div>
    </div>

    {{-- Mensajes generados por JavaScript --}}
    <div
        id="mensajeEmbarcaciones"
        class="hidden rounded-md border px-4 py-3"
        role="alert"
    ></div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

        @if($embarcaciones->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg
                    class="mx-auto h-12 w-12 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 21V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16m14 0h4m-4 0h-5m-6 0H2m5 0h6m-6-4h6m-6-4h6m-6-4h6"
                    />
                </svg>

                <h2 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">
                    No hay embarcaciones registradas
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Presiona “Nueva embarcación” para agregar el primer registro.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                No.
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Nombre
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Solicitudes
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Requisiciones
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Fecha de registro
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        @foreach($embarcaciones as $embarcacion)
                            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $embarcaciones->firstItem() + $loop->index }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $embarcacion->nombre }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ $embarcacion->solicitudes_count }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span class="inline-flex rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                        {{ $embarcacion->requisiciones_count }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $embarcacion->created_at?->format('d/m/Y H:i') ?? 'Sin fecha' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Editar --}}
                                        <button
                                            type="button"
                                            class="btn-editar-embarcacion inline-flex items-center rounded-md bg-amber-100 px-3 py-1.5 text-sm font-medium text-amber-700 transition-colors hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:bg-amber-900/40 dark:text-amber-300 dark:hover:bg-amber-900/60"
                                            data-id="{{ $embarcacion->id }}"
                                            data-nombre="{{ $embarcacion->nombre }}"
                                            data-url="{{ route('embarcaciones.update', $embarcacion) }}"
                                        >
                                            <svg
                                                class="mr-1.5 h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 13H9v-2.828l6.586-6.586z"
                                                />
                                            </svg>

                                            Editar
                                        </button>

                                        {{-- Eliminar --}}
                                        <button
                                            type="button"
                                            class="btn-eliminar-embarcacion inline-flex items-center rounded-md bg-red-100 px-3 py-1.5 text-sm font-medium text-red-700 transition-colors hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-red-900/40 dark:text-red-300 dark:hover:bg-red-900/60"
                                            data-id="{{ $embarcacion->id }}"
                                            data-nombre="{{ $embarcacion->nombre }}"
                                            data-url="{{ route('embarcaciones.destroy', $embarcacion) }}"
                                            data-solicitudes="{{ $embarcacion->solicitudes_count }}"
                                            data-requisiciones="{{ $embarcacion->requisiciones_count }}"
                                        >
                                            <svg
                                                class="mr-1.5 h-4 w-4"
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

                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($embarcaciones->hasPages())
                <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                    {{ $embarcaciones->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

{{-- Modal para crear y editar --}}
<div
    id="modalEmbarcacion"
    class="fixed inset-0 z-50 hidden"
    aria-labelledby="tituloModalEmbarcacion"
    aria-modal="true"
    role="dialog"
>
    {{-- Fondo oscuro --}}
    <div
        class="modal-fondo absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
        data-modal-close="modalEmbarcacion"
    ></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl dark:bg-gray-800">

            {{-- Encabezado --}}
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <div>
                    <h2
                        id="tituloModalEmbarcacion"
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Nueva embarcación
                    </h2>

                    <p
                        id="descripcionModalEmbarcacion"
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    >
                        Ingresa el nombre del nuevo destino o embarcación.
                    </p>
                </div>

                <button
                    type="button"
                    class="rounded-md p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                    data-modal-close="modalEmbarcacion"
                    aria-label="Cerrar"
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
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            {{-- Formulario --}}
            <form id="formEmbarcacion" novalidate>
                @csrf

                <input
                    type="hidden"
                    id="embarcacionId"
                    name="embarcacion_id"
                    value=""
                >

                <input
                    type="hidden"
                    id="metodoFormulario"
                    value="POST"
                >

                <div class="space-y-5 px-6 py-5">
                    {{-- Error general --}}
                    <div
                        id="errorGeneralEmbarcacion"
                        class="hidden rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300"
                        role="alert"
                    ></div>

                    {{-- Nombre --}}
                    <div>
                        <label
                            for="nombreEmbarcacion"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Nombre de la embarcación
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="nombreEmbarcacion"
                            name="nombre"
                            maxlength="150"
                            autocomplete="off"
                            placeholder="Ejemplo: BMS Ocean Intrepid"
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400"
                        >

                        <p
                            id="errorNombreEmbarcacion"
                            class="mt-2 hidden text-sm text-red-600 dark:text-red-400"
                        ></p>

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Máximo 150 caracteres.
                        </p>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800">
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        data-modal-close="modalEmbarcacion"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        id="btnGuardarEmbarcacion"
                        class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg
                            id="iconoCargandoEmbarcacion"
                            class="mr-2 hidden h-4 w-4 animate-spin"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>

                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            ></path>
                        </svg>

                        <span id="textoBtnGuardarEmbarcacion">
                            Guardar
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal para confirmar eliminación --}}
<div
    id="modalEliminarEmbarcacion"
    class="fixed inset-0 z-50 hidden"
    aria-labelledby="tituloModalEliminar"
    aria-modal="true"
    role="dialog"
>
    {{-- Fondo oscuro --}}
    <div
        class="modal-fondo absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
        data-modal-close="modalEliminarEmbarcacion"
    ></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl dark:bg-gray-800">

            <div class="px-6 py-5">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40">
                        <svg
                            class="h-6 w-6 text-red-600 dark:text-red-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"
                            />
                        </svg>
                    </div>

                    <div class="flex-1">
                        <h2
                            id="tituloModalEliminar"
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            Eliminar embarcación
                        </h2>

                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            ¿Estás seguro de eliminar
                            <span
                                id="nombreEmbarcacionEliminar"
                                class="font-semibold text-gray-900 dark:text-white"
                            ></span>?
                        </p>

                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Esta acción no se puede deshacer.
                        </p>

                        <div
                            id="advertenciaRelaciones"
                            class="mt-4 hidden rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-300"
                        ></div>

                        <div
                            id="errorEliminarEmbarcacion"
                            class="mt-4 hidden rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300"
                            role="alert"
                        ></div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800">
                <button
                    type="button"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                    data-modal-close="modalEliminarEmbarcacion"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    id="btnConfirmarEliminar"
                    class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <svg
                        id="iconoCargandoEliminar"
                        class="mr-2 hidden h-4 w-4 animate-spin"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                        ></path>
                    </svg>

                    <span id="textoBtnEliminar">
                        Eliminar
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- El comportamiento se implementará en el siguiente paso --}}
<script
    src="{{ asset('js/embarcaciones.js') }}"
    defer
></script>
@endsection