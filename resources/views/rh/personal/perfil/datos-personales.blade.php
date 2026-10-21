@php
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $inputClass = 'mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white';
    $helpClass = 'mt-1.5 text-xs text-gray-500 dark:text-gray-400';
@endphp

<div class="mb-5">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Datos personales</h2>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        La edad se calcula a partir de la fecha de nacimiento.
    </p>
</div>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
    <div>
        <label for="nombre" class="{{ $labelClass }}">Nombre(s) <span class="text-red-500">*</span></label>
        <input id="nombre" name="nombre" type="text" maxlength="255"
               value="{{ old('nombre', $personal->nombre) }}" required class="{{ $inputClass }}">
        @error('nombre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="ape_pat" class="{{ $labelClass }}">Apellido paterno <span class="text-red-500">*</span></label>
        <input id="ape_pat" name="ape_pat" type="text" maxlength="255"
               value="{{ old('ape_pat', $personal->ape_pat) }}" required class="{{ $inputClass }}">
        @error('ape_pat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="ape_mat" class="{{ $labelClass }}">Apellido materno <span class="text-red-500">*</span></label>
        <input id="ape_mat" name="ape_mat" type="text" maxlength="255"
               value="{{ old('ape_mat', $personal->ape_mat) }}" required class="{{ $inputClass }}">
        @error('ape_mat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="fecha_nacimiento" class="{{ $labelClass }}">Fecha de nacimiento</label>
        <input id="fecha_nacimiento" name="fecha_nacimiento" type="date"
               value="{{ old('fecha_nacimiento', $personal->fecha_nacimiento?->format('Y-m-d')) }}"
               max="{{ now()->format('Y-m-d') }}" class="{{ $inputClass }}">
        @error('fecha_nacimiento') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="edad" class="{{ $labelClass }}">Edad</label>
        <input id="edad" name="edad" type="number" min="18" max="100"
               value="{{ old('edad', $personal->edad) }}"
               readonly aria-readonly="true"
               class="{{ $inputClass }} cursor-not-allowed bg-gray-100 dark:bg-gray-600">
        <p class="{{ $helpClass }}">Campo calculado; no se puede editar manualmente.</p>
        @error('edad') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sexo" class="{{ $labelClass }}">Sexo <span class="text-red-500">*</span></label>
        <select id="sexo" name="sexo" required class="{{ $inputClass }}">
            <option value="">Selecciona una opción</option>
            @foreach (['Masculino', 'Femenino', 'Otro'] as $opcion)
                <option value="{{ $opcion }}" @selected(old('sexo', $personal->sexo) === $opcion)>{{ $opcion }}</option>
            @endforeach
        </select>
        @error('sexo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="nacionalidad" class="{{ $labelClass }}">Nacionalidad</label>
        <input id="nacionalidad" name="nacionalidad" type="text" maxlength="100"
               value="{{ old('nacionalidad', $personal->nacionalidad) }}" class="{{ $inputClass }}">
        @error('nacionalidad') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="estado_civil" class="{{ $labelClass }}">Estado civil</label>
        <select id="estado_civil" name="estado_civil" class="{{ $inputClass }}">
            <option value="">Selecciona una opción</option>
            @foreach (['Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)', 'Unión Libre'] as $opcion)
                <option value="{{ $opcion }}" @selected(old('estado_civil', $personal->estado_civil) === $opcion)>
                    {{ $opcion }}
                </option>
            @endforeach
        </select>
        @error('estado_civil') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="grupo_sanguineo" class="{{ $labelClass }}">Grupo sanguíneo</label>
        <select id="grupo_sanguineo" name="grupo_sanguineo" class="{{ $inputClass }}">
            <option value="">Selecciona una opción</option>
            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $grupo)
                <option value="{{ $grupo }}" @selected(old('grupo_sanguineo', $personal->grupo_sanguineo) === $grupo)>
                    {{ $grupo }}
                </option>
            @endforeach
        </select>
        @error('grupo_sanguineo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2 lg:col-span-3">
        <label for="enfermedad_alergia" class="{{ $labelClass }}">Enfermedades o alergias</label>
        <textarea id="enfermedad_alergia" name="enfermedad_alergia" rows="3" maxlength="1000"
                  class="{{ $inputClass }}">{{ old('enfermedad_alergia', $personal->enfermedad_alergia) }}</textarea>
        @error('enfermedad_alergia') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2 lg:col-span-3">
        <label for="foto" class="{{ $labelClass }}">Fotografía</label>
        <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-center">
            @if ($personal->foto && $personal->foto !== 'N/A')
                <img src="{{ asset('storage/' . $personal->foto) }}"
                     alt="Fotografía actual de {{ $personal->nombre_mostrar }}"
                     class="h-20 w-20 rounded-lg border border-gray-200 object-cover dark:border-gray-700">
            @endif

            <div class="min-w-0 flex-1">
                <input id="foto" name="foto" type="file" accept="image/jpeg,image/png"
                       class="{{ $inputClass }} file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-blue-700 dark:file:bg-blue-900/40 dark:file:text-blue-300">
                <p class="{{ $helpClass }}">Selecciona una imagen solo si deseas reemplazar la actual. JPG o PNG, máximo 2 MB.</p>
                @error('foto') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>