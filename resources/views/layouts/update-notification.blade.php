@auth

    {{-- ============================================================
         AVISO DE NUEVA ACTUALIZACIÓN
         ============================================================ --}}

    <div
        id="update-notification"
        class="fixed inset-0 z-[9999] hidden items-center justify-center
               px-4 py-6"
        aria-labelledby="update-notification-title"
        role="dialog"
        aria-modal="true"
    >

        {{-- Fondo oscuro --}}
        <div
            id="update-notification-overlay"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        ></div>


        {{-- ========================================================
             TARJETA PRINCIPAL
             ======================================================== --}}
        <div
            id="update-notification-card"
            class="relative mx-auto w-full max-w-2xl overflow-hidden
                   rounded-2xl bg-white shadow-2xl
                   dark:bg-gray-800"
        >

            {{-- ====================================================
                 ENCABEZADO
                 ==================================================== --}}
            <div
                class="relative bg-gradient-to-r
                       from-yellow-400 to-yellow-500
                       px-7 py-6"
            >

                <div class="flex items-start justify-between gap-5">

                    <div class="flex min-w-0 items-center gap-4">

                        {{-- Saludo --}}
                        <div class="min-w-0">

                            <p
                                class="text-sm font-medium text-yellow-900"
                            >
                                Nueva actualización
                            </p>

                            <h2
                                id="update-notification-title"
                                class="mt-1 text-2xl font-bold text-white"
                            >
                                ¡Hola, {{ auth()->user()->name }}! 👋
                            </h2>

                        </div>

                    </div>


                    {{-- Botón cerrar --}}
                    <button
                        type="button"
                        id="update-notification-close"
                        class="flex h-9 w-9 shrink-0 items-center
                               justify-center rounded-full
                               bg-white/20 text-white
                               transition hover:bg-white/30
                               focus:outline-none
                               focus:ring-2 focus:ring-white/50"
                        aria-label="Cerrar"
                    >
                        <i class="fas fa-times"></i>
                    </button>

                </div>

            </div>


            {{-- ====================================================
                 CONTENIDO
                 ==================================================== --}}
            <div
                class="max-h-[65vh] overflow-y-auto px-7 py-6"
            >

                {{-- Introducción --}}
                <p
                    class="text-sm leading-6
                           text-gray-600 dark:text-gray-300"
                >
                    Hemos realizado varias mejoras en el ERP CMAN
                    para facilitar la navegación y optimizar algunos
                    de los módulos que utilizas diariamente.
                </p>


                {{-- ====================================================
                     MÓDULO ALMACÉN
                     ==================================================== --}}
                <div class="mt-6">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center
                                   justify-center rounded-xl
                                   bg-blue-100 text-blue-600
                                   dark:bg-blue-900/30
                                   dark:text-blue-400"
                        >
                            <i class="fas fa-boxes-stacked"></i>
                        </div>


                        <div class="min-w-0">

                            <h3
                                class="font-semibold
                                       text-gray-800 dark:text-white"
                            >
                                Almacén
                            </h3>

                            <p
                                class="text-xs
                                       text-gray-500 dark:text-gray-400"
                            >
                                Mejoras en la gestión de materiales
                            </p>

                        </div>

                    </div>


                    <ul
                        class="mt-3 space-y-2 pl-1 text-sm
                               text-gray-600 dark:text-gray-300"
                    >

                        <li class="flex gap-2">

                            <i
                                class="fas fa-check mt-1 shrink-0
                                       text-green-500"
                            ></i>

                            <span>
                                Mejoras en el módulo de inventario.
                            </span>

                        </li>


                        <li class="flex gap-2">

                            <i
                                class="fas fa-check mt-1 shrink-0
                                       text-green-500"
                            ></i>

                            <span>
                                Mejoras en el registro de entradas y salidas.
                            </span>

                        </li>


                        <li class="flex gap-2">

                            <i
                                class="fas fa-check mt-1 shrink-0
                                       text-green-500"
                            ></i>

                            <span>
                                Optimización del registro de solicitudes
                                de material.
                            </span>

                        </li>


                        <li class="flex gap-2">

                            <i
                                class="fas fa-check mt-1 shrink-0
                                       text-green-500"
                            ></i>

                            <span>
                                Mejoras en la generación de documentos
                                de salida.
                            </span>

                        </li>

                    </ul>

                </div>


                {{-- ====================================================
                     MÓDULO VALES EPP
                     ==================================================== --}}
                <div class="mt-7">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center
                                   justify-center rounded-xl
                                   bg-green-100 text-green-600
                                   dark:bg-green-900/30
                                   dark:text-green-400"
                        >
                            <i class="fas fa-helmet-safety"></i>
                        </div>


                        <div class="min-w-0">

                            <h3
                                class="font-semibold
                                       text-gray-800 dark:text-white"
                            >
                                Vales EPP
                            </h3>

                            <p
                                class="text-xs
                                       text-gray-500 dark:text-gray-400"
                            >
                                Mejoras en la gestión de protección personal
                            </p>

                        </div>

                    </div>


                    <ul
                        class="mt-3 space-y-2 pl-1 text-sm
                               text-gray-600 dark:text-gray-300"
                    >

                        <li class="flex gap-2">

                            <i
                                class="fas fa-check mt-1 shrink-0
                                       text-green-500"
                            ></i>

                            <span>
                                Mejoras en la gestión de vales de
                                protección personal.
                            </span>

                        </li>


                        <li class="flex gap-2">

                            <i
                                class="fas fa-check mt-1 shrink-0
                                       text-green-500"
                            ></i>

                            <span>
                                Mejor organización de la información
                                de los vales.
                            </span>

                        </li>


                        <li class="flex gap-2">

                            <i
                                class="fas fa-check mt-1 shrink-0
                                       text-green-500"
                            ></i>

                            <span>
                                Ajustes en la navegación y presentación
                                del módulo.
                            </span>

                        </li>

                    </ul>

                </div>


                {{-- ====================================================
                     INTERFAZ DEL SISTEMA
                     ==================================================== --}}
                <div class="mt-7">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center
                                   justify-center rounded-xl
                                   bg-purple-100 text-purple-600
                                   dark:bg-purple-900/30
                                   dark:text-purple-400"
                        >
                            <i class="fas fa-palette"></i>
                        </div>


                        <div class="min-w-0">

                            <h3
                                class="font-semibold
                                       text-gray-800 dark:text-white"
                            >
                                Interfaz del sistema
                            </h3>

                            <p
                                class="text-xs
                                       text-gray-500 dark:text-gray-400"
                            >
                                Una navegación más clara y organizada
                            </p>

                        </div>

                    </div>


                    <ul
                        class="mt-3 space-y-2 pl-1 text-sm
                               text-gray-600 dark:text-gray-300"
                    >

                        <li class="flex gap-2">

                            <i
                                class="fas fa-check mt-1 shrink-0
                                       text-green-500"
                            ></i>

                            <span>
                                Nuevo menú lateral de navegación.
                            </span>

                        </li>


                        <li class="flex gap-2">

                            <i
                                class="fas fa-check mt-1 shrink-0
                                       text-green-500"
                            ></i>

                            <span>
                                Módulos organizados por áreas.
                            </span>

                        </li>


                        <li class="flex gap-2">

                            <i
                                class="fas fa-check mt-1 shrink-0
                                       text-green-500"
                            ></i>

                            <span>
                                Mejoras generales en el modo oscuro.
                            </span>

                        </li>

                    </ul>

                </div>


                {{-- ====================================================
                     MENSAJE FINAL
                     ==================================================== --}}
                <div
                    class="mt-7 rounded-xl
                           bg-gray-50 px-5 py-4 text-center
                           dark:bg-gray-900/50"
                >

                    <p
                        class="text-sm font-medium
                               text-gray-700 dark:text-gray-200"
                    >
                        ¡Gracias por utilizar el ERP CMAN!
                    </p>

                    <p
                        class="mt-1 text-xs
                               text-gray-500 dark:text-gray-400"
                    >
                        Esperamos que estas mejoras hagan más sencillo
                        tu trabajo.
                    </p>

                </div>

            </div>


            {{-- ====================================================
                 FOOTER
                 ==================================================== --}}
            <div
                class="border-t border-gray-200
                       bg-gray-50 px-7 py-4
                       dark:border-gray-700
                       dark:bg-gray-900/30"
            >

                <button
                    type="button"
                    id="update-notification-confirm"
                    class="w-full rounded-xl
                           bg-yellow-500 px-5 py-3
                           text-sm font-semibold text-white
                           shadow-sm transition
                           hover:bg-yellow-600
                           focus:outline-none
                           focus:ring-2 focus:ring-yellow-400
                           focus:ring-offset-2
                           dark:focus:ring-offset-gray-800"
                >
                    Entendido
                </button>

            </div>

        </div>

    </div>


    {{-- ============================================================
         JAVASCRIPT DEL AVISO
         ============================================================ --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            /*
             * Identificador único de esta actualización.
             *
             * Cuando exista una nueva actualización importante,
             * solamente cambiaremos este valor.
             */
            const updateVersion = '2026-10-layout-almacen-epp';


            /*
             * La actualización se registra por usuario.
             *
             * De esta manera cada usuario verá el aviso una sola vez.
             */
            const storageKey =
                'erp_update_seen_' +
                updateVersion +
                '_user_{{ auth()->id() }}';


            const notification =
                document.getElementById('update-notification');

            const closeButton =
                document.getElementById('update-notification-close');

            const confirmButton =
                document.getElementById('update-notification-confirm');

            const overlay =
                document.getElementById('update-notification-overlay');


            /*
             * Si el elemento no existe, no hacemos nada.
             */
            if (!notification) {
                return;
            }


            /*
             * Función para cerrar el aviso.
             */
            function closeUpdateNotification() {

                notification.classList.add('hidden');

                notification.classList.remove('flex');

                localStorage.setItem(storageKey, '1');

            }


            /*
             * Mostrar el aviso solamente si el usuario
             * todavía no ha visto esta actualización.
             */
            if (!localStorage.getItem(storageKey)) {

                setTimeout(function () {

                    notification.classList.remove('hidden');

                    notification.classList.add('flex');

                }, 500);

            }


            /*
             * Cerrar mediante el botón X.
             */
            closeButton?.addEventListener(
                'click',
                closeUpdateNotification
            );


            /*
             * Cerrar mediante "Entendido".
             */
            confirmButton?.addEventListener(
                'click',
                closeUpdateNotification
            );


            /*
             * Cerrar haciendo clic fuera de la tarjeta.
             */
            overlay?.addEventListener(
                'click',
                closeUpdateNotification
            );


            /*
             * Cerrar utilizando la tecla ESC.
             */
            document.addEventListener('keydown', function (event) {

                if (event.key === 'Escape') {

                    closeUpdateNotification();

                }

            });

        });

    </script>

@endauth