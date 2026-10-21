@php
    $erroresBaja = $errors->hasAny([
        'personal_id',
        'fecha_baja',
        'motivo_baja',
    ]);
@endphp

<div id="baja-modal"
     class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-gray-950/60 p-4 opacity-0 invisible pointer-events-none transition-opacity duration-200 ease-out"
     data-reopen="{{ $erroresBaja ? '1' : '0' }}"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="baja-modal-title">

    <div id="baja-modal-panel"
         class="w-full max-w-lg translate-y-2 scale-95 rounded-2xl border border-red-200 bg-white opacity-0 shadow-2xl transition duration-200 ease-out dark:border-red-900/60 dark:bg-gray-800">

        <div class="flex items-start justify-between gap-4 border-b border-red-100 px-6 py-5 dark:border-red-900/50">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-red-600 dark:text-red-400">
                    Recursos Humanos
                </p>

                <h2 id="baja-modal-title" class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                    Registrar baja
                </h2>

                <p id="baja-modal-person" class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    Selecciona un colaborador de la lista.
                </p>

                <p id="baja-modal-employee" class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"></p>
            </div>

            <button type="button"
                    class="close-baja-modal inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-gray-300 dark:hover:bg-gray-700"
                    aria-label="Cerrar modal">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('bajas.store') }}" class="space-y-5 px-6 py-5">
            @csrf

            <input type="hidden"
                   id="baja-personal-id"
                   name="personal_id"
                   value="{{ $erroresBaja ? old('personal_id') : '' }}">

            @error('personal_id')
                <p class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-200">
                    {{ $message }}
                </p>
            @enderror

            <div>
                <label for="fecha_baja" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">
                    Fecha efectiva de baja <span class="text-red-600">*</span>
                </label>

                <input id="fecha_baja"
                       name="fecha_baja"
                       type="date"
                       required
                       value="{{ $erroresBaja ? old('fecha_baja') : now()->toDateString() }}"
                       class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                @error('fecha_baja')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="motivo_baja" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">
                    Motivo de la baja <span class="text-red-600">*</span>
                </label>

                <textarea id="motivo_baja"
                          name="motivo_baja"
                          rows="4"
                          required
                          maxlength="2000"
                          placeholder="Describe el motivo de la baja..."
                          class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ $erroresBaja ? old('motivo_baja') : '' }}</textarea>

                @error('motivo_baja')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-gray-200 pt-4 sm:flex-row sm:justify-end dark:border-gray-700">
                <button type="button"
                        class="close-baja-modal inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    Cancelar
                </button>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Registrar baja
                </button>
            </div>
        </form>
    </div>
</div>