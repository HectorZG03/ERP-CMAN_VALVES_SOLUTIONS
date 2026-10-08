@extends('layouts.app')

@section('content')
@php
    $usuarioActual = auth()->user();

    $puedeAdministrar = $usuarioActual->canApproveRequests()
        || $usuarioActual->canManageInventory();
@endphp

<div class="mx-auto w-full max-w-[1700px] space-y-5">
    <header class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Solicitudes de material
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Consulta solicitudes estándar y solicitudes de equipo de
                protección personal.
            </p>
        </div>

        <a
            href="{{ route('solicitudes.create') }}"
            class="inline-flex h-11 items-center gap-2 rounded-lg bg-blue-600 px-5 font-bold text-white shadow-sm transition hover:bg-blue-700"
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
                    d="M12 4v16m8-8H4"
                />
            </svg>

            Nueva solicitud
        </a>
    </header>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-5 py-4 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if($puedeAdministrar)
        <section class="rounded-lg border border-gray-200 bg-white px-5 py-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Estatus:
                    </span>

                    <nav class="flex items-center gap-2">
                        <a
                            href="{{ route('solicitudes.index', ['status' => 'all']) }}"
                            class="rounded-full px-3 py-1.5 text-xs font-bold transition
                                {{ $filterStatus === 'all'
                                    ? 'bg-blue-600 text-white shadow-sm'
                                    : 'bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-800 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-blue-900/40 dark:hover:text-blue-300' }}"
                        >
                            Todas
                            <span class="ml-1">{{ $counts['all'] }}</span>
                        </a>

                        <a
                            href="{{ route('solicitudes.index', ['status' => 'pendiente']) }}"
                            class="rounded-full px-3 py-1.5 text-xs font-bold transition
                                {{ $filterStatus === 'pendiente'
                                    ? 'bg-yellow-500 text-white shadow-sm'
                                    : 'bg-gray-100 text-gray-700 hover:bg-yellow-100 hover:text-yellow-800 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-yellow-900/40 dark:hover:text-yellow-300' }}"
                        >
                            Pendientes
                            <span class="ml-1">{{ $counts['pendiente'] }}</span>
                        </a>

                        <a
                            href="{{ route('solicitudes.index', ['status' => 'aprobado']) }}"
                            class="rounded-full px-3 py-1.5 text-xs font-bold transition
                                {{ $filterStatus === 'aprobado'
                                    ? 'bg-green-600 text-white shadow-sm'
                                    : 'bg-gray-100 text-gray-700 hover:bg-green-100 hover:text-green-800 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-green-900/40 dark:hover:text-green-300' }}"
                        >
                            Aprobadas
                            <span class="ml-1">{{ $counts['aprobado'] }}</span>
                        </a>

                        <a
                            href="{{ route('solicitudes.index', ['status' => 'denegado']) }}"
                            class="rounded-full px-3 py-1.5 text-xs font-bold transition
                                {{ $filterStatus === 'denegado'
                                    ? 'bg-red-600 text-white shadow-sm'
                                    : 'bg-gray-100 text-gray-700 hover:bg-red-100 hover:text-red-800 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-red-900/40 dark:hover:text-red-300' }}"
                        >
                            Denegadas
                            <span class="ml-1">{{ $counts['denegado'] }}</span>
                        </a>
                    </nav>
                </div>

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Registros del filtro:

                    <span class="font-bold text-gray-800 dark:text-gray-200">
                        {{ $isPaginated
                            ? $solicitudes->total()
                            : $solicitudes->count() }}
                    </span>
                </div>
            </div>
        </section>
    @endif

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-end justify-between gap-6 border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <div class="w-[620px]">
                <label
                    for="search"
                    class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300"
                >
                    Buscar en los registros visibles
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
                        id="search"
                        autocomplete="off"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white pl-10 pr-10 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="Folio, fecha, destino, solicitante, tipo o estatus..."
                    >

                    <button
                        type="button"
                        id="limpiar-busqueda"
                        class="absolute right-2 top-1/2 hidden h-7 w-7 -translate-y-1/2 items-center justify-center rounded text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-600 dark:hover:text-white"
                        aria-label="Limpiar búsqueda"
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
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-5">
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                    <span class="h-3 w-3 rounded-sm border border-orange-300 bg-orange-100 dark:border-orange-700 dark:bg-orange-900/40"></span>
                    Solicitud EPP
                </div>

                <div
                    id="contador-visibles"
                    class="rounded-lg bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                    aria-live="polite"
                >
                    {{ $solicitudes->count() }} visibles
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-fixed">
                <colgroup>
                    <col class="w-[12%]">
                    <col class="w-[19%]">
                    <col class="w-[15%]">
                    <col class="w-[20%]">
                    <col class="w-[13%]">
                    <col class="w-[21%]">
                </colgroup>

                <thead class="bg-gray-50 dark:bg-gray-900/40">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Folio / fecha
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Destino
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Resumen
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Solicitante
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Estatus
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody
                    id="solicitudes-tbody"
                    class="divide-y divide-gray-200 dark:divide-gray-700"
                >
                    @forelse($solicitudes as $solicitud)
                        @php
                            $esEpp = $solicitud->esEpp();

                            $nombreSolicitante = $solicitud->user?->name
                                ?? 'Usuario no disponible';

                            $rolSolicitante = $solicitud->user?->role
                                ?? 'N/A';

                            $fechaBusqueda = $solicitud->created_at
                                ? $solicitud->created_at->format('d/m/Y H:i')
                                : '';

                           $textoBusqueda = implode(' ', [
                            $solicitud->id,
                            str_pad(
                                $solicitud->id,
                                4,
                                '0',
                                STR_PAD_LEFT
                            ),
                            $fechaBusqueda,
                            $solicitud->destino,
                            $nombreSolicitante,
                            $rolSolicitante,
                            $solicitud->estatus,
                            $esEpp ? 'epp seguridad' : 'estandar',
                        ]);
                        @endphp

                        <tr
                            class="searchable-row transition-colors duration-150
                                {{ $esEpp
                                    ? 'bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/30 dark:hover:bg-orange-900/40'
                                    : 'bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/60' }}"
                            data-search="{{ $textoBusqueda }}"
                            data-status="{{ $solicitud->estatus }}"
                            data-tipo="{{ $solicitud->tipo_solicitud }}"
                        >
                            <td class="px-5 py-4 align-middle">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        #{{ str_pad(
                                            $solicitud->id,
                                            4,
                                            '0',
                                            STR_PAD_LEFT
                                        ) }}
                                    </span>

                                    @if($esEpp)
                                        <span class="rounded bg-orange-200 px-2 py-0.5 text-[10px] font-bold uppercase text-orange-800 dark:bg-orange-800 dark:text-orange-100">
                                            EPP
                                        </span>
                                    @else
                                        <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold uppercase text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                            Estándar
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $solicitud->created_at?->format('d/m/Y')
                                        ?? 'Sin fecha' }}
                                </p>

                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $solicitud->created_at?->format('H:i')
                                        ?? '--:--' }}
                                </p>
                            </td>

                            <td class="px-5 py-4 align-middle">
                                <div class="flex items-start gap-2">
                                    <svg
                                        class="{{ $esEpp
                                            ? 'text-orange-500'
                                            : 'text-blue-500' }} mt-0.5 h-5 w-5 shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"
                                        />
                                    </svg>

                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-sm font-semibold text-gray-900 dark:text-white"
                                            title="{{ $solicitud->destino }}"
                                        >
                                            {{ $solicitud->destino
                                                ?: 'Destino no disponible' }}
                                        </p>

                                        @if($esEpp)
                                            <p class="mt-1 text-xs font-medium text-orange-700 dark:text-orange-300">
                                                Equipo de protección personal
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 align-middle">
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-700 dark:text-gray-200">
                                        <span class="font-bold">
                                            {{ $solicitud->total_productos }}
                                        </span>
                                        productos
                                    </p>

                                    <p class="text-sm text-gray-700 dark:text-gray-200">
                                        <span class="font-bold">
                                            {{ $solicitud->total_unidades }}
                                        </span>
                                        unidades
                                    </p>

                                    @if($solicitud->total > 0)
                                        <p class="text-xs font-semibold text-purple-600 dark:text-purple-400">
                                            ${{ number_format(
                                                $solicitud->total,
                                                2
                                            ) }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            <td class="px-5 py-4 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                        {{ $esEpp
                                            ? 'bg-orange-500'
                                            : 'bg-blue-600' }}"
                                    >
                                        <span class="text-sm font-bold text-white">
                                            {{ mb_strtoupper(
                                                mb_substr(
                                                    $nombreSolicitante,
                                                    0,
                                                    1
                                                )
                                            ) }}
                                        </span>
                                    </div>

                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-sm font-semibold text-gray-900 dark:text-white"
                                            title="{{ $nombreSolicitante }}"
                                        >
                                            {{ $nombreSolicitante }}
                                        </p>

                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ ucfirst(str_replace(
                                                '_',
                                                ' ',
                                                $rolSolicitante
                                            )) }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 align-middle">
                                @if($solicitud->estatus === 'pendiente')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                        Pendiente
                                    </span>
                                @elseif($solicitud->estatus === 'aprobado')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Aprobada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Denegada
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-right align-middle">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="{{ route('solicitudes.show', $solicitud) }}"
                                        class="inline-flex h-8 items-center gap-1 rounded-md bg-blue-100 px-3 text-xs font-semibold text-blue-800 transition hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50"
                                    >
                                        Ver
                                    </a>

                                    <a
                                        href="{{ route('solicitudes.pdf', $solicitud) }}"
                                        target="_blank"
                                        class="inline-flex h-8 items-center gap-1 rounded-md bg-gray-100 px-3 text-xs font-semibold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                                    >
                                        PDF
                                    </a>

                                    @if(
                                        $usuarioActual->canApproveRequests()
                                        && $solicitud->estatus === 'pendiente'
                                    )
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'solicitudes.updateEstatus',
                                                $solicitud
                                            ) }}"
                                        >
                                            @csrf
                                            @method('PUT')

                                            <input
                                                type="hidden"
                                                name="estatus"
                                                value="aprobado"
                                            >

                                            <button
                                                type="submit"
                                                class="inline-flex h-8 items-center rounded-md bg-green-100 px-3 text-xs font-semibold text-green-800 transition hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-900/50"
                                                onclick="return confirm('¿Aprobar esta solicitud? La aprobación no descuenta inventario; el descuento se realizará al registrar la salida.')"
                                            >
                                                Aprobar
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'solicitudes.updateEstatus',
                                                $solicitud
                                            ) }}"
                                        >
                                            @csrf
                                            @method('PUT')

                                            <input
                                                type="hidden"
                                                name="estatus"
                                                value="denegado"
                                            >

                                            <button
                                                type="submit"
                                                class="inline-flex h-8 items-center rounded-md bg-red-100 px-3 text-xs font-semibold text-red-800 transition hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50"
                                                onclick="return confirm('¿Denegar esta solicitud?')"
                                            >
                                                Denegar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="6"
                                class="px-6 py-16 text-center"
                            >
                                <div class="flex flex-col items-center">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
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
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                    </div>

                                    <h3 class="mt-3 font-semibold text-gray-900 dark:text-white">
                                        No hay solicitudes
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        No existen registros para el filtro seleccionado.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    @if($solicitudes->isNotEmpty())
                        <tr
                            id="sin-coincidencias"
                            class="hidden"
                        >
                            <td
                                colspan="6"
                                class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
                            >
                                No se encontraron coincidencias en los registros visibles.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($isPaginated)
            <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                {{ $solicitudes->appends(
                    request()->query()
                )->links() }}
            </div>
        @else
            <div class="border-t border-gray-200 px-5 py-4 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                Mostrando todos los registros con estatus

                <span class="font-semibold">
                    {{ ucfirst($filterStatus) }}
                </span>

                ({{ $solicitudes->count() }} en total)
            </div>
        @endif
    </section>
</div>

<script
    src="{{ asset('js/solicitudes/index.js') }}"
    defer
></script>
@endsection