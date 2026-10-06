@extends('layouts.app')

@section('content')
<div
    id="valepp-create-app"
    class="space-y-6"
    data-search-url="{{ route('valepp.solicitudes-epp.buscar') }}"
    data-detail-url-template="{{ route('valepp.solicitudes-epp.show', ['solicitud' => '__SOLICITUD__']) }}"
    data-old-solicitud-id="{{ old('solicitud_material_id') }}"
    data-old-detalles='@json(old('detalles', []))'
>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-orange-600 dark:text-orange-400">
                Seguridad · Equipo de protección personal
            </p>
            <h1 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                Nuevo Vale EPP
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Asigne a un colaborador los equipos disponibles de una solicitud EPP.
            </p>
        </div>

        <a
            href="{{ route('valepp.index') }}"
            class="rounded-lg bg-gray-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700"
        >
            Volver al listado
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
            <p class="font-semibold">No fue posible registrar el Vale EPP:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('valepp.store') }}"
        id="valepp-form"
        class="space-y-6"
    >
        @csrf

        <input
            type="hidden"
            name="solicitud_material_id"
            id="solicitud_material_id"
            value="{{ old('solicitud_material_id') }}"
        >

        <div class="grid grid-cols-12 gap-6">
            <section class="col-span-5 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                1. Seleccionar solicitud EPP
                            </h2>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Solicitudes pendientes o aprobadas con cantidades por asignar.
                            </p>
                        </div>

                        <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">
                            Tipo EPP
                        </span>
                    </div>
                </div>

                <div class="space-y-4 p-5">
                    <div>
                        <label for="buscar-solicitud" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Buscar por ID, destino, comentario o solicitante
                        </label>
                        <input
                            type="search"
                            id="buscar-solicitud"
                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            placeholder="Ejemplo: 61, Base Operativa o Wendy"
                            autocomplete="off"
                        >
                    </div>

                    <div class="grid grid-cols-5 gap-3">
                        <div class="col-span-3">
                            <label for="fecha-solicitud-filtro" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Fecha de creación
                            </label>
                            <input
                                type="date"
                                id="fecha-solicitud-filtro"
                                class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        <div class="col-span-2 flex items-end gap-2">
                            <button
                                type="button"
                                id="buscar-solicitudes-btn"
                                class="flex-1 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700"
                            >
                                Buscar
                            </button>

                            <button
                                type="button"
                                id="limpiar-busqueda-btn"
                                class="rounded-lg border border-gray-300 px-3 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                                title="Limpiar búsqueda"
                            >
                                Limpiar
                            </button>
                        </div>
                    </div>

                    <div
                        id="estado-busqueda"
                        class="hidden rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                    ></div>

                    <div
                        id="resultados-solicitudes"
                        class="max-h-[520px] space-y-3 overflow-y-auto pr-1"
                    >
                        <div class="rounded-lg border border-dashed border-gray-300 px-5 py-10 text-center dark:border-gray-600">
                            <p class="font-medium text-gray-700 dark:text-gray-300">
                                Buscando solicitudes disponibles…
                            </p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                También puede buscar por fecha o número de solicitud.
                            </p>
                        </div>
                    </div>

                    @error('solicitud_material_id')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <section class="col-span-7 space-y-6">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            2. Datos del vale
                        </h2>
                    </div>

                    <div class="p-5">
                        <div class="mb-5 grid grid-cols-3 gap-4 rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-800 dark:bg-orange-900/20">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-orange-700 dark:text-orange-300">
                                    Próximo vale
                                </p>
                                <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $numeroVale }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-orange-700 dark:text-orange-300">
                                    Solicitud vinculada
                                </p>
                                <p id="resumen-folio" class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                                    Sin seleccionar
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-orange-700 dark:text-orange-300">
                                    Destino
                                </p>
                                <p id="resumen-destino" class="mt-1 truncate text-sm font-semibold text-gray-900 dark:text-white">
                                    —
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label for="personal_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Colaborador que recibe <span class="text-red-500">*</span>
                                </label>
                                <select
                                    name="personal_id"
                                    id="personal_id"
                                    required
                                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="">Seleccione un colaborador</option>
                                    @foreach($personalActivo as $persona)
                                        <option
                                            value="{{ $persona->id }}"
                                            @selected((string) old('personal_id') === (string) $persona->id)
                                        >
                                            {{ $persona->nombre_completo }} · {{ $persona->employee_id }} · {{ $persona->area }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('personal_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="fecha_solicitud" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha del vale <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    name="fecha_solicitud"
                                    id="fecha_solicitud"
                                    value="{{ old('fecha_solicitud', now()->format('Y-m-d')) }}"
                                    max="{{ now()->format('Y-m-d') }}"
                                    required
                                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                >
                                @error('fecha_solicitud')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-5">
                            <label for="observaciones" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Observaciones
                            </label>
                            <textarea
                                name="observaciones"
                                id="observaciones"
                                rows="3"
                                maxlength="2000"
                                class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                placeholder="Anotaciones relacionadas con la asignación de EPP"
                            >{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                3. Equipos que recibirá el colaborador
                            </h2>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                La cantidad disponible considera todos los vales vinculados previamente.
                            </p>
                        </div>

                        <span id="resumen-asignacion" class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                            0 equipos asignados
                        </span>
                    </div>

                    <div id="detalle-solicitud-vacio" class="px-6 py-14 text-center">
                        <p class="font-medium text-gray-700 dark:text-gray-300">
                            Seleccione una solicitud EPP
                        </p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Aquí se mostrarán solamente los equipos con cantidades pendientes.
                        </p>
                    </div>

                    <div id="detalle-solicitud-contenido" class="hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/40">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Equipo
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Unidad
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Solicitado
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Asignado
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Disponible
                                        </th>
                                        <th class="w-28 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Entregar
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    id="detalle-solicitud-filas"
                                    class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800"
                                ></tbody>
                            </table>
                        </div>
                    </div>

                    <div id="detalles-hidden"></div>

                    <div id="error-asignacion" class="hidden border-t border-red-200 bg-red-50 px-5 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300"></div>

                    @error('detalles')
                        <p class="border-t border-red-200 bg-red-50 px-5 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </section>
        </div>

        <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white px-6 py-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="max-w-3xl text-sm text-gray-500 dark:text-gray-400">
                El vale quedará vinculado a la solicitud EPP y autorizado para efectos del registro histórico.
                Esta operación no descuenta inventario ni genera una salida de almacén.
            </p>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('valepp.index') }}"
                    class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    id="guardar-vale-btn"
                    disabled
                    class="rounded-lg bg-orange-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Registrar Vale EPP
                </button>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset('js/valepp/create.js') }}" defer></script>
@endsection
