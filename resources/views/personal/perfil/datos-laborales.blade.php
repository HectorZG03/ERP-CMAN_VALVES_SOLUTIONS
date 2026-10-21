@php
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $inputClass = 'mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white';
    $helpClass = 'mt-1.5 text-xs text-gray-500 dark:text-gray-400';
@endphp

<div class="mb-5">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Datos laborales</h2>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Administra la asignación actual y los datos de contratación.
    </p>
</div>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
    <div>
        <label for="employee_id" class="{{ $labelClass }}">Número de empleado</label>
        <input id="employee_id"
               name="employee_id"
               type="text"
               maxlength="50"
               value="{{ old('employee_id', $laboral?->employee_id) }}"
               placeholder="Ej. CMAN-OP-001"
               class="{{ $inputClass }}">
        @error('employee_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="fecha_ingreso" class="{{ $labelClass }}">
            Fecha de ingreso <span class="text-red-500">*</span>
        </label>
        <input id="fecha_ingreso"
               name="fecha_ingreso"
               type="date"
               value="{{ old('fecha_ingreso', $laboral?->fecha_ingreso?->format('Y-m-d')) }}"
               required
               class="{{ $inputClass }}">
        @error('fecha_ingreso')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="sueldo" class="{{ $labelClass }}">Sueldo</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-gray-500">$</span>
            <input id="sueldo"
                   name="sueldo"
                   type="number"
                   min="0"
                   step="0.01"
                   value="{{ old('sueldo', $laboral?->sueldo) }}"
                   placeholder="0.00"
                   class="{{ $inputClass }} pl-7">
        </div>
        @error('sueldo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="area_id" class="{{ $labelClass }}">
            1. Área <span class="text-red-500">*</span>
        </label>
        <select id="area_id"
                name="area_id"
                required
                class="{{ $inputClass }}"
                data-selected="{{ old('area_id', $areaActual?->id) }}">
            <option value="">Selecciona un área</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}"
                    @selected((string) old('area_id', $areaActual?->id) === (string) $area->id)>
                    {{ $area->nombre }}
                </option>
            @endforeach
        </select>
        <p class="{{ $helpClass }}">Primero selecciona el área.</p>
        @error('area_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="departamento_id" class="{{ $labelClass }}">
            2. Departamento <span class="text-red-500">*</span>
        </label>
        <select id="departamento_id"
                name="departamento_id"
                required
                class="{{ $inputClass }}"
                data-selected="{{ old('departamento_id', $departamentoActual?->id) }}"
                disabled>
            <option value="">Primero selecciona un área</option>
        </select>
        <p class="{{ $helpClass }}">Elige un departamento de esa área.</p>
        @error('departamento_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="puesto_id" class="{{ $labelClass }}">
            3. Puesto <span class="text-red-500">*</span>
        </label>
        <select id="puesto_id"
                name="puesto_id"
                required
                class="{{ $inputClass }}"
                data-selected="{{ old('puesto_id', $puestoActual?->id) }}"
                disabled>
            <option value="">Primero selecciona un departamento</option>
        </select>
        <p class="{{ $helpClass }}">Por último, selecciona el puesto.</p>
        @error('puesto_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
        Estado laboral actual
    </p>
    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
        {{ $laboral?->estadoLaboral?->nombre ?? 'Sin estado asignado' }}
    </p>
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
        El estado se consulta aquí; su administración se manejará desde el flujo de estados laborales.
    </p>
</div>