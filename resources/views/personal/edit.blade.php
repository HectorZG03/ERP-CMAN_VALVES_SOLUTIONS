@extends('layouts.app')

@section('content')
@php
    $inputClass = 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-400';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300';
@endphp

<div class="mx-auto max-w-7xl space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                Recursos Humanos
            </p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                Editar colaborador
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $personal->nombre_mostrar }}
                @if($personal->employee_id)
                    <span class="mx-1">·</span>{{ $personal->employee_id }}
                @endif
            </p>
        </div>

        <a href="{{ route('personal.show', $personal) }}"
           class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
            Volver al expediente
        </a>
    </header>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-200">
            <p class="font-semibold">Revisa la información del formulario:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('personal.update', $personal) }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Información laboral --}}
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    Información laboral
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Puesto, área y clasificación del colaborador.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3 sm:p-6">
                <div>
                    <label for="employee_id" class="{{ $labelClass }}">ID de empleado</label>
                    <input type="text"
                           id="employee_id"
                           name="employee_id"
                           value="{{ old('employee_id', $personal->employee_id) }}"
                           maxlength="50"
                           placeholder="CMAN-OP-001"
                           class="{{ $inputClass }}">
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="division" class="{{ $labelClass }}">
                        Tipo de trabajador <span class="text-red-500">*</span>
                    </label>
                    <select id="division" name="division" required class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        <option value="Operativa" @selected(old('division', $personal->division) === 'Operativa')>
                            Operativa
                        </option>
                        <option value="Administrativa" @selected(old('division', $personal->division) === 'Administrativa')>
                            Administrativa
                        </option>
                    </select>
                    @error('division')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_ingreso" class="{{ $labelClass }}">
                        Fecha de ingreso <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           id="fecha_ingreso"
                           name="fecha_ingreso"
                           required
                           value="{{ old('fecha_ingreso', $personal->fecha_ingreso?->format('Y-m-d') ?? '') }}"
                           class="{{ $inputClass }}">
                    @error('fecha_ingreso')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="area" class="{{ $labelClass }}">
                        Área <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="area"
                           name="area"
                           required
                           maxlength="255"
                           value="{{ old('area', $personal->area) }}"
                           placeholder="Ej. Operaciones"
                           class="{{ $inputClass }}">
                    @error('area')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="departamento" class="{{ $labelClass }}">
                        Departamento <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="departamento"
                           name="departamento"
                           required
                           maxlength="255"
                           value="{{ old('departamento', $personal->departamento) }}"
                           placeholder="Ej. Producción"
                           class="{{ $inputClass }}">
                    @error('departamento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="grado" class="{{ $labelClass }}">Puesto o grado</label>
                    <input type="text"
                           id="grado"
                           name="grado"
                           maxlength="255"
                           value="{{ old('grado', $personal->grado) }}"
                           placeholder="Ej. Coordinador"
                           class="{{ $inputClass }}">
                    @error('grado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sueldo" class="{{ $labelClass }}">Sueldo</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-gray-500 dark:text-gray-400">$</span>
                        <input type="number"
                               id="sueldo"
                               name="sueldo"
                               min="0"
                               step="0.01"
                               value="{{ old('sueldo', $personal->sueldo) }}"
                               placeholder="0.00"
                               class="{{ $inputClass }} pl-7">
                    </div>
                    @error('sueldo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bonos" class="{{ $labelClass }}">Bonos</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-gray-500 dark:text-gray-400">$</span>
                        <input type="number"
                               id="bonos"
                               name="bonos"
                               min="0"
                               step="0.01"
                               value="{{ old('bonos', $personal->bonos) }}"
                               placeholder="0.00"
                               class="{{ $inputClass }} pl-7">
                    </div>
                    @error('bonos')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Nombre --}}
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    Nombre del colaborador
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Captura cada parte en su campo correspondiente.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-3 sm:p-6">
                <div>
                    <label for="nombre" class="{{ $labelClass }}">
                        Nombre(s) <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="nombre"
                           name="nombre"
                           required
                           maxlength="255"
                           autocomplete="given-name"
                           value="{{ old('nombre', $personal->nombre) }}"
                           class="{{ $inputClass }}">
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ape_pat" class="{{ $labelClass }}">
                        Apellido paterno <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="ape_pat"
                           name="ape_pat"
                           required
                           maxlength="255"
                           autocomplete="family-name"
                           value="{{ old('ape_pat', $personal->ape_pat) }}"
                           class="{{ $inputClass }}">
                    @error('ape_pat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ape_mat" class="{{ $labelClass }}">
                        Apellido materno <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="ape_mat"
                           name="ape_mat"
                           required
                           maxlength="255"
                           value="{{ old('ape_mat', $personal->ape_mat) }}"
                           class="{{ $inputClass }}">
                    @error('ape_mat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Datos personales --}}
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    Datos personales
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3 sm:p-6">
                <div>
                    <label for="fecha_nacimiento" class="{{ $labelClass }}">Fecha de nacimiento</label>
                    <input type="date"
                           id="fecha_nacimiento"
                           name="fecha_nacimiento"
                           value="{{ old('fecha_nacimiento', $personal->fecha_nacimiento?->format('Y-m-d') ?? '') }}"
                           class="{{ $inputClass }}">
                    @error('fecha_nacimiento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edad" class="{{ $labelClass }}">Edad</label>
                    <input type="number"
                           id="edad"
                           name="edad"
                           min="18"
                           max="100"
                           value="{{ old('edad', $personal->edad) }}"
                           class="{{ $inputClass }}">
                    @error('edad')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sexo" class="{{ $labelClass }}">Sexo</label>
                    <select id="sexo" name="sexo" class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        <option value="Masculino" @selected(old('sexo', $personal->sexo) === 'Masculino')>Masculino</option>
                        <option value="Femenino" @selected(old('sexo', $personal->sexo) === 'Femenino')>Femenino</option>
                        <option value="Otro" @selected(old('sexo', $personal->sexo) === 'Otro')>Otro</option>
                    </select>
                    @error('sexo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nacionalidad" class="{{ $labelClass }}">Nacionalidad</label>
                    <input type="text"
                           id="nacionalidad"
                           name="nacionalidad"
                           maxlength="100"
                           value="{{ old('nacionalidad', $personal->nacionalidad) }}"
                           class="{{ $inputClass }}">
                    @error('nacionalidad')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estado_civil" class="{{ $labelClass }}">Estado civil</label>
                    <select id="estado_civil" name="estado_civil" class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        @foreach(['Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)', 'Unión Libre'] as $estado)
                            <option value="{{ $estado }}" @selected(old('estado_civil', $personal->estado_civil) === $estado)>
                                {{ $estado }}
                            </option>
                        @endforeach
                    </select>
                    @error('estado_civil')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="grupo_sanguineo" class="{{ $labelClass }}">Grupo sanguíneo</label>
                    <select id="grupo_sanguineo" name="grupo_sanguineo" class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $grupo)
                            <option value="{{ $grupo }}" @selected(old('grupo_sanguineo', $personal->grupo_sanguineo) === $grupo)>
                                {{ $grupo }}
                            </option>
                        @endforeach
                    </select>
                    @error('grupo_sanguineo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="enfermedad_alergia" class="{{ $labelClass }}">
                        Enfermedades o alergias
                    </label>
                    <textarea id="enfermedad_alergia"
                              name="enfermedad_alergia"
                              rows="3"
                              maxlength="1000"
                              placeholder="Anota información relevante o deja el campo vacío."
                              class="{{ $inputClass }}">{{ old('enfermedad_alergia', $personal->enfermedad_alergia) }}</textarea>
                    @error('enfermedad_alergia')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Fotografía --}}
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    Fotografía
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Selecciona una imagen nueva solo si deseas reemplazar la actual.
                </p>
            </div>

            <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:p-6">
                @if($personal->foto && $personal->foto !== 'N/A')
                    <img src="{{ asset('storage/' . $personal->foto) }}"
                         alt="Fotografía actual de {{ $personal->nombre_mostrar }}"
                         class="h-28 w-28 rounded-lg border border-gray-200 object-cover dark:border-gray-600">
                @endif

                <div class="min-w-0 flex-1">
                    <label for="foto" class="{{ $labelClass }}">Cambiar fotografía</label>
                    <input type="file"
                           id="foto"
                           name="foto"
                           accept=".jpg,.jpeg,.png"
                           class="{{ $inputClass }}">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Formatos JPG o PNG. Tamaño máximo: 2 MB.
                    </p>
                    @error('foto')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Documentos --}}
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    Documentos oficiales
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                <div>
                    <label for="curp" class="{{ $labelClass }}">CURP</label>
                    <input type="text"
                           id="curp"
                           name="curp"
                           maxlength="18"
                           value="{{ old('curp', $personal->curp) }}"
                           class="{{ $inputClass }}">
                    @error('curp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rfc" class="{{ $labelClass }}">RFC</label>
                    <input type="text"
                           id="rfc"
                           name="rfc"
                           maxlength="13"
                           value="{{ old('rfc', $personal->rfc) }}"
                           class="{{ $inputClass }}">
                    @error('rfc')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nss" class="{{ $labelClass }}">NSS</label>
                    <input type="text"
                           id="nss"
                           name="nss"
                           maxlength="11"
                           value="{{ old('nss', $personal->nss) }}"
                           class="{{ $inputClass }}">
                    @error('nss')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="clave_interbancaria" class="{{ $labelClass }}">
                        CLABE interbancaria
                    </label>
                    <input type="text"
                           id="clave_interbancaria"
                           name="clave_interbancaria"
                           maxlength="18"
                           value="{{ old('clave_interbancaria', $personal->clave_interbancaria) }}"
                           class="{{ $inputClass }}">
                    @error('clave_interbancaria')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Contacto --}}
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    Contacto
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                <div class="sm:col-span-2">
                    <label for="direccion" class="{{ $labelClass }}">Dirección</label>
                    <textarea id="direccion"
                              name="direccion"
                              rows="2"
                              maxlength="500"
                              placeholder="Calle, número, colonia, ciudad, estado y código postal."
                              class="{{ $inputClass }}">{{ old('direccion', $personal->direccion) }}</textarea>
                    @error('direccion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="correo_electronico" class="{{ $labelClass }}">Correo electrónico</label>
                    <input type="email"
                           id="correo_electronico"
                           name="correo_electronico"
                           maxlength="255"
                           value="{{ old('correo_electronico', $personal->correo_electronico) }}"
                           class="{{ $inputClass }}">
                    @error('correo_electronico')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_telefonico" class="{{ $labelClass }}">Teléfono</label>
                    <input type="tel"
                           id="numero_telefonico"
                           name="numero_telefonico"
                           maxlength="20"
                           value="{{ old('numero_telefonico', $personal->numero_telefonico) }}"
                           class="{{ $inputClass }}">
                    @error('numero_telefonico')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Contacto de emergencia --}}
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    Contacto de emergencia
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                <div>
                    <label for="nombre_contacto_emergencia" class="{{ $labelClass }}">
                        Nombre del contacto
                    </label>
                    <input type="text"
                           id="nombre_contacto_emergencia"
                           name="nombre_contacto_emergencia"
                           maxlength="255"
                           value="{{ old('nombre_contacto_emergencia', $personal->nombre_contacto_emergencia) }}"
                           class="{{ $inputClass }}">
                    @error('nombre_contacto_emergencia')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_telefonico_emergencia" class="{{ $labelClass }}">
                        Teléfono de emergencia
                    </label>
                    <input type="tel"
                           id="numero_telefonico_emergencia"
                           name="numero_telefonico_emergencia"
                           maxlength="20"
                           value="{{ old('numero_telefonico_emergencia', $personal->numero_telefonico_emergencia) }}"
                           class="{{ $inputClass }}">
                    @error('numero_telefonico_emergencia')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 dark:border-gray-700 sm:flex-row sm:justify-end">
            <a href="{{ route('personal.show', $personal) }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                Cancelar
            </a>

            <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                Guardar cambios
            </button>
        </div>
    </form>
</div>
@endsection