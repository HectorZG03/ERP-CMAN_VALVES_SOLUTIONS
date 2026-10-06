<aside
    class="fixed inset-y-0 left-0 z-40 w-64
           bg-white dark:bg-gray-900
           border-r border-gray-200 dark:border-gray-700
           flex flex-col">

    {{-- Logo --}}
    <div class="h-20 flex items-center px-6 border-b border-gray-200 dark:border-gray-700">

        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">

            <img
                src="{{ asset('img/logo/logo.png') }}"
                alt="Logo"
                class="h-9 w-auto"
            >

            <div>
                <h1 class="text-lg font-bold text-gray-800 dark:text-white">
                    ERP CMAN
                </h1>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Sistema de gestión
                </p>
            </div>

        </a>

    </div>


    {{-- Navegación --}}
    <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6">

        {{-- Dashboard --}}
        <div>

            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                       text-sm font-medium transition
                       {{ request()->routeIs('dashboard')
                           ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                           : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
            >

                <i class="fas fa-house w-5 text-center"></i>

                <span>Dashboard</span>

            </a>

        </div>


        {{-- =========================================================
             OPERACIÓN
        ========================================================== --}}
        @if(
            auth()->user()->canManageInventory() ||
            auth()->user()->canManageInventoryadmin()
        )

            <div>

                <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider
                          text-gray-400 dark:text-gray-500">
                    Operación
                </p>

                <div class="space-y-1">

                    {{-- Inventario --}}
                    @if(
                        auth()->user()->canManageInventory() ||
                        auth()->user()->canManageInventoryadmin()
                    )

                        <a
                            href="{{ route('inventario.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                                   text-sm font-medium transition
                                   {{ request()->routeIs('inventario.*')
                                       ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                                       : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                        >

                            <i class="fas fa-boxes-stacked w-5 text-center"></i>

                            <span>Inventario</span>

                        </a>

                    @endif


                    {{-- Entradas --}}
                    @if(auth()->user()->canManageInventory())

                        <a
                            href="{{ route('entradas.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                                   text-sm font-medium transition
                                   {{ request()->routeIs('entradas.*')
                                       ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                                       : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                        >

                            <i class="fas fa-arrow-right-to-bracket w-5 text-center"></i>

                            <span>Entradas</span>

                        </a>

                    @endif


                    {{-- Salidas --}}
                    @if(auth()->user()->canManageInventory())

                        <a
                            href="{{ route('salidas.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                                   text-sm font-medium transition
                                   {{ request()->routeIs('salidas.*')
                                       ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                                       : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                        >

                            <i class="fas fa-arrow-right-from-bracket w-5 text-center"></i>

                            <span>Salidas</span>

                        </a>

                    @endif

                </div>

            </div>

        @endif


        {{-- =========================================================
             SOLICITUDES
        ========================================================== --}}
        <div>

            <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider
                      text-gray-400 dark:text-gray-500">
                Solicitudes
            </p>

            <div class="space-y-1">

                <a
                    href="{{ route('solicitudes.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                           text-sm font-medium transition
                           {{ request()->routeIs('solicitudes.*')
                               ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                               : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >

                    <i class="fas fa-clipboard-list w-5 text-center"></i>

                    <span>Solicitudes</span>

                </a>


                <a
                    href="{{ route('requisiciones.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                           text-sm font-medium transition
                           {{ request()->routeIs('requisiciones.*')
                               ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                               : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >

                    <i class="fas fa-file-lines w-5 text-center"></i>

                    <span>Requisiciones</span>

                </a>


                <a
                    href="{{ route('prestamos.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                           text-sm font-medium transition
                           {{ request()->routeIs('prestamos.*')
                               ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                               : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >

                    <i class="fas fa-hand-holding w-5 text-center"></i>

                    <span>Préstamos</span>

                </a>

            </div>

        </div>


        {{-- =========================================================
             FINANZAS
        ========================================================== --}}
        @if(auth()->user()->canManageFinanzas())

            <div>

                <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider
                          text-gray-400 dark:text-gray-500">
                    Finanzas
                </p>

                <a
                    href="{{ route('orden-compra.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                           text-sm font-medium transition
                           {{ request()->routeIs('orden-compra.*')
                               ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                               : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >

                    <i class="fas fa-file-invoice-dollar w-5 text-center"></i>

                    <span>Órdenes de compra</span>

                </a>

            </div>

        @endif


        {{-- =========================================================
             SEGURIDAD / EPP
        ========================================================== --}}
        @if(auth()->user()->canManageValeEPP())

            <div>

                <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider
                          text-gray-400 dark:text-gray-500">
                    Seguridad / EPP
                </p>

                <a
                    href="{{ route('valepp.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                           text-sm font-medium transition
                           {{ request()->routeIs('valepp.*')
                               ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                               : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >

                    <i class="fas fa-hard-hat w-5 text-center"></i>

                    <span>Vales de EPP</span>

                </a>

            </div>

        @endif


        {{-- =========================================================
             RECURSOS HUMANOS
        ========================================================== --}}
        @if(auth()->user()->canManagePersonal())

            <div>

                <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider
                          text-gray-400 dark:text-gray-500">
                    Recursos Humanos
                </p>

                <div class="space-y-1">

                    <a
                        href="{{ route('personal.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                               text-sm font-medium transition
                               {{ request()->routeIs('personal.*')
                                   ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                                   : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                    >

                        <i class="fas fa-users w-5 text-center"></i>

                        <span>Personal</span>

                    </a>


                    <a
                        href="{{ route('bajas.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                               text-sm font-medium transition
                               {{ request()->routeIs('bajas.*')
                                   ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                                   : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                    >

                        <i class="fas fa-user-minus w-5 text-center"></i>

                        <span>Bajas</span>

                    </a>


                    <a
                        href="{{ route('cambios.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                               text-sm font-medium transition
                               {{ request()->routeIs('cambios.*')
                                   ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                                   : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                    >

                        <i class="fas fa-user-pen w-5 text-center"></i>

                        <span>Cambios</span>

                    </a>

                </div>

            </div>

        @endif


        {{-- =========================================================
             ADMINISTRACIÓN
        ========================================================== --}}
        @if(
            auth()->user()->canManageUsers() ||
            auth()->user()->canManageInventory()
        )

            <div>

                <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider
                          text-gray-400 dark:text-gray-500">
                    Administración
                </p>

                <div class="space-y-1">

                    {{-- Usuarios --}}
                    @if(auth()->user()->canManageUsers())

                        <a
                            href="{{ route('users.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                                   text-sm font-medium transition
                                   {{ request()->routeIs('users.*')
                                       ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                                       : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                        >

                            <i class="fas fa-user-shield w-5 text-center"></i>

                            <span>Usuarios</span>

                        </a>

                    @endif


                    {{-- Proveedores --}}
                    @if(
                        auth()->user()->canManageInventory() ||
                        auth()->user()->canManageUsers()
                    )

                        <a
                            href="{{ route('proveedores.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                                   text-sm font-medium transition
                                   {{ request()->routeIs('proveedores.*')
                                       ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                                       : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                        >

                            <i class="fas fa-truck-field w-5 text-center"></i>

                            <span>Proveedores</span>

                        </a>

                    @endif

                </div>

            </div>

        @endif

    </nav>


    {{-- =========================================================
         USUARIO
    ========================================================== --}}
    <div class="border-t border-gray-200 dark:border-gray-700 p-4">

        <div class="flex items-center gap-3 mb-4">

            <div
                class="w-10 h-10 rounded-full
                       bg-yellow-500
                       flex items-center justify-center
                       text-white font-bold shrink-0"
            >

                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}

            </div>


            <div class="min-w-0">

                <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">
                    {{ Auth::user()->name }}
                </p>

                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ Auth::user()->role ?? 'Usuario' }}
                </p>

            </div>

        </div>


        <form method="POST" action="{{ route('logout') }}">

            @csrf

            <button
                type="submit"
                class="w-full flex items-center justify-center gap-2
                       px-3 py-2 rounded-lg
                       text-sm font-medium
                       text-red-600
                       hover:bg-red-50
                       dark:text-red-400
                       dark:hover:bg-red-900/20
                       transition"
            >

                <i class="fas fa-right-from-bracket"></i>

                <span>Cerrar sesión</span>

            </button>

        </form>

    </div>

</aside>