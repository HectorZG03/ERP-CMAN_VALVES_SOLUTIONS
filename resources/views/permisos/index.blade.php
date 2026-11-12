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

    <form method="GET" action="{{ route('permisos.index') }}"
        class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
        data-permisos-filter-form>
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-0 flex-1 basis-72">
                <label for="permisos-buscar" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-200">Buscar permisos</label>
                <input id="permisos-buscar" type="search" name="buscar" value="{{ $buscar }}" maxlength="150"
                    placeholder="Nombre, apellidos, número laboral o motivo…" autocomplete="off"
                    aria-controls="permisos-resultados"
                    class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
            </div>

            @if($puedeRegistrarParaOtros)
                <div>
                    <label for="permisos-alcance" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-200">Permisos de</label>
                    <select id="permisos-alcance" name="alcance" aria-controls="permisos-resultados"
                        class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                        <option value="todos" @selected($alcance === 'todos')>Todos</option>
                        <option value="mios" @selected($alcance === 'mios') @disabled(!$personalPropio)>{{ $personalPropio ? 'Mis permisos' : 'Mis permisos (sin expediente vinculado)' }}</option>
                        <option value="otros" @selected($alcance === 'otros')>Otros colaboradores</option>
                    </select>
                </div>
            @endif

            <div>
                <label for="permisos-por-pagina" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-200">Por página</label>
                <select id="permisos-por-pagina" name="per_page" aria-controls="permisos-resultados"
                    class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                    @foreach([15, 30, 50] as $cantidad)
                        <option value="{{ $cantidad }}" @selected($porPagina === $cantidad)>{{ $cantidad }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <x-button type="submit" variant="accent" icon="search">Buscar</x-button>
                <x-button type="button" variant="secondary" icon="close" data-permisos-clear>Limpiar</x-button>
            </div>
        </div>
        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400" role="status" aria-live="polite" data-permisos-status></p>
        <p class="mt-2 hidden text-sm text-red-700 dark:text-red-300" role="alert" data-permisos-filter-error></p>
    </form>

    <div id="permisos-resultados" class="space-y-6" aria-busy="false" data-permisos-results>
        @include('permisos.partials.listado')
    </div>
</div>

@includeWhen($puedeSolicitar, 'permisos.modales.crear')
@include('permisos.modales.informacion')

@push('scripts')
    <script src="{{ asset('js/permisos-index.js') }}" defer></script>
    <script src="{{ asset('js/permisos-filtros.js') }}" defer></script>
@endpush
@endsection
