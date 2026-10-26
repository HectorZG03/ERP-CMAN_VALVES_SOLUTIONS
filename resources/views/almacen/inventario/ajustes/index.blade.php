@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                Historial de ajustes
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Consulta y seguimiento de los ajustes realizados al inventario.
            </p>
        </div>

        <a
            href="{{ route('inventario.index') }}"
            class="inline-flex items-center justify-center rounded-lg bg-gray-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-gray-700"
        >
            Volver al inventario
        </a>
    </div>


    {{-- Filtros --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <form
            method="GET"
            action="{{ route('inventario.ajustes.index') }}"
            class="space-y-4"
        >

            {{-- Búsqueda general --}}
            <div>
                <label
                    for="search"
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400"
                >
                    Buscar
                </label>

                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Producto, económico o motivo..."
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                >
            </div>


            {{-- Filtros secundarios --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

                {{-- Tipo --}}
                <div>
                    <label
                        for="tipo"
                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400"
                    >
                        Tipo
                    </label>

                    <select
                        name="tipo"
                        id="tipo"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Todos</option>

                        <option
                            value="incremento"
                            {{ ($filters['tipo'] ?? '') === 'incremento' ? 'selected' : '' }}
                        >
                            Incremento
                        </option>

                        <option
                            value="disminucion"
                            {{ ($filters['tipo'] ?? '') === 'disminucion' ? 'selected' : '' }}
                        >
                            Disminución
                        </option>

                        <option
                            value="revaluacion"
                            {{ ($filters['tipo'] ?? '') === 'revaluacion' ? 'selected' : '' }}
                        >
                            Revaluación
                        </option>
                    </select>
                </div>


                {{-- Usuario --}}
                <div>
                    <label
                        for="user_id"
                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400"
                    >
                        Usuario
                    </label>

                    <select
                        name="user_id"
                        id="user_id"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Todos</option>

                        @foreach($usuarios as $usuario)
                            <option
                                value="{{ $usuario->id }}"
                                {{ (string) ($filters['user_id'] ?? '') === (string) $usuario->id ? 'selected' : '' }}
                            >
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                {{-- Desde --}}
                <div>
                    <label
                        for="fecha_desde"
                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400"
                    >
                        Desde
                    </label>

                    <input
                        type="date"
                        name="fecha_desde"
                        id="fecha_desde"
                        value="{{ $filters['fecha_desde'] ?? '' }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                </div>


                {{-- Hasta --}}
                <div>
                    <label
                        for="fecha_hasta"
                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-400"
                    >
                        Hasta
                    </label>

                    <input
                        type="date"
                        name="fecha_hasta"
                        id="fecha_hasta"
                        value="{{ $filters['fecha_hasta'] ?? '' }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                </div>

            </div>


            {{-- Acciones --}}
            <div class="flex flex-col gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end dark:border-gray-700">

                <a
                    href="{{ route('inventario.ajustes.index') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                >
                    Limpiar
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700"
                >
                    Filtrar
                </button>

            </div>

        </form>
    </div>


    {{-- Historial --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

                <thead class="bg-gray-50 dark:bg-gray-700/50">

                    <tr>

                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            Fecha / usuario
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            Producto
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            Tipo
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            Existencia
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            Costo promedio
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            Valor total
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            Motivo
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                    @forelse($ajustes as $ajuste)

                        @php
                            $tipoClases = match($ajuste->tipo) {
                                'incremento' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'disminucion' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                default => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                            };
                        @endphp

                        <tr class="align-top transition hover:bg-gray-50 dark:hover:bg-gray-700/40">

                            {{-- Fecha / usuario --}}
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-300">

                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $ajuste->created_at->format('d/m/Y H:i') }}
                                </div>

                                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $ajuste->usuario_nombre }}
                                </div>

                            </td>


                            {{-- Producto --}}
                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">

                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $ajuste->producto }}
                                </div>

                                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $ajuste->economico ?: 'Sin económico' }}
                                </div>

                                @if($ajuste->inventario && auth()->user()->canManageInventory())

                                    <a
                                        href="{{ route('inventario.edit', $ajuste->inventario) }}"
                                        class="mt-1.5 inline-block text-xs font-medium text-blue-600 hover:underline dark:text-blue-400"
                                    >
                                        Abrir producto
                                    </a>

                                @endif

                            </td>


                            {{-- Tipo --}}
                            <td class="whitespace-nowrap px-4 py-4 text-sm">

                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $tipoClases }}">
                                    {{ ucfirst($ajuste->tipo) }}
                                </span>

                            </td>


                            {{-- Existencia --}}
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-300">

                                <div>
                                    {{ $ajuste->existencia_anterior }}
                                    →
                                    {{ $ajuste->existencia_nueva }}
                                </div>

                                <div class="mt-0.5 text-xs font-semibold {{ $ajuste->diferencia > 0 ? 'text-green-600' : ($ajuste->diferencia < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                    {{ $ajuste->diferencia > 0 ? '+' : '' }}{{ $ajuste->diferencia }}
                                </div>

                            </td>


                            {{-- Costo promedio --}}
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-300">

                                <div>
                                    ${{ number_format((float) $ajuste->costo_promedio_anterior, 2) }}
                                </div>

                                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    → ${{ number_format((float) $ajuste->costo_promedio_nuevo, 2) }}
                                </div>

                            </td>


                            {{-- Valor total --}}
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-300">

                                <div>
                                    ${{ number_format((float) $ajuste->valor_total_anterior, 2) }}
                                </div>

                                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    → ${{ number_format((float) $ajuste->valor_total_nuevo, 2) }}
                                </div>

                                <div class="mt-0.5 text-xs font-semibold {{ $ajuste->diferencia_valor > 0 ? 'text-green-600' : ($ajuste->diferencia_valor < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                    {{ $ajuste->diferencia_valor > 0 ? '+' : '' }}${{ number_format((float) $ajuste->diferencia_valor, 2) }}
                                </div>

                            </td>


                            {{-- Motivo --}}
                            <td class="max-w-sm px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $ajuste->motivo }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="px-6 py-10 text-center">

                                <div class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    No se encontraron ajustes
                                </div>

                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Intenta cambiar los filtros de búsqueda.
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Paginación --}}
        @if($ajustes->hasPages())

            <div class="border-t border-gray-200 px-4 py-4 dark:border-gray-700">
                {{ $ajustes->links() }}
            </div>

        @endif

    </div>

</div>
@endsection