@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 pb-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                <i class="fas fa-user-minus" aria-hidden="true"></i>
            </span>

            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Bajas de personal
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Consulta las bajas activas y los registros anulados.
                </p>
            </div>
        </div>

        <button
            type="button"
            class="open-baja-modal inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
        >
            <i class="fas fa-plus" aria-hidden="true"></i>
            Registrar baja
        </button>
    </header>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <article class="rounded-xl border border-red-200 bg-white p-5 shadow-sm dark:border-red-900/50 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Bajas activas
            </p>

            <div class="mt-2 flex items-end justify-between">
                <p class="text-3xl font-bold text-red-700 dark:text-red-300">
                    {{ $totalBajasActivas }}
                </p>

                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                    <i class="fas fa-user-minus" aria-hidden="true"></i>
                </span>
            </div>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Colaboradores actualmente dados de baja
            </p>
        </article>

        <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Bajas anuladas
            </p>

            <div class="mt-2 flex items-end justify-between">
                <p class="text-3xl font-bold text-gray-700 dark:text-gray-200">
                    {{ $totalBajasAnuladas }}
                </p>

                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    <i class="fas fa-undo" aria-hidden="true"></i>
                </span>
            </div>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Registros conservados como historial
            </p>
        </article>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 p-4 dark:border-gray-700">
            <form
                method="GET"
                action="{{ route('bajas.index') }}"
                class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_220px_auto_auto]"
            >
                <label class="sr-only" for="search">
                    Buscar bajas
                </label>

                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true"></i>

                    <input
                        type="search"
                        id="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Buscar persona, empleado, puesto o motivo"
                        class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                    >
                </div>

                <label class="sr-only" for="estado">
                    Filtrar por estado
                </label>

                <select
                    id="estado"
                    name="estado"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                >
                    <option value="todas" @selected($estado === 'todas')>
                        Todas las bajas
                    </option>
                    <option value="activa" @selected($estado === 'activa')>
                        Bajas activas
                    </option>
                    <option value="anulada" @selected($estado === 'anulada')>
                        Bajas anuladas
                    </option>
                </select>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    <i class="fas fa-filter" aria-hidden="true"></i>
                    Aplicar
                </button>

                @if ($search !== '' || $estado !== 'todas')
                    <a
                        href="{{ route('bajas.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        @if ($bajas->isEmpty())
            <div class="px-6 py-12 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700">
                    <i class="fas fa-inbox" aria-hidden="true"></i>
                </span>

                <h2 class="mt-3 font-semibold text-gray-800 dark:text-gray-200">
                    No hay bajas para mostrar
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Cambia los filtros o registra una baja con el botón de esta pantalla.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-[1100px] w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Colaborador
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Puesto y área
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Fecha de baja
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Motivo
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Estado
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        @foreach ($bajas as $baja)
                            @php
                                $persona = $baja->personal;
                                $laboral = $persona?->datosLaborales;
                                $puesto = $laboral?->puesto;
                                $departamento = $puesto?->departamento;
                                $area = $departamento?->area;
                                $estaAnulada = $baja->estado === 'anulada';
                                $nombrePersona = $persona?->nombre_mostrar ?? 'Colaborador no disponible';
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $nombrePersona }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $laboral?->employee_id ?: 'Sin número de empleado' }}
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $puesto?->nombre ?? 'Sin puesto asignado' }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $departamento?->nombre ?? 'Sin departamento' }}
                                        @if ($area)
                                            · {{ $area->nombre }}
                                        @endif
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $baja->fecha_baja?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td class="max-w-xs px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    <p class="line-clamp-3 whitespace-pre-line">
                                        {{ $baja->motivo_baja ?: 'Sin motivo especificado' }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    @if ($estaAnulada)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-500"></span>
                                            Anulada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/40 dark:text-red-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>
                                            Activa
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($persona)
                                            <a
                                                href="{{ route('personal.show', ['personal' => $persona->id, 'tab' => 'bajas']) }}"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50 dark:border-blue-900/60 dark:text-blue-300 dark:hover:bg-blue-950/30"
                                                title="Ver historial en el perfil"
                                            >
                                                <i class="fas fa-user" aria-hidden="true"></i>
                                                Perfil
                                            </a>
                                        @endif

                                        @unless ($estaAnulada)
                                            <form
                                                method="POST"
                                                action="{{ route('bajas.reactivar', $baja) }}"
                                                data-confirm-reactivation
                                                data-worker-name="{{ $nombrePersona }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700"
                                                >
                                                    <i class="fas fa-undo" aria-hidden="true"></i>
                                                    Reactivar
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                {{ $bajas->links() }}
            </div>
        @endif
    </section>
</div>

@include('rh.bajas.modales.registrar-baja')

<script src="{{ asset('js/rh/bajas-index.js') }}" defer></script>
<script src="{{ asset('js/rh/bajas-modal.js') }}" defer></script>
@endsection