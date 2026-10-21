@extends('layouts.app')

@section('content')
@php
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $inputClass = 'mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white';
    $helpClass = 'mt-1.5 text-xs text-gray-500 dark:text-gray-400';
    $errorClass = 'mt-1.5 text-sm text-red-600 dark:text-red-400';
    $cardClass = 'overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700';
    $sectionTitleClass = 'text-lg font-semibold text-gray-900 dark:text-white';
@endphp

<div class="mx-auto max-w-6xl space-y-6 pb-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Recursos Humanos</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                Registrar colaborador
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Completa la información por sección. Los campos con <span class="text-red-500">*</span> son obligatorios.
            </p>
        </div>

        <a href="{{ route('personal.index') }}"
           class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            Volver al personal
        </a>
    </header>

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/60 dark:bg-red-950/30">
            <p class="font-semibold text-red-800 dark:text-red-300">Revisa la información del formulario:</p>
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

        {{-- Identidad --}}
        <section class="{{ $cardClass }}">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="{{ $sectionTitleClass }}">Identidad del colaborador</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Nombre y apellidos tal como aparecen en sus documentos.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-3 sm:p-6">
                <div>
                    <label for="nombre" class="{{ $labelClass }}">Nombre(s) <span class="text-red-500">*</span></label>
                    <input id="nombre" name="nombre" type="text" maxlength="255" autocomplete="given-name"
                           value="{{ old('nombre') }}" required class="{{ $inputClass }}">
                    @error('nombre') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ape_pat" class="{{ $labelClass }}">Apellido paterno <span class="text-red-500">*</span></label>
                    <input id="ape_pat" name="ape_pat" type="text" maxlength="255" autocomplete="family-name"
                           value="{{ old('ape_pat') }}" required class="{{ $inputClass }}">
                    @error('ape_pat') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ape_mat" class="{{ $labelClass }}">Apellido materno <span class="text-red-500">*</span></label>
                    <input id="ape_mat" name="ape_mat" type="text" maxlength="255"
                           value="{{ old('ape_mat') }}" required class="{{ $inputClass }}">
                    @error('ape_mat') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Asignación laboral --}}
        <section class="{{ $cardClass }}">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="{{ $sectionTitleClass }}">Asignación laboral</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Selecciona en orden el área, el departamento y el puesto.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3 sm:p-6">
                <div>
                    <label for="employee_id" class="{{ $labelClass }}">Número de empleado</label>
                    <input id="employee_id" name="employee_id" type="text" maxlength="50"
                           value="{{ old('employee_id') }}" placeholder="Ej. CMAN-OP-001"
                           class="{{ $inputClass }}">
                    @error('employee_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="fecha_ingreso" class="{{ $labelClass }}">Fecha de ingreso <span class="text-red-500">*</span></label>
                    <input id="fecha_ingreso" name="fecha_ingreso" type="date"
                           value="{{ old('fecha_ingreso', now()->format('Y-m-d')) }}" required
                           class="{{ $inputClass }}">
                    @error('fecha_ingreso') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sueldo" class="{{ $labelClass }}">Sueldo</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-gray-500">$</span>
                        <input id="sueldo" name="sueldo" type="number" min="0" step="0.01"
                               value="{{ old('sueldo') }}" placeholder="0.00"
                               class="{{ $inputClass }} pl-7">
                    </div>
                    @error('sueldo') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="area_id" class="{{ $labelClass }}">1. Área <span class="text-red-500">*</span></label>
                    <select id="area_id" name="area_id" required class="{{ $inputClass }}"
                            data-selected="{{ old('area_id') }}">
                        <option value="">Selecciona un área</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" @selected((string) old('area_id') === (string) $area->id)>
                                {{ $area->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <p class="{{ $helpClass }}">Primero selecciona el área a la que pertenece el colaborador.</p>
                    @error('area_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="departamento_id" class="{{ $labelClass }}">2. Departamento <span class="text-red-500">*</span></label>
                    <select id="departamento_id" name="departamento_id" required class="{{ $inputClass }}"
                            data-selected="{{ old('departamento_id') }}" disabled>
                        <option value="">Primero selecciona un área</option>
                    </select>
                    <p class="{{ $helpClass }}">Después, elige un departamento disponible dentro del área.</p>
                    @error('departamento_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="puesto_id" class="{{ $labelClass }}">3. Puesto <span class="text-red-500">*</span></label>
                    <select id="puesto_id" name="puesto_id" required class="{{ $inputClass }}"
                            data-selected="{{ old('puesto_id') }}" disabled>
                        <option value="">Primero selecciona un departamento</option>
                    </select>
                    <p class="{{ $helpClass }}">Por último, selecciona el puesto correspondiente al departamento.</p>
                    @error('puesto_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Información personal --}}
        <section class="{{ $cardClass }}">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="{{ $sectionTitleClass }}">Información personal</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Datos generales del expediente.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3 sm:p-6">
                <div>
                    <label for="fecha_nacimiento" class="{{ $labelClass }}">Fecha de nacimiento</label>
                    <input id="fecha_nacimiento" name="fecha_nacimiento" type="date"
                           value="{{ old('fecha_nacimiento') }}" max="{{ now()->format('Y-m-d') }}"
                           class="{{ $inputClass }}">
                    <p class="{{ $helpClass }}">La edad se calculará automáticamente.</p>
                    @error('fecha_nacimiento') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="edad" class="{{ $labelClass }}">Edad</label>
                    <input id="edad" name="edad" type="number" min="0" max="120"
                           value="{{ old('edad') }}" placeholder="Se calcula con la fecha de nacimiento"
                           readonly aria-readonly="true" class="{{ $inputClass }} cursor-not-allowed bg-gray-100 dark:bg-gray-600">
                    <p class="{{ $helpClass }}">Este campo se completa al indicar la fecha de nacimiento.</p>
                    @error('edad') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sexo" class="{{ $labelClass }}">Sexo <span class="text-red-500">*</span></label>
                    <select id="sexo" name="sexo" required class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        @foreach (['Masculino', 'Femenino', 'Otro'] as $opcion)
                            <option value="{{ $opcion }}" @selected(old('sexo') === $opcion)>{{ $opcion }}</option>
                        @endforeach
                    </select>
                    @error('sexo') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nacionalidad" class="{{ $labelClass }}">Nacionalidad</label>
                    <input id="nacionalidad" name="nacionalidad" type="text" maxlength="100"
                           value="{{ old('nacionalidad') }}" class="{{ $inputClass }}">
                    @error('nacionalidad') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="estado_civil" class="{{ $labelClass }}">Estado civil</label>
                    <select id="estado_civil" name="estado_civil" class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        @foreach (['Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)', 'Unión Libre'] as $opcion)
                            <option value="{{ $opcion }}" @selected(old('estado_civil') === $opcion)>{{ $opcion }}</option>
                        @endforeach
                    </select>
                    @error('estado_civil') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="grupo_sanguineo" class="{{ $labelClass }}">Grupo sanguíneo</label>
                    <select id="grupo_sanguineo" name="grupo_sanguineo" class="{{ $inputClass }}">
                        <option value="">Selecciona una opción</option>
                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $grupo)
                            <option value="{{ $grupo }}" @selected(old('grupo_sanguineo') === $grupo)>{{ $grupo }}</option>
                        @endforeach
                    </select>
                    @error('grupo_sanguineo') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="enfermedad_alergia" class="{{ $labelClass }}">Enfermedades o alergias</label>
                    <textarea id="enfermedad_alergia" name="enfermedad_alergia" rows="3" maxlength="1000"
                              placeholder="Información relevante para el expediente."
                              class="{{ $inputClass }}">{{ old('enfermedad_alergia') }}</textarea>
                    @error('enfermedad_alergia') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Documentos --}}
        <section class="{{ $cardClass }}">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="{{ $sectionTitleClass }}">Documentos oficiales</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Captura los datos disponibles.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                <div>
                    <label for="curp" class="{{ $labelClass }}">CURP</label>
                    <input id="curp" name="curp" type="text" maxlength="18" value="{{ old('curp') }}" class="{{ $inputClass }}">
                    @error('curp') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="rfc" class="{{ $labelClass }}">RFC</label>
                    <input id="rfc" name="rfc" type="text" maxlength="13" value="{{ old('rfc') }}" class="{{ $inputClass }}">
                    @error('rfc') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nss" class="{{ $labelClass }}">NSS</label>
                    <input id="nss" name="nss" type="text" maxlength="11" value="{{ old('nss') }}" class="{{ $inputClass }}">
                    @error('nss') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="clave_interbancaria" class="{{ $labelClass }}">CLABE interbancaria</label>
                    <input id="clave_interbancaria" name="clave_interbancaria" type="text" maxlength="18"
                           value="{{ old('clave_interbancaria') }}" class="{{ $inputClass }}">
                    @error('clave_interbancaria') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Contacto --}}
        <section class="{{ $cardClass }}">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="{{ $sectionTitleClass }}">Contacto</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Medios para localizar al colaborador.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                <div class="sm:col-span-2">
                    <label for="direccion" class="{{ $labelClass }}">Dirección</label>
                    <textarea id="direccion" name="direccion" rows="2" maxlength="500"
                              placeholder="Calle, número, colonia, ciudad, estado y código postal."
                              class="{{ $inputClass }}">{{ old('direccion') }}</textarea>
                    @error('direccion') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="correo_electronico" class="{{ $labelClass }}">Correo electrónico</label>
                    <input id="correo_electronico" name="correo_electronico" type="email" maxlength="255"
                           value="{{ old('correo_electronico') }}" class="{{ $inputClass }}">
                    @error('correo_electronico') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="numero_telefonico" class="{{ $labelClass }}">Teléfono</label>
                    <input id="numero_telefonico" name="numero_telefonico" type="tel" maxlength="20"
                           value="{{ old('numero_telefonico') }}" class="{{ $inputClass }}">
                    @error('numero_telefonico') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Emergencia y fotografía --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <section class="{{ $cardClass }}">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                    <h2 class="{{ $sectionTitleClass }}">Contacto de emergencia</h2>
                </div>
                <div class="grid grid-cols-1 gap-5 p-5 sm:p-6">
                    <div>
                        <label for="nombre_contacto_emergencia" class="{{ $labelClass }}">Nombre del contacto</label>
                        <input id="nombre_contacto_emergencia" name="nombre_contacto_emergencia" type="text" maxlength="255"
                               value="{{ old('nombre_contacto_emergencia') }}" class="{{ $inputClass }}">
                        @error('nombre_contacto_emergencia') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="numero_telefonico_emergencia" class="{{ $labelClass }}">Teléfono de emergencia</label>
                        <input id="numero_telefonico_emergencia" name="numero_telefonico_emergencia" type="tel" maxlength="20"
                               value="{{ old('numero_telefonico_emergencia') }}" class="{{ $inputClass }}">
                        @error('numero_telefonico_emergencia') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section class="{{ $cardClass }}">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                    <h2 class="{{ $sectionTitleClass }}">Fotografía</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Opcional. JPG o PNG, máximo 2 MB.</p>
                </div>
                <div class="p-5 sm:p-6">
                    <label for="foto" class="{{ $labelClass }}">Seleccionar imagen</label>
                    <input id="foto" name="foto" type="file" accept="image/jpeg,image/png"
                           class="{{ $inputClass }} file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-blue-700 dark:file:bg-blue-900/40 dark:file:text-blue-300">
                    @error('foto') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </section>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 dark:border-gray-700 sm:flex-row sm:justify-end">
            <a href="{{ route('personal.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                Guardar colaborador
            </button>
        </div>
    </form>
</div>

<script type="application/json" id="personal-catalogs">@json($areas)</script>
<script src="{{ asset('js/rh/personal-form.js') }}" defer></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const birthDate = document.getElementById('fecha_nacimiento');
    const age = document.getElementById('edad');

    if (!birthDate || !age) return;

    const calculateAge = () => {
        if (!birthDate.value) {
            age.value = '';
            return;
        }

        const today = new Date();
        const [year, month, day] = birthDate.value.split('-').map(Number);
        let years = today.getFullYear() - year;
        const birthdayHasNotOccurred =
            today.getMonth() + 1 < month ||
            (today.getMonth() + 1 === month && today.getDate() < day);

        if (birthdayHasNotOccurred) years--;

        age.value = years >= 0 ? years : '';
    };

    birthDate.addEventListener('input', calculateAge);
    birthDate.addEventListener('change', calculateAge);
    calculateAge();
});
</script>
@endsection