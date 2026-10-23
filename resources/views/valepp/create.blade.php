@extends('layouts.app')

@section('content')
<div
    id="valepp-create-app"
    class="space-y-5"
    data-search-url="{{ route('valepp.solicitudes-epp.buscar') }}"
    data-detail-url-template="{{ route('valepp.solicitudes-epp.show', ['solicitud' => '__SOLICITUD__']) }}"
    data-old-solicitud-id="{{ old('solicitud_material_id') }}"
    data-old-detalles='@json(old('detalles', []))'
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-orange-600 dark:text-orange-400">
                Seguridad · Equipo de protección personal
            </p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
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


    <form
        method="POST"
        action="{{ route('valepp.store') }}"
        id="valepp-form"
        class="space-y-5"
    >
        @csrf

        <input
            type="hidden"
            name="solicitud_material_id"
            id="solicitud_material_id"
            value="{{ old('solicitud_material_id') }}"
        >

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
            <section class="overflow-hidden rounded-xl border border-sky-200 bg-sky-50/60 shadow-sm dark:border-sky-900/60 dark:bg-slate-800 xl:col-span-5">
                <div class="border-b border-sky-200 bg-sky-100/70 px-5 py-4 dark:border-sky-900/60 dark:bg-slate-900/50">
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
                        class="max-h-[420px] space-y-3 overflow-y-auto pr-1"
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

            <section class="space-y-5 xl:col-span-7">
                <div class="overflow-hidden rounded-xl border border-amber-200 bg-amber-50/60 shadow-sm dark:border-amber-900/60 dark:bg-slate-800">
                    <div class="border-b border-amber-200 bg-amber-100/70 px-5 py-4 dark:border-amber-900/60 dark:bg-slate-900/50">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            2. Datos del vale
                        </h2>
                    </div>

                    <div class="space-y-4 p-4 sm:p-5">
                        <div class="grid grid-cols-1 gap-2 rounded-lg border border-amber-200 bg-white/70 p-3 dark:border-gray-700 dark:bg-gray-800 sm:grid-cols-3">
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

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <div>
                                @php
                                    $personalSeleccionado = $personalActivo->firstWhere(
                                        'id',
                                        (int) old('personal_id')
                                    );
                                    $laboralSeleccionado = $personalSeleccionado?->datosLaborales;
                                    $puestoSeleccionado = $laboralSeleccionado?->puesto;
                                    $etiquetaSeleccionada = $personalSeleccionado
                                        ? implode(' · ', array_filter([
                                            $personalSeleccionado->nombre_mostrar,
                                            $laboralSeleccionado?->employee_id,
                                            $puestoSeleccionado?->nombre,
                                        ]))
                                        : '';
                                @endphp

                                <label for="buscar-personal" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Colaborador que recibe <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="hidden"
                                    name="personal_id"
                                    id="personal_id"
                                    value="{{ old('personal_id') }}"
                                >

                                <div id="personal-combobox" class="relative">
                                    <input
                                        type="search"
                                        id="buscar-personal"
                                        role="combobox"
                                        aria-autocomplete="list"
                                        aria-haspopup="listbox"
                                        aria-controls="personal-options"
                                        aria-expanded="false"
                                        autocomplete="off"
                                        value="{{ $etiquetaSeleccionada }}"
                                        placeholder="Escribe nombre, código, puesto o área"
                                        class="w-full rounded-lg border-gray-300 bg-white px-3 py-2.5 pr-10 text-sm text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    >
                                    <button
                                        type="button"
                                        id="limpiar-personal"
                                        class="absolute inset-y-0 right-0 hidden items-center px-3 text-gray-400 hover:text-gray-700 dark:hover:text-white"
                                        aria-label="Limpiar colaborador seleccionado"
                                    >
                                        <i class="fas fa-times" aria-hidden="true"></i>
                                    </button>

                                    <div
                                        id="personal-options"
                                        role="listbox"
                                        aria-label="Colaboradores disponibles"
                                        class="absolute z-30 mt-1 hidden max-h-64 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white p-1 shadow-lg dark:border-gray-600 dark:bg-gray-800"
                                    >
                                        @foreach($personalActivo as $persona)
                                            @php
                                                $laboral = $persona->datosLaborales;
                                                $puesto = $laboral?->puesto;
                                                $departamento = $puesto?->departamento;
                                                $area = $departamento?->area;
                                                $etiqueta = implode(' · ', array_filter([
                                                    $persona->nombre_mostrar,
                                                    $laboral?->employee_id,
                                                    $puesto?->nombre,
                                                ]));
                                                $textoBusqueda = implode(' ', array_filter([
                                                    $persona->nombre_mostrar,
                                                    $laboral?->employee_id,
                                                    $puesto?->nombre,
                                                    $departamento?->nombre,
                                                    $area?->nombre,
                                                ]));
                                            @endphp
                                            <button
                                                type="button"
                                                role="option"
                                                aria-selected="{{ (string) old('personal_id') === (string) $persona->id ? 'true' : 'false' }}"
                                                data-personal-option
                                                data-personal-id="{{ $persona->id }}"
                                                data-label="{{ $etiqueta }}"
                                                data-search="{{ $textoBusqueda }}"
                                                class="w-full rounded-md px-3 py-2 text-left transition hover:bg-orange-50 focus:bg-orange-50 focus:outline-none dark:hover:bg-gray-700 dark:focus:bg-gray-700"
                                            >
                                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $persona->nombre_mostrar }}
                                                </span>
                                                <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $laboral?->employee_id ?? 'Sin número' }}
                                                    · {{ $puesto?->nombre ?? 'Sin puesto' }}
                                                    · {{ $area?->nombre ?? 'Sin área' }}
                                                </span>
                                            </button>
                                        @endforeach
                                        <p
                                            id="personal-no-results"
                                            class="hidden px-3 py-3 text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            No se encontraron colaboradores con esa búsqueda.
                                        </p>
                                    </div>
                                </div>

                                <p
                                    id="personal-search-status"
                                    class="mt-1.5 min-h-4 text-xs text-gray-500 dark:text-gray-400"
                                    aria-live="polite"
                                ></p>

                                @if($personalActivo->isEmpty())
                                    <p class="mt-2 rounded-lg border border-orange-200 bg-orange-100/70 px-3 py-2 text-xs text-orange-900 dark:border-orange-900/60 dark:bg-orange-950/30 dark:text-orange-200">
                                        No se encontraron colaboradores con estado laboral activo.
                                    </p>
                                @endif
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

                        <div>
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

                <div class="overflow-hidden rounded-xl border border-emerald-200 bg-emerald-50/50 shadow-sm dark:border-emerald-900/60 dark:bg-slate-800">
                    <div class="flex flex-col gap-2 border-b border-emerald-200 bg-emerald-100/60 px-5 py-4 dark:border-emerald-900/60 dark:bg-slate-900/50 sm:flex-row sm:items-center sm:justify-between">
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

                    <div id="detalle-solicitud-vacio" class="bg-white/50 px-6 py-10 text-center dark:bg-gray-800/40">
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

        <div class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-gray-100/80 px-4 py-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <p class="max-w-3xl text-xs leading-5 text-gray-600 dark:text-gray-300">
                El vale queda vinculado a la solicitud EPP. No descuenta inventario ni genera una salida de almacén.
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
<script src="{{ asset('js/valepp/personal-search.js') }}" defer></script>
@endsection
