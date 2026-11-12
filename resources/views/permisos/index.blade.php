@extends('layouts.app')

@section('title', 'Permisos de personal')

@section('content')
<div class="space-y-6">

    <header class="flex flex-col gap-4 rounded-xl border border-amber-200 bg-gradient-to-r from-amber-50 to-white p-5 shadow-sm dark:border-amber-900/50 dark:from-amber-950/30 dark:to-gray-800 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">
                Recursos Humanos · Control de asistencia
            </p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                Permisos de personal
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                Consulta solicitudes, revisiones y autorizaciones.
            </p>
        </div>

        @if($puedeSolicitar)
            <x-button
                id="abrir-modal-crear-permiso"
                type="button"
                variant="accent"
                icon="add"
            >
                {{ $puedeRegistrarParaOtros ? 'Registrar permiso' : 'Solicitar permiso' }}
            </x-button>
        @endif
    </header>

    @if($mensajeVinculo)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01M10.3 3.9 2.7 17a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p>{{ $mensajeVinculo }}</p>
            </div>
        </div>
    @endif

    <section class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-blue-200 bg-blue-50/70 p-4 shadow-sm dark:border-blue-900/50 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Permisos en esta página
            </p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                {{ $permisos->count() }}
            </p>
        </div>

        <div class="rounded-xl border border-violet-200 bg-violet-50/70 p-4 shadow-sm dark:border-violet-900/50 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Total de permisos
            </p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                {{ $permisos->total() }}
            </p>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Solicitudes registradas
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if($puedeResolver)
                        Consulta las revisiones de RH y resuelve los permisos revisados.
                    @elseif($puedeRevisar)
                        Revisa las solicitudes antes de que pasen a Dirección.
                    @elseif($puedeRegistrarParaOtros)
                        Consulta los permisos del personal y registra nuevos permisos.
                    @else
                        Consulta el estado de tus permisos.
                    @endif
                </p>
            </div>

            <span class="inline-flex w-fit items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">
                {{ $permisos->total() }} {{ $permisos->total() === 1 ? 'registro' : 'registros' }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/40">
                    <tr>
                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Fecha
                        </th>
                        <th class="min-w-56 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Colaborador
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Horas
                        </th>
                        <th class="min-w-56 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Motivo
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Etapa
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($permisos as $permiso)
                        @php
                            $solicitanteEsRh = in_array(
                                $permiso->personal?->usuario?->role,
                                ['rh', 'aux_rh'],
                                true
                            );
                            $puedeRevisarEstePermiso = $solicitanteEsRh
                                ? $puedeRevisarFinanzas
                                : $puedeRevisarRh;

                            $etapaVisual = match (true) {
                                $permiso->estatus === 'aprobado' => [
                                    'texto' => 'Aprobado',
                                    'clases' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                ],
                                $permiso->estatus === 'rechazado' => [
                                    'texto' => 'Rechazado',
                                    'clases' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                ],
                                ! $permiso->revisado_at => [
                                    'texto' => $solicitanteEsRh
                                        ? 'Pendiente de revisión Finanzas'
                                        : 'Pendiente de revisión RH',
                                    'clases' => $solicitanteEsRh
                                        ? 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300'
                                        : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                ],
                                default => [
                                    'texto' => 'Pendiente de Dirección',
                                    'clases' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                ],
                            };

                            $accionTexto = $puedeRevisarEstePermiso
                                && $permiso->estatus === 'pendiente'
                                && ! $permiso->revisado_at
                                    ? 'Revisar permiso'
                                    : ($puedeResolver
                                        && $permiso->estatus === 'pendiente'
                                        && $permiso->revisado_at
                                            ? 'Resolver permiso'
                                            : 'Ver información');
                        @endphp

                        <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700 dark:text-gray-200">
                                {{ $permiso->fecha_permiso?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $permiso->personal?->nombre_mostrar ?? 'Personal no disponible' }}
                                </p>
                                @if($permiso->registradoPor)
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Registrado por {{ $permiso->registradoPor->name }}
                                    </p>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center text-sm font-semibold text-gray-800 dark:text-gray-100">
                                {{ number_format((float) $permiso->horas_solicitadas, 2) }} h
                            </td>

                            <td class="max-w-sm px-5 py-4 text-sm text-gray-600 dark:text-gray-300">
                                <span class="line-clamp-2">{{ $permiso->motivo }}</span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $etapaVisual['clases'] }}">
                                    {{ $etapaVisual['texto'] }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-button
                                        type="button"
                                        variant="secondary"
                                        size="sm"
                                        icon="view"
                                        data-permiso-open
                                        data-url="{{ route('permisos.show', $permiso) }}"
                                        data-review-url="{{ route('permisos.revisar', $permiso) }}"
                                        data-resolver-url="{{ route('permisos.resolver', $permiso) }}"
                                        data-can-review="{{ $puedeRevisarEstePermiso ? 'true' : 'false' }}"
                                    >
                                        {{ $accionTexto }}
                                    </x-button>

                                    @if($permiso->estatus === 'aprobado')
                                        <x-button
                                            :href="route('permisos.pdf', $permiso)"
                                            variant="danger"
                                            size="sm"
                                            icon="download"
                                        >
                                            Descargar PDF
                                        </x-button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <p class="font-semibold text-gray-700 dark:text-gray-200">
                                    No hay permisos registrados.
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    @if($puedeSolicitar)
                                        Usa el botón de arriba para registrar uno.
                                    @else
                                        Cuando existan permisos disponibles, aparecerán aquí.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($permisos->hasPages())
            <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                {{ $permisos->links() }}
            </div>
        @endif
    </section>
</div>

@includeWhen($puedeSolicitar, 'permisos.modales.crear')
@include('permisos.modales.informacion')

@push('scripts')
    <script src="{{ asset('js/permisos-index.js') }}" defer></script>
@endpush
@endsection
