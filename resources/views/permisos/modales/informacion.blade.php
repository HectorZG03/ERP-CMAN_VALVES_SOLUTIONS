<div
    id="modal-info-permiso"
    class="fixed inset-0 z-[90] hidden items-center justify-center bg-slate-950/60 p-4 opacity-0 transition-opacity duration-200"
    aria-hidden="true"
    data-modal
    data-can-review-rh="{{ $puedeRevisarRh ? 'true' : 'false' }}"
    data-can-review-finanzas="{{ $puedeRevisarFinanzas ? 'true' : 'false' }}"
    data-can-resolve="{{ $puedeResolver ? 'true' : 'false' }}"
    data-open-on-error="{{ $errors->any() && old('permiso_id') ? 'true' : 'false' }}"
    data-error-permiso-id="{{ old('permiso_id') }}"
>
    <section
        class="max-h-[90vh] w-full max-w-2xl translate-y-2 overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 opacity-0 shadow-2xl transition duration-200 dark:border-gray-700 dark:bg-gray-900"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-info-permiso-titulo"
        data-modal-panel
        tabindex="-1"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-400">
                    Detalle de solicitud
                </p>
                <h2 id="modal-info-permiso-titulo" class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                    Información del permiso
                </h2>
            </div>

            <button
                type="button"
                class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-gray-800 dark:hover:text-white"
                aria-label="Cerrar"
                data-modal-close
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="mt-6">
            <div
                class="rounded-lg bg-gray-50 px-4 py-8 text-center text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                data-permiso-loading
            >
                Cargando información…
            </div>

            <div
                class="hidden rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300"
                data-permiso-error
                role="alert"
            ></div>

            <div class="hidden space-y-5" data-permiso-content>
                <div class="grid gap-4 rounded-xl border border-gray-200 p-4 dark:border-gray-700 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Colaborador
                        </p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white" data-detalle-personal>—</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Etapa actual
                        </p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white" data-detalle-etapa>—</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Fecha del permiso
                        </p>
                        <p class="mt-1 text-gray-800 dark:text-gray-200" data-detalle-fecha>—</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Duración
                        </p>
                        <p class="mt-1 text-gray-800 dark:text-gray-200" data-detalle-horas>—</p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Motivo
                        </p>
                        <p class="mt-1 whitespace-pre-line text-gray-800 dark:text-gray-200" data-detalle-motivo>—</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Registrado por
                        </p>
                        <p class="mt-1 text-gray-800 dark:text-gray-200" data-detalle-registrado-por>—</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Revisión <span data-detalle-rol-revisor>RH</span>
                        </p>
                        <p class="mt-1 text-gray-800 dark:text-gray-200" data-detalle-revisado-por>—</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" data-detalle-revisado-at></p>
                    </div>

                    <div class="sm:col-span-2" data-detalle-comentario-revision-contenedor>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Comentario de <span data-detalle-rol-revisor>RH</span>
                        </p>
                        <p class="mt-1 whitespace-pre-line text-gray-800 dark:text-gray-200" data-detalle-comentario-revision>—</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Goce de sueldo
                        </p>
                        <p class="mt-1 text-gray-800 dark:text-gray-200" data-detalle-goce>—</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Resuelto por Dirección
                        </p>
                        <p class="mt-1 text-gray-800 dark:text-gray-200" data-detalle-resuelto-por>—</p>
                    </div>

                    <div class="sm:col-span-2" data-detalle-comentario-resolucion-contenedor>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Comentario de Dirección
                        </p>
                        <p class="mt-1 whitespace-pre-line text-gray-800 dark:text-gray-200" data-detalle-comentario-resolucion>—</p>
                    </div>
                </div>

                @if($puedeRevisar)
                    <form
                        method="POST"
                        action=""
                        class="hidden rounded-xl border border-blue-200 bg-blue-50/70 p-4 dark:border-blue-900/50 dark:bg-blue-950/20"
                        data-form-revision
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="permiso_id"
                            value="{{ old('permiso_id') }}"
                            data-revision-permiso-id
                        >

                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            Registrar Vo. Bo. de <span data-form-revision-role>Recursos Humanos</span>
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            Esta revisión no aprueba ni rechaza el permiso. Después de registrarla, Dirección podrá resolverlo.
                        </p>

                        <div class="mt-4">
                            <label for="permiso-comentario-revision" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Comentario de revisión
                                <span class="font-normal text-gray-500">(opcional)</span>
                            </label>
                            <textarea
                                id="permiso-comentario-revision"
                                name="comentario_revision"
                                rows="3"
                                maxlength="5000"
                                class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            >{{ old('comentario_revision') }}</textarea>
                            @error('comentario_revision')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4 flex justify-end">
                            <x-button type="submit" variant="info" icon="check">
                                Registrar revisión
                            </x-button>
                        </div>
                    </form>
                @endif

                @if($puedeResolver)
                    <form
                        method="POST"
                        action=""
                        class="hidden rounded-xl border border-amber-200 bg-amber-50/70 p-4 dark:border-amber-900/50 dark:bg-amber-950/20"
                        data-form-resolver
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="permiso_id"
                            value="{{ old('permiso_id') }}"
                            data-resolver-permiso-id
                        >

                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            Resolver solicitud
                        </h3>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="permiso-resolucion" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Decisión
                                </label>
                                <select
                                    id="permiso-resolucion"
                                    name="estatus"
                                    required
                                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                >
                                    <option value="">Selecciona una decisión</option>
                                    <option value="aprobado" @selected(old('estatus') === 'aprobado')>Aprobar</option>
                                    <option value="rechazado" @selected(old('estatus') === 'rechazado')>Rechazar</option>
                                </select>
                                @error('estatus')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div data-goce-field>
                                <label for="permiso-goce-sueldo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Goce de sueldo
                                </label>
                                <select
                                    id="permiso-goce-sueldo"
                                    name="goce_sueldo"
                                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                >
                                    <option value="">Selecciona una opción</option>
                                    <option value="1" @selected(old('goce_sueldo') === '1')>Con goce</option>
                                    <option value="0" @selected(old('goce_sueldo') === '0')>Sin goce</option>
                                </select>
                                @error('goce_sueldo')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="permiso-comentario-resolucion" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Comentario de resolución
                                    <span class="font-normal text-gray-500">(opcional)</span>
                                </label>
                                <textarea
                                    id="permiso-comentario-resolucion"
                                    name="comentario_resolucion"
                                    rows="3"
                                    maxlength="5000"
                                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                >{{ old('comentario_resolucion') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <x-button type="submit" variant="success" icon="check">
                                Guardar resolución
                            </x-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="mt-6 flex justify-end border-t border-gray-200 pt-4 dark:border-gray-700">
            <x-button type="button" variant="secondary" icon="close" data-modal-close>
                Cerrar
            </x-button>
        </div>
    </section>
</div>
