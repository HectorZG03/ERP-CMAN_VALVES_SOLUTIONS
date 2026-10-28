@extends('layouts.app')

@section('content')
@php
    $buttonBase = 'inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-900';
    $buttonSecondary = $buttonBase . ' border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700';
    $buttonPrimary = $buttonBase . ' bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500';
    $filterBase = 'status-filter rounded-full px-3 py-1 text-xs font-semibold transition';
@endphp

<div id="personal-index"
     class="mx-auto max-w-7xl space-y-5 pb-8"
     data-index-url="{{ route('personal.index') }}">

    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Recursos Humanos</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Personal
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Consulta a los colaboradores y su asignación laboral.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('personal.exportPDF') }}" class="{{ $buttonSecondary }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3M4 19h16M5 4h9l5 5v10H5V4z"/>
                </svg>
                Exportar PDF
            </a>

            <a href="{{ route('personal.create') }}" class="{{ $buttonPrimary }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 5v14m-7-7h14"/>
                </svg>
                Nuevo colaborador
            </a>
        </div>
    </header>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5"
             aria-label="Resumen de personal">

        <article class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total de colaboradores</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPersonal }}</p>
        </article>

        <article class="rounded-xl border border-emerald-200 bg-white px-4 py-3 shadow-sm dark:border-emerald-900 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Activos</p>
            <p class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $totalActivo }}</p>
        </article>

        <article class="rounded-xl border border-red-200 bg-white px-4 py-3 shadow-sm dark:border-red-900 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Bajas</p>
            <p class="mt-1 text-2xl font-bold text-red-700 dark:text-red-400">{{ $totalBaja }}</p>
        </article>

        <article class="rounded-xl border border-blue-200 bg-white px-4 py-3 shadow-sm dark:border-blue-900 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Administrativos</p>
            <p class="mt-1 text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $totalAdministrativos }}</p>
        </article>

        <article class="rounded-xl border border-amber-200 bg-white px-4 py-3 shadow-sm dark:border-amber-900 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Operativos</p>
            <p class="mt-1 text-2xl font-bold text-amber-700 dark:text-amber-400">{{ $totalOperativos }}</p>
        </article>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-4 dark:border-gray-700 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">Directorio de personal</h2>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Busca por nombre, código, RFC, CURP, correo, teléfono, puesto, departamento o área.
                </p>
            </div>

            <form id="personal-search-form"
                  method="GET"
                  action="{{ route('personal.index') }}"
                  class="flex w-full gap-2 lg:max-w-xl">
                <input type="hidden" name="filter" id="personal-filter-input" value="{{ $filter }}">

                <label for="personal-search" class="sr-only">Buscar personal</label>
                <div class="relative min-w-0 flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="personal-search"
                           name="search"
                           type="search"
                           value="{{ $search }}"
                           autocomplete="off"
                           placeholder="Nombre, código, RFC, puesto..."
                           class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                </div>

                <button type="submit" class="{{ $buttonSecondary }} shrink-0">
                    Buscar
                </button>
            </form>
        </div>

        <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Filtrar por estado">
                <span class="mr-1 text-xs font-medium text-gray-500 dark:text-gray-400">Estado:</span>

                @php
                    $filters = [
                        'all' => ['label' => 'Todos', 'count' => $totalPersonal],
                        'activo' => ['label' => 'Activos', 'count' => $totalActivo],
                        'baja' => ['label' => 'Bajas', 'count' => $totalBaja],
                        'suspendido' => ['label' => 'Suspendidos', 'count' => $totalSuspendido],
                    ];

                    $activeFilterClasses = [
                        'all' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                        'activo' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                        'baja' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
                        'suspendido' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                    ];
                @endphp

                @foreach ($filters as $key => $item)
                    <a href="{{ route('personal.index', ['filter' => $key, 'search' => $search]) }}"
                       data-status-filter="{{ $key }}"
                       aria-current="{{ $filter === $key ? 'true' : 'false' }}"
                       class="{{ $filterBase }} {{ $filter === $key
                            ? $activeFilterClasses[$key]
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600' }}">
                        {{ $item['label'] }}
                        <span class="ml-1">{{ $item['count'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div id="personal-results-region">
            <div class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400" aria-live="polite">
                Mostrando
                <span id="total-visible" class="font-semibold text-gray-700 dark:text-gray-200">
                    {{ $personal->count() }}
                </span>
                de
                <span id="total-records" class="font-semibold text-gray-700 dark:text-gray-200">
                    {{ $personal->total() }}
                </span>
                resultados
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/70">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                Colaborador
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                Puesto / departamento
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                Ingreso
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                Estado
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        @forelse ($personal as $persona)
                            @php
                                $laboral = $persona->datosLaborales;
                                $puesto = $laboral?->puesto;
                                $departamento = $puesto?->departamento;
                                $area = $departamento?->area;

                                $estadoClave = strtolower($laboral?->estadoLaboral?->clave ?? 'sin estado');
                                $estadoNombre = $laboral?->estadoLaboral?->nombre ?? 'Sin estado';

                                $estadoClass = match ($estadoClave) {
                                    'activo' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                                    'baja' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
                                    'suspendido' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
                                };
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-4 py-3">
                                    <div class="flex min-w-56 items-center gap-3">
                                        @if ($persona->foto && $persona->foto !== 'N/A')
                                            <img src="{{ asset('storage/' . $persona->foto) }}"
                                                 alt=""
                                                 class="h-9 w-9 shrink-0 rounded-full object-cover">
                                        @else
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-800 dark:bg-blue-900/50 dark:text-blue-200">
                                                {{ strtoupper(substr($persona->nombre_mostrar ?? 'NA', 0, 2)) }}
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <span class="block truncate text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $persona->nombre_mostrar }}
                                            </span>
                                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                                {{ $laboral?->employee_id ?: 'Sin código de empleado' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="max-w-xs">
                                        <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $puesto?->nombre ?? 'Puesto sin asignar' }}
                                        </p>
                                        <p class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400">
                                            {{ $departamento?->nombre ?? 'Sin departamento' }}
                                            @if ($area)
                                                <span aria-hidden="true">·</span> {{ $area->nombre }}
                                            @endif
                                        </p>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $laboral?->fecha_ingreso?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $estadoClass }}">
                                        {{ $estadoNombre }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('personal.show', $persona) }}"
                                           class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-blue-900/60 dark:bg-blue-900/30 dark:text-blue-200 dark:hover:bg-blue-900/60"
                                           aria-label="Abrir perfil de {{ $persona->nombre_mostrar }}"
                                           title="Ver perfil">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </a>

                                        <button
                                            type="button"
                                            class="open-personal-info-modal inline-flex h-9 w-9 items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 transition hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-indigo-900/60 dark:bg-indigo-900/30 dark:text-indigo-200 dark:hover:bg-indigo-900/60"
                                            data-name="{{ $persona->nombre_mostrar }}"
                                            data-employee-id="{{ $laboral?->employee_id }}"
                                            data-area="{{ $area?->nombre }}"
                                            data-department="{{ $departamento?->nombre }}"
                                            data-position="{{ $puesto?->nombre }}"
                                            data-hire-date="{{ $laboral?->fecha_ingreso?->format('d/m/Y') }}"
                                            data-status="{{ $estadoNombre }}"
                                            data-email="{{ $persona->correo_electronico }}"
                                            data-phone="{{ $persona->numero_telefonico }}"
                                            data-curp="{{ $persona->curp }}"
                                            data-rfc="{{ $persona->rfc }}"
                                            data-nss="{{ $persona->nss }}"
                                            data-sex="{{ $persona->sexo }}"
                                            data-birth-date="{{ $persona->fecha_nacimiento }}"
                                            data-nationality="{{ $persona->nacionalidad }}"
                                            data-civil-status="{{ $persona->estado_civil }}"
                                            data-address="{{ $persona->direccion }}"
                                            aria-label="Consultar datos de {{ $persona->nombre_mostrar }}"
                                            title="Consultar datos"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <p class="font-medium text-gray-700 dark:text-gray-200">
                                        No se encontraron colaboradores con esos criterios.
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Prueba con otro nombre, código o dato laboral.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($personal->hasPages())
                <div id="personal-pagination"
                     class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                    {{ $personal->links() }}
                </div>
            @endif
        </div>
    </section>

    @include('rh.personal.modales.informacion')
</div>

<script src="{{ asset('js/rh/personal-index.js') }}" defer></script>
<script src="{{ asset('js/rh/personal-info-modal.js') }}" defer></script>
@endsection