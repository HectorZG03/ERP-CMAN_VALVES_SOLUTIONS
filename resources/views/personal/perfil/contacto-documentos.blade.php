@php
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $inputClass = 'mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white';
@endphp

<div class="mb-5">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Contacto y documentos</h2>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Información para localizar al colaborador y completar su expediente.
    </p>
</div>

<div class="space-y-6">
    <section>
        <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-gray-200">Contacto</h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="direccion" class="{{ $labelClass }}">Dirección</label>
                <textarea id="direccion" name="direccion" rows="2" maxlength="500"
                          placeholder="Calle, número, colonia, ciudad, estado y código postal."
                          class="{{ $inputClass }}">{{ old('direccion', $personal->direccion) }}</textarea>
                @error('direccion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="correo_electronico" class="{{ $labelClass }}">Correo electrónico</label>
                <input id="correo_electronico" name="correo_electronico" type="email" maxlength="255"
                       value="{{ old('correo_electronico', $personal->correo_electronico) }}" class="{{ $inputClass }}">
                @error('correo_electronico') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="numero_telefonico" class="{{ $labelClass }}">Teléfono</label>
                <input id="numero_telefonico" name="numero_telefonico" type="tel" maxlength="20"
                       value="{{ old('numero_telefonico', $personal->numero_telefonico) }}" class="{{ $inputClass }}">
                @error('numero_telefonico') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <section class="border-t border-gray-200 pt-5 dark:border-gray-700">
        <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-gray-200">Contacto de emergencia</h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="nombre_contacto_emergencia" class="{{ $labelClass }}">Nombre del contacto</label>
                <input id="nombre_contacto_emergencia" name="nombre_contacto_emergencia"
                       type="text" maxlength="255"
                       value="{{ old('nombre_contacto_emergencia', $personal->nombre_contacto_emergencia) }}"
                       class="{{ $inputClass }}">
                @error('nombre_contacto_emergencia') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="numero_telefonico_emergencia" class="{{ $labelClass }}">Teléfono de emergencia</label>
                <input id="numero_telefonico_emergencia" name="numero_telefonico_emergencia"
                       type="tel" maxlength="20"
                       value="{{ old('numero_telefonico_emergencia', $personal->numero_telefonico_emergencia) }}"
                       class="{{ $inputClass }}">
                @error('numero_telefonico_emergencia') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <section class="border-t border-gray-200 pt-5 dark:border-gray-700">
        <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-gray-200">Documentos oficiales</h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="curp" class="{{ $labelClass }}">CURP</label>
                <input id="curp" name="curp" type="text" maxlength="18"
                       value="{{ old('curp', $personal->curp) }}" class="{{ $inputClass }}">
                @error('curp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="rfc" class="{{ $labelClass }}">RFC</label>
                <input id="rfc" name="rfc" type="text" maxlength="13"
                       value="{{ old('rfc', $personal->rfc) }}" class="{{ $inputClass }}">
                @error('rfc') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nss" class="{{ $labelClass }}">NSS</label>
                <input id="nss" name="nss" type="text" maxlength="11"
                       value="{{ old('nss', $personal->nss) }}" class="{{ $inputClass }}">
                @error('nss') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="clave_interbancaria" class="{{ $labelClass }}">CLABE interbancaria</label>
                <input id="clave_interbancaria" name="clave_interbancaria" type="text" maxlength="18"
                       value="{{ old('clave_interbancaria', $personal->clave_interbancaria) }}"
                       class="{{ $inputClass }}">
                @error('clave_interbancaria') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>
</div>