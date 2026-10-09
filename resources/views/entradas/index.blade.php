@extends('layouts.app')

@section('content')

{{-- Buscador y filtros --}}
<div class="border-b border-gray-200 px-4 py-4 dark:border-gray-700 sm:px-5">
    <form method="GET"
          action="{{ route('entradas.index') }}"
          data-entradas-filter-form
          class="flex flex-col gap-3 sm:flex-row sm:items-center">

        {{-- Buscador --}}
        <div class="relative min-w-0 flex-1">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24"
                     aria-hidden="true">
                    <circle cx="11" cy="11" r="7" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4"/>
                </svg>
            </div>

            <input type="search"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Buscar por proveedor, usuario o fecha..."
                   autocomplete="off"
                   class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                   aria-label="Buscar entradas">
        </div>

        {{-- Filtro por periodo --}}
        <div class="w-full sm:w-48">
            <select name="periodo"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    aria-label="Filtrar entradas por periodo">
                <option value="todos" @selected(request('periodo', 'todos') === 'todos')>
                    Todos los periodos
                </option>
                <option value="hoy" @selected(request('periodo') === 'hoy')>
                    Hoy
                </option>
                <option value="semana" @selected(request('periodo') === 'semana')>
                    Últimos 7 días
                </option>
                <option value="mes" @selected(request('periodo') === 'mes')>
                    Este mes
                </option>
            </select>
        </div>

        <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
            Buscar
        </button>

        @if(request()->filled('buscar') || (request('periodo', 'todos') !== 'todos'))
            <button type="button"
                    data-entradas-clear
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                Limpiar
            </button>
        @endif
    </form>
</div>

<div class="w-full min-w-0 space-y-5">
    <!-- Encabezado -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                Entradas de Material
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Consulta y seguimiento de las entradas registradas.
            </p>
        </div>

        @if(auth()->user()->canManageInventory())
            <a href="{{ route('entradas.create') }}"
               class="inline-flex shrink-0 items-center justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva entrada
            </a>
        @endif
    </div>

    <!-- Resumen -->
    @if(auth()->user()->canManageInventory())
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Vista actual</span>
            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-300">
                <span class="mr-2 h-2 w-2 rounded-full bg-green-500"></span>
                Todas las entradas
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Total: <strong class="font-semibold text-gray-800 dark:text-gray-200">{{ $entradas->total() }}</strong>
                {{ $entradas->total() === 1 ? 'entrada' : 'entradas' }}
            </span>
        </div>
    @endif

    <!-- Tabla -->
    <section class="w-full min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-4 py-4 dark:border-gray-700 sm:px-5">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Historial de entradas</h2>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                En pantallas pequeñas, desliza la tabla horizontalmente para consultar todas las columnas.
            </p>
        </div>

        <div class="w-full min-w-0 overflow-x-auto">
            <table class="w-full min-w-[980px] table-fixed divide-y divide-gray-200 dark:divide-gray-700">
                <colgroup>
                    <col class="w-[145px]">
                    <col class="w-[290px]">
                    <col class="w-[135px]">
                    <col class="w-[150px]">
                    <col class="w-[235px]">
                    <col class="w-[190px]">
                </colgroup>

                <thead class="bg-gray-50 dark:bg-gray-700/70">
                    <tr>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300 sm:px-4">
                            Fecha
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300 sm:px-4">
                            Proveedor
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300 sm:px-4">
                            Cantidad
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300 sm:px-4">
                            Total con IVA
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300 sm:px-4">
                            Usuario
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300 sm:px-4">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($entradas as $entrada)
                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="break-words px-3 py-4 align-middle text-sm text-gray-700 dark:text-gray-300 sm:px-4">
                                @if($entrada->created_at)
                                    <span class="block">{{ $entrada->created_at->format('d/m/Y') }}</span>
                                    <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">{{ $entrada->created_at->format('H:i') }} h</span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                @endif
                            </td>

                            <td class="px-3 py-4 align-middle sm:px-4">
                                @if($entrada->proveedor)
                                    <div class="flex min-w-0 items-start gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100 text-sm font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-300">
                                            {{ mb_substr($entrada->proveedor->proveedor, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="break-words text-sm font-medium leading-5 text-gray-900 dark:text-white">
                                                {{ $entrada->proveedor->proveedor }}
                                            </p>
                                            @if($entrada->proveedor->contacto ?? false)
                                                <p class="mt-1 break-words text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $entrada->proveedor->contacto }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm font-medium text-red-600 dark:text-red-400">Proveedor eliminado</span>
                                @endif
                            </td>

                            <td class="px-3 py-4 align-middle sm:px-4">
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="h-4 w-4 shrink-0 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                    </svg>
                                    <span class="font-semibold text-green-700 dark:text-green-400">{{ $entrada->cantidad }}</span>
                                    <span class="break-words text-gray-600 dark:text-gray-300">
                                        {{ $entrada->inventario->medida ?? 'unidades' }}
                                    </span>
                                </div>
                            </td>

                            <td class="break-words px-3 py-4 align-middle sm:px-4">
                                <span class="text-sm font-semibold tabular-nums text-green-700 dark:text-green-400">
                                    ${{ number_format($entrada->total_con_iva, 2) }}
                                </span>
                            </td>

                            <td class="px-3 py-4 align-middle sm:px-4">
                                @if($entrada->user)
                                    <div class="flex min-w-0 items-start gap-2">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-200 text-xs font-semibold text-gray-700 dark:bg-gray-600 dark:text-gray-100">
                                            {{ mb_substr($entrada->user->name, 0, 1) }}
                                        </div>
                                        <span class="break-words text-sm leading-5 text-gray-700 dark:text-gray-200">
                                            {{ $entrada->user->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">Usuario eliminado</span>
                                @endif
                            </td>

                            <td class="px-3 py-4 align-middle sm:px-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('entradas.show', $entrada) }}"
                                       class="inline-flex items-center justify-center gap-1.5 rounded-md bg-green-50 px-2.5 py-2 text-xs font-semibold text-green-700 transition hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-900/50"
                                       aria-label="Ver detalles de la entrada">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 3 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Detalles
                                    </a>

                                    <a href="{{ route('entradas.pdf', $entrada) }}"
                                       class="inline-flex items-center justify-center gap-1.5 rounded-md bg-blue-50 px-2.5 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50"
                                       aria-label="Generar PDF de la entrada">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a2 2 0 01.707.293l5.414 5.414A2 2 0 0118 9v10a2 2 0 01-2 2z"/>
                                        </svg>
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-14 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18M5 12h14"/>
                                </svg>
                                <h3 class="mt-3 text-sm font-semibold text-gray-900 dark:text-white">No hay entradas</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No se encontraron entradas de material.</p>
                                @if(auth()->user()->canManageInventory())
                                    <div class="mt-5">
                                        <a href="{{ route('entradas.create') }}"
                                           class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">
                                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Nueva entrada
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="flex flex-col gap-3 border-t border-gray-200 px-4 py-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Mostrando {{ $entradas->firstItem() ?? 0 }}–{{ $entradas->lastItem() ?? 0 }} de {{ $entradas->total() }} entradas
            </p>
            <div class="min-w-0">
                {{ $entradas->links() }}
            </div>
        </div>
    </section>
</div>

<script src="{{ asset('js/inventario/entradas/index.js') }}" defer></script>
@endsection
