@extends('layouts.app')

@section('content')
@php
    $solicitud = $valepp->solicitudMaterial;
    $personal = $valepp->personal;
    $totalAsignado = (int) $valepp->detalles->sum('cantidad');

    $estatusVale = match ($valepp->estatus) {
        'aprobado' => [
            'texto' => 'Autorizado',
            'clases' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
        ],
        'rechazado' => [
            'texto' => 'Rechazado',
            'clases' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        ],
        default => [
            'texto' => ucfirst($valepp->estatus ?? 'registrado'),
            'clases' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
        ],
    };
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-orange-600 dark:text-orange-400">
                Vale de equipo de protección personal
            </p>
            <div class="mt-1 flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $valepp->numero_vale }}
                </h1>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $estatusVale['clases'] }}">
                    {{ $estatusVale['texto'] }}
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Registro de asignación vinculado a una solicitud de almacén tipo EPP.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('valepp.index') }}"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
            >
                Volver al listado
            </a>

            <a
                href="{{ route('valepp.exportPDF', $valepp) }}"
                class="rounded-lg bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700"
            >
                Descargar PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-8 space-y-6">
            <section class="overflow-hidden rounded-xl border border-orange-200 bg-white shadow-sm dark:border-orange-800 dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-orange-200 bg-orange-50 px-5 py-4 dark:border-orange-800 dark:bg-orange-900/20">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-orange-600 dark:text-orange-300">
                            Solicitud EPP vinculada
                        </p>
                        <h2 class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                            @if($solicitud)
                                Solicitud #{{ str_pad((string) $solicitud->id, 4, '0', STR_PAD_LEFT) }}
                            @else
                                Solicitud no disponible
                            @endif
                        </h2>
                    </div>

                    @if($solicitud)
                        <a
                            href="{{ route('solicitudes.show', $solicitud) }}"
                            class="rounded-lg border border-orange-300 bg-white px-4 py-2 text-sm font-semibold text-orange-700 transition hover:bg-orange-100 dark:border-orange-700 dark:bg-gray-800 dark:text-orange-300 dark:hover:bg-orange-900/30"
                        >
                            Ver solicitud
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-4 divide-x divide-gray-200 dark:divide-gray-700">
                    <div class="px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Tipo
                        </p>
                        <p class="mt-1 font-semibold text-orange-700 dark:text-orange-300">
                            EPP
                        </p>
                    </div>

                    <div class="px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Estatus de solicitud
                        </p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                            {{ ucfirst($solicitud?->estatus ?? 'No disponible') }}
                        </p>
                    </div>

                    <div class="px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Destino
                        </p>
                        <p class="mt-1 truncate font-semibold text-gray-900 dark:text-white" title="{{ $solicitud?->destino }}">
                            {{ $solicitud?->destino ?? 'Sin destino' }}
                        </p>
                    </div>

                    <div class="px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Solicitante
                        </p>
                        <p class="mt-1 truncate font-semibold text-gray-900 dark:text-white" title="{{ $solicitud?->user?->name }}">
                            {{ $solicitud?->user?->name ?? 'No disponible' }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Equipos asignados en este vale
                        </h2>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Estas cantidades se descuentan de lo disponible para generar otros vales vinculados.
                        </p>
                    </div>

                    <span class="rounded-full bg-orange-100 px-3 py-1 text-sm font-semibold text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                        {{ $totalAsignado }} {{ $totalAsignado === 1 ? 'unidad' : 'unidades' }}
                    </span>
                </div>

                @if($valepp->detalles->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="w-12 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        #
                                    </th>
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
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                @foreach($valepp->detalles as $detalle)
                                    <tr class="hover:bg-orange-50/40 dark:hover:bg-orange-900/10">
                                        <td class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $detalle->inventario?->nombre_producto ?? 'Producto no disponible' }}
                                            </p>
                                            <div class="mt-1 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span>{{ $detalle->inventario?->economico ?? 'Sin económico' }}</span>
                                                <span>·</span>
                                                <span>{{ $detalle->inventario?->categoria ?? 'SEGURIDAD' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-300">
                                            {{ $detalle->inventario?->medida ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-300">
                                            {{ $detalle->solicitudMaterialDetalle?->cantidad_solicitada ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-base font-bold text-orange-700 dark:text-orange-300">
                                            {{ $detalle->cantidad }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        Total asignado en el vale
                                    </td>
                                    <td class="px-4 py-3 text-center text-base font-bold text-orange-700 dark:text-orange-300">
                                        {{ $totalAsignado }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        Este vale no tiene equipos asignados.
                    </div>
                @endif
            </section>

            @if($valepp->observaciones)
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Observaciones
                    </h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $valepp->observaciones }}</p>
                </section>
            @endif
        </div>

        <aside class="col-span-4 space-y-6">
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Colaborador receptor
                    </h2>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-orange-600 text-lg font-bold text-white">
                            {{ mb_strtoupper(mb_substr($personal?->nombre_completo ?? 'NA', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-lg font-bold text-gray-900 dark:text-white" title="{{ $personal?->nombre_completo }}">
                                {{ $personal?->nombre_completo ?? 'No disponible' }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $personal?->employee_id ?? 'Sin número de empleado' }}
                            </p>
                        </div>
                    </div>

                    <dl class="mt-5 divide-y divide-gray-200 border-t border-gray-200 text-sm dark:divide-gray-700 dark:border-gray-700">
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-gray-500 dark:text-gray-400">Área</dt>
                            <dd class="text-right font-semibold text-gray-900 dark:text-white">
                                {{ $personal?->area ?? 'N/A' }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-gray-500 dark:text-gray-400">Puesto / grado</dt>
                            <dd class="text-right font-semibold text-gray-900 dark:text-white">
                                {{ $personal?->grado ?? 'N/A' }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-gray-500 dark:text-gray-400">Departamento</dt>
                            <dd class="text-right font-semibold text-gray-900 dark:text-white">
                                {{ $personal?->departamento ?? 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Información del registro
                    </h2>
                </div>

                <dl class="divide-y divide-gray-200 px-5 text-sm dark:divide-gray-700">
                    <div class="flex justify-between gap-4 py-3">
                        <dt class="text-gray-500 dark:text-gray-400">Fecha del vale</dt>
                        <dd class="font-semibold text-gray-900 dark:text-white">
                            {{ $valepp->fecha_solicitud?->format('d/m/Y') ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4 py-3">
                        <dt class="text-gray-500 dark:text-gray-400">Registrado</dt>
                        <dd class="text-right font-semibold text-gray-900 dark:text-white">
                            {{ $valepp->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4 py-3">
                        <dt class="text-gray-500 dark:text-gray-400">Registrado por</dt>
                        <dd class="max-w-[220px] text-right font-semibold text-gray-900 dark:text-white">
                            {{ $valepp->user?->name ?? 'Sistema' }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4 py-3">
                        <dt class="text-gray-500 dark:text-gray-400">Renglones</dt>
                        <dd class="font-semibold text-gray-900 dark:text-white">
                            {{ $valepp->detalles->count() }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4 py-3">
                        <dt class="text-gray-500 dark:text-gray-400">Total de unidades</dt>
                        <dd class="font-bold text-orange-700 dark:text-orange-300">
                            {{ $totalAsignado }}
                        </dd>
                    </div>
                </dl>
            </section>

            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-800 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                Este vale registra la distribución de la solicitud EPP. No representa por sí mismo una salida ni un descuento del inventario.
            </div>
        </aside>
    </div>
</div>
@endsection
