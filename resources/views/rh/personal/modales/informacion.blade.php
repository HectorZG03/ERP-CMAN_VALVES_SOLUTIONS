<div
    id="personal-info-modal"
    class="fixed inset-0 z-[100] invisible pointer-events-none flex items-center justify-center overflow-y-auto bg-slate-950/60 p-4 opacity-0 backdrop-blur-sm transition-opacity duration-200"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-labelledby="personal-info-title"
>
    <section
        id="personal-info-panel"
        class="my-auto max-h-[calc(100vh-2rem)] w-full max-w-3xl translate-y-2 scale-95 overflow-y-auto rounded-2xl border border-indigo-200 bg-white opacity-0 shadow-2xl transition duration-200 dark:border-indigo-900/60 dark:bg-gray-800"
    >
        <header class="flex items-start justify-between gap-4 border-b border-indigo-100 bg-indigo-50 px-6 py-5 dark:border-indigo-900/50 dark:bg-indigo-950/40">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">
                    Ficha informativa
                </p>

                <h2 id="personal-info-title" class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                    Datos del colaborador
                </h2>

                <p id="info-personal-name" class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    —
                </p>
            </div>

            <x-button type="button" class="close-personal-info-modal" variant="ghost" size="sm" icon="close" icon-only aria-label="Cerrar" title="Cerrar" />
        </header>

        <div class="space-y-6 p-6">
            @php
                $sections = [
                    'Laboral' => [
                        'employee-id' => 'Número de empleado',
                        'status' => 'Estado laboral',
                        'area' => 'Área',
                        'department' => 'Departamento',
                        'position' => 'Puesto',
                        'hire-date' => 'Fecha de ingreso',
                    ],
                    'Contacto' => [
                        'email' => 'Correo electrónico',
                        'phone' => 'Teléfono',
                    ],
                    'Personal y Fiscal' => [
                        'curp' => 'CURP',
                        'rfc' => 'RFC',
                        'nss' => 'NSS',
                        'sex' => 'Sexo',
                        'birth-date' => 'Fecha de nacimiento',
                        'nationality' => 'Nacionalidad',
                        'civil-status' => 'Estado civil',
                        'address' => 'Dirección',
                    ],
                ];
            @endphp

            @foreach ($sections as $sectionTitle => $fields)
                <div>
                    <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                        {{ $sectionTitle }}
                    </h3>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($fields as $key => $label)
                            <div class="min-w-0 rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40 @if($key === 'address') sm:col-span-2 lg:col-span-3 @endif">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    {{ $label }}
                                </p>

                                <p
                                    id="info-{{ $key }}"
                                    class="mt-1 break-words text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    —
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <footer class="flex justify-end border-t border-gray-200 px-6 py-4 dark:border-gray-700">
            <x-button type="button" class="close-personal-info-modal" variant="secondary" icon="close">Cerrar</x-button>
        </footer>
    </section>
</div>
