@extends('layouts.app')

@section('content')
@php
    $laboral = $personal->datosLaborales;
    $puestoActual = $laboral?->puesto;
    $departamentoActual = $puestoActual?->departamento;
    $areaActual = $departamentoActual?->area;

    $estadoClave = strtolower($laboral?->estadoLaboral?->clave ?? '');
    $estadoNombre = $laboral?->estadoLaboral?->nombre ?? 'Sin estado';

    $estadoClass = match ($estadoClave) {
        'activo' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
        'baja' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
        'suspendido' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
    };

    $tabClass = 'profile-tab inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition';
    $tabActiveClass = 'border-blue-600 text-blue-700 dark:border-blue-400 dark:text-blue-300';
    $tabInactiveClass = 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200';
@endphp

<div class="mx-auto max-w-6xl space-y-5 pb-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-center gap-4">
            @if ($personal->foto && $personal->foto !== 'N/A')
                <img src="{{ asset('storage/' . $personal->foto) }}"
                     alt="Fotografía de {{ $personal->nombre_mostrar }}"
                     class="h-14 w-14 shrink-0 rounded-full border border-gray-200 object-cover dark:border-gray-700">
            @else
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-blue-100 text-lg font-bold text-blue-800 dark:bg-blue-900/50 dark:text-blue-200">
                    {{ strtoupper(substr($personal->nombre_mostrar ?? 'NA', 0, 2)) }}
                </div>
            @endif

            <div class="min-w-0">
                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Recursos Humanos</p>
                <h1 class="truncate text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $personal->nombre_mostrar }}
                </h1>
                <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ $laboral?->employee_id ?: 'Sin número de empleado' }}</span>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $estadoClass }}">
                        {{ $estadoNombre }}
                    </span>
                </div>
            </div>
        </div>

        <a href="{{ route('personal.index') }}"
           class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            Volver al personal
        </a>
    </header>

    @if (session('success'))
        <div role="status"
             class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert"
             class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900/60 dark:bg-red-950/30">
            <p class="font-semibold text-red-800 dark:text-red-300">
                No se pudieron guardar los cambios. Revisa los datos:
            </p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('personal.update', $personal) }}"
          enctype="multipart/form-data"
          novalidate
          class="space-y-5">
        @csrf
        @method('PUT')

        <input type="hidden"
               name="_active_tab"
               id="active-profile-tab"
               value="{{ old('_active_tab', 'laboral') }}">

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-2 dark:border-gray-700 sm:px-4">
                <nav class="-mb-px flex overflow-x-auto"
                     role="tablist"
                     aria-label="Secciones del expediente">

                    <button type="button"
                            id="tab-button-laboral"
                            class="{{ $tabClass }} {{ $tabActiveClass }}"
                            data-profile-tab="laboral"
                            role="tab"
                            aria-controls="tab-panel-laboral"
                            aria-selected="true">
                        Laboral
                        @if ($errors->hasAny(['area_id', 'departamento_id', 'puesto_id', 'employee_id', 'fecha_ingreso', 'sueldo']))
                            <span class="h-2 w-2 rounded-full bg-red-500" aria-label="Hay errores"></span>
                        @endif
                    </button>

                    <button type="button"
                            id="tab-button-personal"
                            class="{{ $tabClass }} {{ $tabInactiveClass }}"
                            data-profile-tab="personal"
                            role="tab"
                            aria-controls="tab-panel-personal"
                            aria-selected="false">
                        Datos personales
                        @if ($errors->hasAny(['nombre', 'ape_pat', 'ape_mat', 'sexo', 'edad', 'fecha_nacimiento', 'foto', 'nacionalidad', 'estado_civil', 'grupo_sanguineo', 'enfermedad_alergia']))
                            <span class="h-2 w-2 rounded-full bg-red-500" aria-label="Hay errores"></span>
                        @endif
                    </button>

                    <button type="button"
                            id="tab-button-contacto"
                            class="{{ $tabClass }} {{ $tabInactiveClass }}"
                            data-profile-tab="contacto"
                            role="tab"
                            aria-controls="tab-panel-contacto"
                            aria-selected="false">
                        Contacto y documentos
                        @if ($errors->hasAny(['curp', 'rfc', 'nss', 'clave_interbancaria', 'direccion', 'correo_electronico', 'numero_telefonico', 'nombre_contacto_emergencia', 'numero_telefonico_emergencia']))
                            <span class="h-2 w-2 rounded-full bg-red-500" aria-label="Hay errores"></span>
                        @endif
                    </button>
                </nav>
            </div>

            <div id="tab-panel-laboral"
                 class="profile-panel p-5 sm:p-6"
                 role="tabpanel"
                 aria-labelledby="tab-button-laboral"
                 data-profile-panel="laboral">
                @include('personal.perfil.datos-laborales')
            </div>

            <div id="tab-panel-personal"
                 class="profile-panel hidden p-5 sm:p-6"
                 role="tabpanel"
                 aria-labelledby="tab-button-personal"
                 data-profile-panel="personal"
                 hidden>
                @include('personal.perfil.datos-personales')
            </div>

            <div id="tab-panel-contacto"
                 class="profile-panel hidden p-5 sm:p-6"
                 role="tabpanel"
                 aria-labelledby="tab-button-contacto"
                 data-profile-panel="contacto"
                 hidden>
                @include('personal.perfil.contacto-documentos')
            </div>
        </section>

        <div class="flex flex-col-reverse justify-end gap-3 border-t border-gray-200 pt-5 dark:border-gray-700 sm:flex-row">
            <a href="{{ route('personal.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                Cancelar
            </a>

            <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                Guardar cambios
            </button>
        </div>
    </form>
</div>

<script type="application/json" id="personal-catalogs">@json($areas)</script>
<script src="{{ asset('js/rh/personal-form.js') }}" defer></script>
<script src="{{ asset('js/rh/perfil-personal.js') }}" defer></script>
@endsection