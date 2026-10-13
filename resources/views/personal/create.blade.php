@extends('layouts.app')

@section('content')
@php
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $inputClass = 'mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-400';
    $helpClass = 'mt-1.5 text-xs text-gray-500 dark:text-gray-400';
    $errorClass = 'mt-1.5 text-sm text-red-600 dark:text-red-400';
@endphp

<div class="mx-auto max-w-6xl space-y-6 pb-8">

    {{-- Encabezado --}}
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                Recursos Humanos
            </p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                Registrar colaborador
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Captura la información laboral y personal del nuevo colaborador.
            </p>
        </div>

        <a href="{{ route('personal.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500/30 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7M8 12h13"/>
            </svg>
            Volver al personal
        </a>
    </header>

    {{-- Resumen de errores --}}
    @if ($errors->any())
        <div role="alert"
             class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/60 dark:bg-red-950/30">
            <p class="font-semibold text-red-800 dark:text-red-300">
                No se pudo guardar el registro. Revisa estos campos:
            </p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('personal.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf

        {{-- Información laboral --}}
        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Información laboral
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Datos del puesto, ingreso y clasificación del colaborador.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 xl:grid-cols-3 sm:p-6">

                <div>
                    <label for="employee_id" class="{{ $labelClass }}">
                        ID de empleado
                    </label>
                    <input type="text"
                           name="employee_id"
                           id="employee_id"
                           value="{{ old('employee_id') }}"
                           maxlength="50"
                           placeholder="Ej. CMAN-OP-001"
                           class="{{ $inputClass }}">
                    @error('employee_id')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="division" class="{{ $labelClass }}">
                        Tipo de trabajador <span class="text-red-500">*</span>
                    </label>
                    <select name="division" id="division" required class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        <option value="Operativa" @selected(old('division') === 'Operativa')>
                            Operativa
                        </option>
                        <option value="Administrativa" @selected(old('division') === 'Administrativa')>
                            Administrativa
                        </option>
                    </select>
                    @error('division')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_ingreso" class="{{ $labelClass }}">
                        Fecha de ingreso <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="fecha_ingreso"
                           id="fecha_ingreso"
                           value="{{ old('fecha_ingreso', now()->format('Y-m-d')) }}"
                           required
                           class="{{ $inputClass }}">
                    @error('fecha_ingreso')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="area" class="{{ $labelClass }}">
                        Área <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="area"
                           id="area"
                           value="{{ old('area') }}"
                           maxlength="255"
                           required
                           placeholder="Ej. Operaciones"
                           class="{{ $inputClass }}">
                    @error('area')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="departamento" class="{{ $labelClass }}">
                        Departamento <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="departamento"
                           id="departamento"
                           value="{{ old('departamento') }}"
                           maxlength="255"
                           required
                           placeholder="Ej. Mantenimiento"
                           class="{{ $inputClass }}">
                    @error('departamento')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="grado" class="{{ $labelClass }}">
                        Puesto o grado
                    </label>
                    <input type="text"
                           name="grado"
                           id="grado"
                           value="{{ old('grado') }}"
                           maxlength="255"
                           placeholder="Ej. Técnico especialista"
                           class="{{ $inputClass }}">
                    @error('grado')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sueldo" class="{{ $labelClass }}">
                        Sueldo
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            $
                        </span>
                        <input type="number"
                               name="sueldo"
                               id="sueldo"
                               value="{{ old('sueldo') }}"
                               min="0"
                               step="0.01"
                               placeholder="0.00"
                               class="{{ $inputClass }} pl-7">
                    </div>
                    @error('sueldo')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bonos" class="{{ $labelClass }}">
                        Bonos
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            $
                        </span>
                        <input type="number"
                               name="bonos"
                               id="bonos"
                               value="{{ old('bonos', '0') }}"
                               min="0"
                               step="0.01"
                               placeholder="0.00"
                               class="{{ $inputClass }} pl-7">
                    </div>
                    @error('bonos')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 xl:col-span-1">
                    <label for="foto" class="{{ $labelClass }}">
                        Fotografía
                    </label>
                    <input type="file"
                           name="foto"
                           id="foto"
                           accept="image/jpeg,image/png"
                           class="{{ $inputClass }} file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/40 dark:file:text-blue-300">
                    <p class="{{ $helpClass }}">JPG o PNG, máximo 2 MB.</p>
                    @error('foto')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Nombre --}}
        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Nombre del colaborador
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Captura los nombres y apellidos en campos separados.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-3 sm:p-6">
                <div>
                    <label for="nombre" class="{{ $labelClass }}">
                        Nombre(s) <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nombre"
                           id="nombre"
                           value="{{ old('nombre') }}"
                           maxlength="100"
                           autocomplete="given-name"
                           required
                           class="{{ $inputClass }}">
                    @error('nombre')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ape_pat" class="{{ $labelClass }}">
                        Apellido paterno <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="ape_pat"
                           id="ape_pat"
                           value="{{ old('ape_pat') }}"
                           maxlength="100"
                           autocomplete="family-name"
                           required
                           class="{{ $inputClass }}">
                    @error('ape_pat')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ape_mat" class="{{ $labelClass }}">
                        Apellido materno <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="ape_mat"
                           id="ape_mat"
                           value="{{ old('ape_mat') }}"
                           maxlength="100"
                           required
                           class="{{ $inputClass }}">
                    @error('ape_mat')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Datos personales --}}
        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Datos personales
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Información básica y médica del colaborador.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3 sm:p-6">
                <div>
                    <label for="fecha_nacimiento" class="{{ $labelClass }}">
                        Fecha de nacimiento
                    </label>
                    <input type="date"
                           name="fecha_nacimiento"
                           id="fecha_nacimiento"
                           value="{{ old('fecha_nacimiento') }}"
                           max="{{ now()->format('Y-m-d') }}"
                           class="{{ $inputClass }}">
                    @error('fecha_nacimiento')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edad" class="{{ $labelClass }}">
                        Edad
                    </label>
                    <input type="number"
                           name="edad"
                           id="edad"
                           value="{{ old('edad') }}"
                           min="18"
                           max="100"
                           placeholder="Edad en años"
                           class="{{ $inputClass }}">
                    <p class="{{ $helpClass }}">Opcional si se registró la fecha de nacimiento.</p>
                    @error('edad')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sexo" class="{{ $labelClass }}">
                        Sexo <span class="text-red-500">*</span>
                    </label>
                    <select name="sexo" id="sexo" required class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        <option value="Masculino" @selected(old('sexo') === 'Masculino')>Masculino</option>
                        <option value="Femenino" @selected(old('sexo') === 'Femenino')>Femenino</option>
                        <option value="Otro" @selected(old('sexo') === 'Otro')>Otro</option>
                    </select>
                    @error('sexo')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nacionalidad" class="{{ $labelClass }}">
                        Nacionalidad
                    </label>
                    <input type="text"
                           name="nacionalidad"
                           id="nacionalidad"
                           value="{{ old('nacionalidad') }}"
                           maxlength="100"
                           class="{{ $inputClass }}">
                    @error('nacionalidad')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estado_civil" class="{{ $labelClass }}">
                        Estado civil
                    </label>
                    <select name="estado_civil" id="estado_civil" class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        <option value="Soltero(a)" @selected(old('estado_civil') === 'Soltero(a)')>Soltero(a)</option>
                        <option value="Casado(a)" @selected(old('estado_civil') === 'Casado(a)')>Casado(a)</option>
                        <option value="Divorciado(a)" @selected(old('estado_civil') === 'Divorciado(a)')>Divorciado(a)</option>
                        <option value="Viudo(a)" @selected(old('estado_civil') === 'Viudo(a)')>Viudo(a)</option>
                        <option value="Unión Libre" @selected(old('estado_civil') === 'Unión Libre')>Unión libre</option>
                    </select>
                    @error('estado_civil')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="grupo_sanguineo" class="{{ $labelClass }}">
                        Grupo sanguíneo
                    </label>
                    <select name="grupo_sanguineo" id="grupo_sanguineo" class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $grupo)
                            <option value="{{ $grupo }}" @selected(old('grupo_sanguineo') === $grupo)>
                                {{ $grupo }}
                            </option>
                        @endforeach
                    </select>
                    @error('grupo_sanguineo')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="enfermedad_alergia" class="{{ $labelClass }}">
                        Enfermedades o alergias
                    </label>
                    <textarea name="enfermedad_alergia"
                              id="enfermedad_alergia"
                              rows="3"
                              maxlength="1000"
                              class="{{ $inputClass }}"
                              placeholder="Información relevante para el expediente">{{ old('enfermedad_alergia') }}</textarea>
                    @error('enfermedad_alergia')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Documentos --}}
        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Documentos
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Datos de identificación y seguridad social.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-4 sm:p-6">
                <div>
                    <label for="curp" class="{{ $labelClass }}">CURP</label>
                    <input type="text" name="curp" id="curp"
                           value="{{ old('curp') }}" maxlength="18"
                           class="{{ $inputClass }}">
                    @error('curp')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rfc" class="{{ $labelClass }}">RFC</label>
                    <input type="text" name="rfc" id="rfc"
                           value="{{ old('rfc') }}" maxlength="13"
                           class="{{ $inputClass }}">
                    @error('rfc')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nss" class="{{ $labelClass }}">NSS</label>
                    <input type="text" name="nss" id="nss"
                           value="{{ old('nss') }}" maxlength="11"
                           class="{{ $inputClass }}">
                    @error('nss')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="clave_interbancaria" class="{{ $labelClass }}">
                        Clave interbancaria
                    </label>
                    <input type="text"
                           name="clave_interbancaria"
                           id="clave_interbancaria"
                           value="{{ old('clave_interbancaria') }}"
                           maxlength="18"
                           class="{{ $inputClass }}">
                    @error('clave_interbancaria')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Contacto --}}
        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Contacto
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Medios de contacto y persona para emergencias.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                <div class="sm:col-span-2">
                    <label for="direccion" class="{{ $labelClass }}">Dirección</label>
                    <textarea name="direccion"
                              id="direccion"
                              rows="2"
                              maxlength="500"
                              class="{{ $inputClass }}"
                              placeholder="Calle, número, colonia, ciudad y estado">{{ old('direccion') }}</textarea>
                    @error('direccion')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="correo_electronico" class="{{ $labelClass }}">
                        Correo electrónico
                    </label>
                    <input type="email"
                           name="correo_electronico"
                           id="correo_electronico"
                           value="{{ old('correo_electronico') }}"
                           maxlength="255"
                           autocomplete="email"
                           class="{{ $inputClass }}">
                    @error('correo_electronico')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_telefonico" class="{{ $labelClass }}">
                        Teléfono
                    </label>
                    <input type="tel"
                           name="numero_telefonico"
                           id="numero_telefonico"
                           value="{{ old('numero_telefonico') }}"
                           maxlength="20"
                           autocomplete="tel"
                           class="{{ $inputClass }}">
                    @error('numero_telefonico')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nombre_contacto_emergencia" class="{{ $labelClass }}">
                        Contacto de emergencia
                    </label>
                    <input type="text"
                           name="nombre_contacto_emergencia"
                           id="nombre_contacto_emergencia"
                           value="{{ old('nombre_contacto_emergencia') }}"
                           maxlength="255"
                           class="{{ $inputClass }}">
                    @error('nombre_contacto_emergencia')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_telefonico_emergencia" class="{{ $labelClass }}">
                        Teléfono de emergencia
                    </label>
                    <input type="tel"
                           name="numero_telefonico_emergencia"
                           id="numero_telefonico_emergencia"
                           value="{{ old('numero_telefonico_emergencia') }}"
                           maxlength="20"
                           class="{{ $inputClass }}">
                    @error('numero_telefonico_emergencia')
                        <p class="{{ $errorClass }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Estado y acciones --}}
        <section class="flex flex-col gap-4 rounded-2xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-900/60 dark:bg-blue-950/30 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-semibold text-blue-900 dark:text-blue-200">
                    El colaborador se registrará como activo
                </p>
                <p class="mt-1 text-sm text-blue-800 dark:text-blue-300">
                    Los campos con <span class="text-red-500">*</span> son obligatorios.
                    La fotografía y el resto de la información son opcionales.
                </p>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row">
                <a href="{{ route('personal.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    Cancelar
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar colaborador
                </button>
            </div>
        </section>
    </form>
</div>
@endsection