<header class="sticky top-0 z-30
               bg-white/95 dark:bg-gray-900/95
               backdrop-blur
               border-b border-gray-200 dark:border-gray-700">

    <div class="h-20 px-6 flex items-center justify-between">

        {{-- Información --}}
        <div>
            <p class="text-xs uppercase tracking-wider
                      text-gray-400 dark:text-gray-500">
                Sistema de gestión
            </p>

            <h2 class="text-lg font-semibold
                       text-gray-800 dark:text-white">
                ERP CMAN VALVES SOLUTION
            </h2>
        </div>


        {{-- Acciones --}}
        <div class="flex items-center gap-4">

            {{-- Tema --}}
            <button
                id="theme-toggle"
                type="button"
                aria-label="Cambiar tema"
                class="w-10 h-10 rounded-lg
                       flex items-center justify-center
                       text-gray-600 dark:text-gray-300
                       hover:bg-gray-100 dark:hover:bg-gray-800
                       transition">

                <i class="fas fa-sun dark:hidden"></i>
                <i class="fas fa-moon hidden dark:block"></i>

            </button>


            {{-- Usuario --}}
            <div class="hidden sm:flex items-center gap-3
                        pl-4 border-l border-gray-200 dark:border-gray-700">

                <div class="w-10 h-10 rounded-full
                            bg-yellow-500
                            flex items-center justify-center
                            text-white font-bold">

                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}

                </div>

                <div>

                    <p class="text-sm font-semibold
                              text-gray-800 dark:text-white">

                        {{ Auth::user()->name }}

                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400">

                        {{ Auth::user()->role ?? 'Usuario' }}

                    </p>

                </div>

            </div>

        </div>

    </div>

</header>