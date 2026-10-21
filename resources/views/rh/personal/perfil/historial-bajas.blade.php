@php
    $bajas = $personal->bajas ?? collect();

    $totalBajasActivas = $bajas->where('estado', 'activa')->count();
    $totalBajasAnuladas = $bajas->where('estado', 'anulada')->count();
@endphp

<div id="personal-bajas-history"
     class="space-y-5"
     aria-label="Historial de bajas">

    <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                Historial de bajas
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Consulta las bajas activas y anuladas de este colaborador.
            </p>
        </div>

        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-red-800 dark:bg-red-900/40 dark:text-red-200">
                <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>
                Activas: {{ $totalBajasActivas }}
            </span>

            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1.5 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                <span class="h-1.5 w-1.5 rounded-full bg-gray-500"></span>
                Anuladas: {{ $totalBajasAnuladas }}
            </span>
        </div>
    </header>

    @if ($bajas->isEmpty())
        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center dark:border-gray-600 dark:bg-gray-900/30">
            <i class="fas fa-clipboard-list mb-3 text-2xl text-gray-400" aria-hidden="true"></i>
            <p class="font-semibold text-gray-700 dark:text-gray-200">
                No hay bajas registradas
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Las bajas de este colaborador aparecerán aquí.
            </p>
        </div>
    @else
        <section class="space-y-4">
            <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/30 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2">
                    <label for="historial-bajas-busqueda"
                           class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                        Buscar en el historial
                    </label>
                    <input id="historial-bajas-busqueda"
                           type="search"
                           autocomplete="off"
                           placeholder="Motivo, usuario que registró..."
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label for="historial-bajas-estado"
                           class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                        Estado
                    </label>
                    <select id="historial-bajas-estado"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                        <option value="todas">Todas</option>
                        <option value="activa">Activa</option>
                        <option value="anulada">Anulada</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button id="historial-bajas-limpiar"
                            type="button"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        Limpiar filtros
                    </button>
                </div>

                <div>
                    <label for="historial-bajas-desde"
                           class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                        Fecha desde
                    </label>
                    <input id="historial-bajas-desde"
                           type="date"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label for="historial-bajas-hasta"
                           class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                        Fecha hasta
                    </label>
                    <input id="historial-bajas-hasta"
                           type="date"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400"
                 aria-live="polite">
                <span>
                    Registros mostrados:
                    <strong id="historial-bajas-resultados" class="text-gray-700 dark:text-gray-200">
                        {{ $bajas->count() }}
                    </strong>
                    de {{ $bajas->count() }}
                </span>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Fecha de baja
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Motivo de baja
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Registro
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Estado
                            </th>
                        </tr>
                    </thead>

                    <tbody id="historial-bajas-filas"
                           class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        @foreach ($bajas->sortByDesc('fecha_baja') as $baja)
                            @php
                                $estadoBaja = $baja->estado;
                                $estaAnulada = $estadoBaja === 'anulada';

                                $textoBusqueda = implode(' ', array_filter([
                                    $baja->motivo_baja,
                                    $baja->user?->name,
                                    $baja->usuarioAnulacion?->name,
                                ]));
                            @endphp

                            <tr data-historial-baja
                                data-estado="{{ $estadoBaja }}"
                                data-fecha="{{ $baja->fecha_baja?->format('Y-m-d') }}"
                                data-busqueda="{{ \Illuminate\Support\Str::lower($textoBusqueda) }}"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/40">

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    {{ $baja->fecha_baja?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td class="max-w-lg px-4 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    <p class="whitespace-pre-line">
                                        {{ $baja->motivo_baja ?: 'Sin motivo especificado' }}
                                    </p>

                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    <div>
                                        Registró: {{ $baja->user?->name ?? '—' }}
                                    </div>

                                    @if ($estaAnulada)
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Anuló: {{ $baja->usuarioAnulacion?->name ?? '—' }}
                                        </div>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    @if ($estaAnulada)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-500"></span>
                                            Anulada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/40 dark:text-red-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>
                                            Activa
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p id="historial-bajas-sin-resultados"
                   class="hidden px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                    No hay registros que coincidan con los filtros.
                </p>
            </div>
        </section>
    @endif
</div>
