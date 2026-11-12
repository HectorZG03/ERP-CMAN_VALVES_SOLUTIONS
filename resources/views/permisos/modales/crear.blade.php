<div
    id="modal-crear-permiso"
    class="fixed inset-0 z-[90] hidden items-center justify-center bg-slate-950/60 p-4 opacity-0 transition-opacity duration-200"
    aria-hidden="true"
    data-modal
    data-open-on-error="{{ $errors->any() && old('fecha_permiso') ? 'true' : 'false' }}"
    data-personal-search-url="{{ route('permisos.buscar-personal') }}"
    data-old-personal-id="{{ old('personal_id') }}"
>
    <section
        class="max-h-[90vh] w-full max-w-2xl translate-y-2 overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 opacity-0 shadow-2xl transition duration-200 dark:border-gray-700 dark:bg-gray-900"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-crear-permiso-titulo"
        data-modal-panel
        tabindex="-1"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">
                    Permisos de personal
                </p>
                <h2 id="modal-crear-permiso-titulo" class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                    {{ $puedeRegistrarParaOtros ? 'Registrar permiso' : 'Solicitar permiso' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Captura la fecha, duración y motivo del permiso.
                </p>
            </div>

            <button
                type="button"
                class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 dark:hover:bg-gray-800 dark:hover:text-white"
                aria-label="Cerrar"
                data-modal-close
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('permisos.store') }}" class="mt-6 space-y-5">
            @csrf

            @if($puedeRegistrarParaOtros)
                <div class="relative">
                    <label for="permiso-buscar-personal" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Colaborador
                    </label>
                    <input
                        id="permiso-buscar-personal"
                        type="search"
                        autocomplete="off"
                        placeholder="Escribe nombre, número de empleado o correo…"
                        aria-controls="permiso-resultados-personal"
                        aria-autocomplete="list"
                        class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        data-personal-search
                    >
                    <input
                        id="permiso-personal-id"
                        type="hidden"
                        name="personal_id"
                        value="{{ old('personal_id') }}"
                        data-personal-id
                    >
                    <div
                        id="permiso-resultados-personal"
                        class="absolute z-10 mt-1 hidden max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800"
                        role="listbox"
                        data-personal-results
                    ></div>
                    <div class="mt-2 hidden rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 dark:border-emerald-900/50 dark:bg-emerald-950/30" data-personal-selected>
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Persona seleccionada</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white" data-personal-selected-name></p>
                                <p class="text-xs text-gray-600 dark:text-gray-400" data-personal-selected-meta></p>
                            </div>
                            <button type="button" class="text-sm font-semibold text-red-700 hover:underline dark:text-red-300" data-personal-clear>
                                Cambiar
                            </button>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" data-personal-search-hint>
                        Escribe al menos 2 caracteres para buscar en el personal.
                    </p>
                    @error('personal_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @else
                <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 dark:border-blue-900/50 dark:bg-blue-950/30">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-300">
                        Solicitud a nombre de
                    </p>
                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $personalPropio?->nombre_mostrar }}
                    </p>
                    <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">No. de empleado</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $personalPropio?->datosLaborales?->employee_id ?? 'Sin número registrado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Puesto</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $personalPropio?->datosLaborales?->puesto?->nombre ?? 'Sin puesto registrado' }}</dd>
                        </div>
                    </dl>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                        La solicitud se asociará al expediente vinculado con tu cuenta.
                    </p>
                </div>
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="permiso-fecha" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Fecha del permiso
                    </label>
                    <input
                        id="permiso-fecha"
                        type="date"
                        name="fecha_permiso"
                        value="{{ old('fecha_permiso') }}"
                        required
                        class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >
                    @error('fecha_permiso')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="permiso-horas" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Duración en horas
                    </label>
                    <input
                        id="permiso-horas"
                        type="number"
                        name="horas_solicitadas"
                        value="{{ old('horas_solicitadas') }}"
                        min="0.25"
                        max="24"
                        step="0.25"
                        required
                        placeholder="Ej. 3"
                        class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >
                    @error('horas_solicitadas')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="permiso-motivo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Motivo
                </label>
                <textarea
                    id="permiso-motivo"
                    name="motivo"
                    rows="4"
                    maxlength="5000"
                    required
                    placeholder="Describe brevemente el motivo del permiso"
                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                >{{ old('motivo') }}</textarea>
                @error('motivo')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-gray-200 pt-5 dark:border-gray-700 sm:flex-row sm:justify-end">
                <x-button type="button" variant="secondary" icon="close" data-modal-close>
                    Cancelar
                </x-button>

                <x-button type="submit" variant="success" icon="save">
                    {{ $puedeRegistrarParaOtros ? 'Registrar permiso' : 'Enviar solicitud' }}
                </x-button>
            </div>
        </form>
    </section>
</div>
