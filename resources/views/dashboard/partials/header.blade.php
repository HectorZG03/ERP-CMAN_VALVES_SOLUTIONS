<div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

    <div>

        <div class="flex items-center gap-3">

            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">

                <i class="fas fa-chart-line text-gray-600 dark:text-gray-300"></i>

            </div>

            <div>

                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Dashboard
                </h1>

                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Bienvenido, {{ $user->name }}
                </p>

            </div>

        </div>

    </div>


    <div class="text-sm text-gray-500 dark:text-gray-400">

        {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}

    </div>

</div>