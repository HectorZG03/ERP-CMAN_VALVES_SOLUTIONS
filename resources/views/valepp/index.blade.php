@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-orange-600 dark:text-orange-400">
                Seguridad · Equipo de protección personal
            </p>
            <h1 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                Vales EPP
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Distribución de equipos vinculada a solicitudes de almacén tipo EPP.
            </p>
        </div>

        <a
            href="{{ route('valepp.create') }}"
            class="rounded-lg bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700"
        >
            Nuevo Vale EPP
        </a>
    </div>

    <div class="grid grid-cols-4 gap-5">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Total de vales
            </p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                {{ $totalVales }}
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Resultados encontrados
            </p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                {{ $valepp->total() }}
            </p>
        </div>

        <div class="col-span-2 rounded-xl border border-orange-200 bg-orange-50 p-5 shadow-sm dark:border-orange-800 dark:bg-orange-900/20">
            <p class="text-xs font-semibold uppercase tracking-wide text-orange-700 dark:text-orange-300">
                Flujo actual
            </p>
            <p class="mt-2 text-sm leading-6 text-orange-900 dark:text-orange-200">
                Cada vale distribuye cantidades de una solicitud EPP. Registrar el vale no descuenta inventario ni crea una salida de almacén.
            </p>
        </div>
    </div>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form method="GET" action="{{ route('valepp.index') }}" class="grid grid-cols-12 gap-4">
            <div class="col-span-7">
                <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Buscar vale
                </label>
                <input
                    type="search"
                    name="search"
                    id="search"
                    value="{{ $search }}"
                    maxlength="150"
                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Número o ID de vale, solicitud, colaborador, empleado, área o destino"
                >
            </div>

            <div class="col-span-3">
                <label for="fecha" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Fecha del vale
                </label>
                <input
                    type="date"
                    name="fecha"
                    id="fecha"
                    value="{{ $fecha }}"
                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                >
            </div>

            <div class="col-span-2 flex items-end gap-2">
                <button
                    type="submit"
                    class="flex-1 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700"
                >
                    Buscar
                </button>

                @if($search !== '' || $fecha)
                    <a
                        href="{{ route('valepp.index') }}"
                        class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/40">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Vale / Fecha
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Solicitud EPP
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Colaborador
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Destino
                        </th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Asignación
                        </th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Estatus
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($valepp as $vale)
                        @php
                            $solicitud = $vale->solicitudMaterial;
                            $estatus = match ($vale->estatus) {
                                'aprobado' => [
                                    'texto' => 'Autorizado',
                                    'clases' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                ],
                                'rechazado' => [
                                    'texto' => 'Rechazado',
                                    'clases' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                ],
                                default => [
                                    'texto' => ucfirst($vale->estatus ?? 'registrado'),
                                    'clases' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                                ],
                            };
                        @endphp

                        <tr class="transition hover:bg-orange-50/40 dark:hover:bg-orange-900/10">
                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="font-bold text-gray-900 dark:text-white">
                                    {{ $vale->numero_vale }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $vale->fecha_solicitud?->format('d/m/Y') ?? 'Sin fecha' }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                                    ID #{{ $vale->id }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if($solicitud)
                                    <a
                                        href="{{ route('solicitudes.show', $solicitud) }}"
                                        class="font-bold text-orange-700 hover:text-orange-900 dark:text-orange-300 dark:hover:text-orange-200"
                                    >
                                        #{{ str_pad((string) $solicitud->id, 4, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ ucfirst($solicitud->estatus) }} · EPP
                                    </p>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">
                                        No disponible
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <p class="max-w-[260px] truncate font-semibold text-gray-900 dark:text-white" title="{{ $vale->personal?->nombre_completo }}">
                                    {{ $vale->personal?->nombre_completo ?? 'No disponible' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $vale->personal?->employee_id ?? 'Sin número' }} · {{ $vale->personal?->area ?? 'Sin área' }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="max-w-[210px] truncate text-sm font-medium text-gray-700 dark:text-gray-300" title="{{ $solicitud?->destino?->nombre }}">
                                    {{ $solicitud?->destino?->nombre ?? 'Sin destino' }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <p class="text-lg font-bold text-orange-700 dark:text-orange-300">
                                   {{ (int) ($vale->unidades_asignadas ?? 0) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $vale->detalles_count }} {{ $vale->detalles_count === 1 ? 'renglón' : 'renglones' }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $estatus['clases'] }}">
                                    {{ $estatus['texto'] }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="{{ route('valepp.show', $vale) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                                    >
                                        Ver detalle
                                    </a>

                                    <a
                                        href="{{ route('valepp.exportPDF', $vale) }}"
                                        class="rounded-lg bg-orange-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-orange-700"
                                    >
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center">
                                <p class="font-semibold text-gray-700 dark:text-gray-300">
                                    No se encontraron Vales EPP
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Cree un nuevo vale o modifique los criterios de búsqueda.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($valepp->hasPages())
            <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                {{ $valepp->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
