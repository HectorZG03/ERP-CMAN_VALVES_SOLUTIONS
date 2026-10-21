@extends('layouts.app')

@section('content')
@php
    $cardClass = 'overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800';
    $labelClass = 'mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300';
    $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white';
    $primaryButtonClass = 'inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900';
    $secondaryButtonClass = 'inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600';
    $tableHeadingClass = 'sticky top-0 z-10 bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-900 dark:text-gray-400';
    $tabClass = 'catalog-tab inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-semibold transition';
@endphp

<div id="catalogos-rh" class="mx-auto max-w-7xl space-y-6 pb-8" data-csrf="{{ csrf_token() }}">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Recursos Humanos</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Catálogos de RH
            </h1>
            <p class="mt-1 max-w-2xl text-sm text-gray-600 dark:text-gray-400">
                Administra las áreas, departamentos y puestos disponibles para el personal.
            </p>
        </div>

        <a href="{{ route('personal.index') }}" class="{{ $secondaryButtonClass }}">
            <i class="fas fa-users" aria-hidden="true"></i>
            Volver a personal
        </a>
    </header>

    <div id="catalogos-alert" class="hidden rounded-xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

    <nav class="flex flex-wrap gap-2 border-b border-gray-200 pb-3 dark:border-gray-700"
         role="tablist"
         aria-label="Catálogos de Recursos Humanos">
        <button type="button"
                id="tab-areas"
                class="{{ $tabClass }}"
                role="tab"
                aria-controls="panel-areas"
                aria-selected="true"
                data-catalog-tab="areas">
            <i class="fas fa-building" aria-hidden="true"></i>
            Áreas
            <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs">{{ $areas->count() }}</span>
        </button>

        <button type="button"
                id="tab-departamentos"
                class="{{ $tabClass }}"
                role="tab"
                aria-controls="panel-departamentos"
                aria-selected="false"
                data-catalog-tab="departamentos">
            <i class="fas fa-sitemap" aria-hidden="true"></i>
            Departamentos
            <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs">{{ $departamentos->count() }}</span>
        </button>

        <button type="button"
                id="tab-puestos"
                class="{{ $tabClass }}"
                role="tab"
                aria-controls="panel-puestos"
                aria-selected="false"
                data-catalog-tab="puestos">
            <i class="fas fa-briefcase" aria-hidden="true"></i>
            Puestos
            <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs">{{ $puestos->count() }}</span>
        </button>
    </nav>

    {{-- Áreas --}}
    <section id="panel-areas"
             class="{{ $cardClass }}"
             role="tabpanel"
             aria-labelledby="tab-areas"
             data-catalog-panel="areas">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Áreas</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Nivel principal de organización. Los departamentos se asignan a un área.
            </p>
        </div>

        <div class="grid gap-6 p-5 lg:grid-cols-[minmax(260px,360px)_minmax(0,1fr)]">
            <form id="area-form"
                  data-catalog-form
                  data-entity="area"
                  data-store-url="{{ route('catalogos-rh.areas.store') }}"
                  data-update-url="{{ route('catalogos-rh.areas.update', ['area' => '__ID__']) }}"
                  method="POST"
                  action="{{ route('catalogos-rh.areas.store') }}"
                  class="h-fit space-y-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                @csrf
                <input type="hidden" name="_method" value="POST" data-method-field>

                <div>
                    <label for="area-nombre" class="{{ $labelClass }}">Nombre del área</label>
                    <input id="area-nombre" name="nombre" type="text" maxlength="100" required
                           class="{{ $inputClass }}" placeholder="Ej. Operativa" data-name-input>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="{{ $primaryButtonClass }}" data-submit-label>
                        <i class="fas fa-plus" aria-hidden="true"></i> Agregar área
                    </button>
                    <button type="button" class="{{ $secondaryButtonClass }} hidden" data-cancel-edit>
                        Cancelar
                    </button>
                </div>
            </form>

            <div class="min-w-0">
                <div class="max-h-[55vh] min-h-48 overflow-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full min-w-[520px] divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="{{ $tableHeadingClass }}">
                            <tr>
                                <th class="px-4 py-3">Área</th>
                                <th class="px-4 py-3 text-center">Departamentos</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="areas-table-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($areas as $area)
                                <tr class="text-sm" data-row="area" data-id="{{ $area->id }}">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white" data-cell="nombre">
                                        {{ $area->nombre }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300" data-cell="count">
                                        {{ $area->departamentos_count }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                    class="rounded-md p-2 text-blue-700 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-900/30"
                                                    title="Editar área"
                                                    data-edit="area"
                                                    data-id="{{ $area->id }}"
                                                    data-name="{{ $area->nombre }}">
                                                <i class="fas fa-pen" aria-hidden="true"></i>
                                            </button>
                                            <button type="button"
                                                    class="rounded-md p-2 text-red-700 hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-900/30"
                                                    title="Eliminar área"
                                                    data-delete="area"
                                                    data-id="{{ $area->id }}"
                                                    data-name="{{ $area->nombre }}"
                                                    data-delete-url="{{ route('catalogos-rh.areas.destroy', ['area' => $area->id]) }}">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr data-empty-row="area">
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Todavía no hay áreas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- Departamentos --}}
    <section id="panel-departamentos"
             class="{{ $cardClass }} hidden"
             role="tabpanel"
             aria-labelledby="tab-departamentos"
             data-catalog-panel="departamentos"
             hidden>
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Departamentos</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Selecciona el área a la que pertenece cada departamento.
            </p>
        </div>

        <div class="grid gap-6 p-5 lg:grid-cols-[minmax(260px,360px)_minmax(0,1fr)]">
            <form id="departamento-form"
                  data-catalog-form
                  data-entity="departamento"
                  data-store-url="{{ route('catalogos-rh.departamentos.store') }}"
                  data-update-url="{{ route('catalogos-rh.departamentos.update', ['departamento' => '__ID__']) }}"
                  method="POST"
                  action="{{ route('catalogos-rh.departamentos.store') }}"
                  class="h-fit space-y-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                @csrf
                <input type="hidden" name="_method" value="POST" data-method-field>

                <div>
                    <label for="departamento-area" class="{{ $labelClass }}">Área</label>
                    <select id="departamento-area" name="area_id" required
                            class="{{ $inputClass }}" data-parent-select="area">
                        <option value="">Selecciona un área</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="departamento-nombre" class="{{ $labelClass }}">Nombre del departamento</label>
                    <input id="departamento-nombre" name="nombre" type="text" maxlength="150" required
                           class="{{ $inputClass }}" placeholder="Ej. Operaciones" data-name-input>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="{{ $primaryButtonClass }}" data-submit-label>
                        <i class="fas fa-plus" aria-hidden="true"></i> Agregar departamento
                    </button>
                    <button type="button" class="{{ $secondaryButtonClass }} hidden" data-cancel-edit>
                        Cancelar
                    </button>
                </div>
            </form>

            <div class="min-w-0">
                <div class="max-h-[55vh] min-h-48 overflow-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full min-w-[680px] divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="{{ $tableHeadingClass }}">
                            <tr>
                                <th class="px-4 py-3">Departamento</th>
                                <th class="px-4 py-3">Área</th>
                                <th class="px-4 py-3 text-center">Puestos</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="departamentos-table-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($departamentos as $departamento)
                                <tr class="text-sm" data-row="departamento" data-id="{{ $departamento->id }}">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white" data-cell="nombre">
                                        {{ $departamento->nombre }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300"
                                        data-cell="parent-name"
                                        data-parent-id="{{ $departamento->area_id }}">
                                        {{ $departamento->area?->nombre ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300" data-cell="count">
                                        {{ $departamento->puestos_count }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                    class="rounded-md p-2 text-blue-700 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-900/30"
                                                    title="Editar departamento"
                                                    data-edit="departamento"
                                                    data-id="{{ $departamento->id }}"
                                                    data-name="{{ $departamento->nombre }}"
                                                    data-parent-id="{{ $departamento->area_id }}">
                                                <i class="fas fa-pen" aria-hidden="true"></i>
                                            </button>
                                            <button type="button"
                                                    class="rounded-md p-2 text-red-700 hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-900/30"
                                                    title="Eliminar departamento"
                                                    data-delete="departamento"
                                                    data-id="{{ $departamento->id }}"
                                                    data-name="{{ $departamento->nombre }}"
                                                    data-delete-url="{{ route('catalogos-rh.departamentos.destroy', ['departamento' => $departamento->id]) }}">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr data-empty-row="departamento">
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Todavía no hay departamentos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- Puestos --}}
    <section id="panel-puestos"
             class="{{ $cardClass }} hidden"
             role="tabpanel"
             aria-labelledby="tab-puestos"
             data-catalog-panel="puestos"
             hidden>
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Puestos</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Elige el departamento al que pertenece el puesto.
            </p>
        </div>

        <div class="grid gap-6 p-5 lg:grid-cols-[minmax(260px,360px)_minmax(0,1fr)]">
            <form id="puesto-form"
                  data-catalog-form
                  data-entity="puesto"
                  data-store-url="{{ route('catalogos-rh.puestos.store') }}"
                  data-update-url="{{ route('catalogos-rh.puestos.update', ['puesto' => '__ID__']) }}"
                  method="POST"
                  action="{{ route('catalogos-rh.puestos.store') }}"
                  class="h-fit space-y-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                @csrf
                <input type="hidden" name="_method" value="POST" data-method-field>

                <div>
                    <label for="puesto-departamento" class="{{ $labelClass }}">Departamento</label>
                    <select id="puesto-departamento" name="departamento_id" required
                            class="{{ $inputClass }}" data-parent-select="departamento">
                        <option value="">Selecciona un departamento</option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{ $departamento->id }}">
                                {{ $departamento->area?->nombre }} — {{ $departamento->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="puesto-nombre" class="{{ $labelClass }}">Nombre del puesto</label>
                    <input id="puesto-nombre" name="nombre" type="text" maxlength="150" required
                           class="{{ $inputClass }}" placeholder="Ej. Coordinador de operaciones" data-name-input>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="{{ $primaryButtonClass }}" data-submit-label>
                        <i class="fas fa-plus" aria-hidden="true"></i> Agregar puesto
                    </button>
                    <button type="button" class="{{ $secondaryButtonClass }} hidden" data-cancel-edit>
                        Cancelar
                    </button>
                </div>
            </form>

            <div class="min-w-0">
                <div class="max-h-[55vh] min-h-48 overflow-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full min-w-[760px] divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="{{ $tableHeadingClass }}">
                            <tr>
                                <th class="px-4 py-3">Puesto</th>
                                <th class="px-4 py-3">Departamento</th>
                                <th class="px-4 py-3">Área</th>
                                <th class="px-4 py-3 text-center">Personal</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="puestos-table-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($puestos as $puesto)
                                <tr class="text-sm" data-row="puesto" data-id="{{ $puesto->id }}">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white" data-cell="nombre">
                                        {{ $puesto->nombre }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300"
                                        data-cell="parent-name"
                                        data-parent-id="{{ $puesto->departamento_id }}">
                                        {{ $puesto->departamento?->nombre ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300" data-cell="area-name">
                                        {{ $puesto->departamento?->area?->nombre ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300" data-cell="count">
                                        {{ $puesto->datos_laborales_count }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                    class="rounded-md p-2 text-blue-700 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-900/30"
                                                    title="Editar puesto"
                                                    data-edit="puesto"
                                                    data-id="{{ $puesto->id }}"
                                                    data-name="{{ $puesto->nombre }}"
                                                    data-parent-id="{{ $puesto->departamento_id }}">
                                                <i class="fas fa-pen" aria-hidden="true"></i>
                                            </button>
                                            <button type="button"
                                                    class="rounded-md p-2 text-red-700 hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-900/30"
                                                    title="Eliminar puesto"
                                                    data-delete="puesto"
                                                    data-id="{{ $puesto->id }}"
                                                    data-name="{{ $puesto->nombre }}"
                                                    data-delete-url="{{ route('catalogos-rh.puestos.destroy', ['puesto' => $puesto->id]) }}">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr data-empty-row="puesto">
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Todavía no hay puestos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/rh/catalogos-rh.js') }}" defer></script>
@endpush