@php
    $mostrarModalConErrores = $errors->has('personal_id')
        || $errors->has('fecha_baja')
        || $errors->has('motivo_baja');
@endphp

<div
    id="baja-modal"
    data-reopen="{{ $mostrarModalConErrores ? '1' : '0' }}"
    class="invisible pointer-events-none fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-950/60 p-4 opacity-0 transition-opacity duration-200"
    aria-hidden="true"
>
    <section
        id="baja-modal-panel"
        role="dialog"
        aria-modal="true"
        aria-labelledby="baja-modal-title"
        class="my-auto w-full max-w-2xl translate-y-2 scale-95 rounded-2xl bg-white p-6 opacity-0 shadow-2xl transition-all duration-200 dark:bg-gray-800"
    >
        <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-4 dark:border-gray-700">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-red-600 dark:text-red-400">Recursos Humanos</p>
                <h2 id="baja-modal-title" class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                    Registrar baja de colaborador
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Busca y selecciona a un colaborador activo para registrar su baja.
                </p>
            </div>

            <button
                type="button"
                class="close-baja-modal inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-gray-300 dark:hover:bg-gray-700"
                aria-label="Cerrar ventana"
            >
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('bajas.store') }}" class="mt-5 space-y-4">
            @csrf

            <!-- Selección de Colaborador (Ocupa todo el ancho) -->
            <div>
                <label for="buscar-personal-baja" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">
                    Buscar colaborador activo
                </label>
                <input
                    id="buscar-personal-baja"
                    type="search"
                    autocomplete="off"
                    data-search-url="{{ route('bajas.buscar-personal') }}"
                    placeholder="Escribe nombre o código de empleado"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    aria-describedby="estado-busqueda-personal-baja"
                >

                <p id="estado-busqueda-personal-baja" class="mt-2 hidden text-sm text-gray-500 dark:text-gray-400" aria-live="polite"></p>
                <div id="resultados-personal-baja" class="mt-2 hidden max-h-52 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-600" role="listbox" aria-label="Resultados de colaboradores"></div>
                @error('personal_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Colaborador seleccionado (Ocupa todo el ancho) -->
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40" aria-live="polite">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Colaborador seleccionado</p>
                <p id="baja-modal-person" class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">Selecciona un colaborador de la lista.</p>
                <p id="baja-modal-employee" class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"></p>
            </div>

            <input id="baja-personal-id" type="hidden" name="personal_id" value="{{ old('personal_id') }}">

            <!-- Grid de dos columnas para Fecha y Motivo (Optimiza el espacio vertical) -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <!-- Fecha de baja -->
                <div class="sm:col-span-1">
                    <label for="fecha-baja" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">
                        Fecha de baja
                    </label>
                    <input
                        id="fecha-baja"
                        type="date"
                        name="fecha_baja"
                        value="{{ old('fecha_baja', now()->toDateString()) }}"
                        required
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                    @error('fecha_baja')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Motivo de la baja -->
                <div class="sm:col-span-2">
                    <label for="motivo-baja" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">
                        Motivo de la baja
                    </label>
                    <textarea
                        id="motivo-baja"
                        name="motivo_baja"
                        rows="2"
                        maxlength="2000"
                        required
                        class="block w-full resize-y rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="Describe el motivo de la baja"
                    >{{ old('motivo_baja') }}</textarea>
                    @error('motivo_baja')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col-reverse gap-2 border-t border-gray-200 pt-4 sm:flex-row sm:justify-end dark:border-gray-700">
                <button
                    type="button"
                    class="close-baja-modal rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                >
                    <i class="fas fa-user-minus" aria-hidden="true"></i>
                    Registrar baja
                </button>
            </div>
        </form>
    </section>
</div>