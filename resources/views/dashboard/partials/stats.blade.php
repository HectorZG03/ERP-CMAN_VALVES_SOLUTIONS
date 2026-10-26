<div>
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
            Resumen
        </h2>

        <p class="text-sm text-gray-500 dark:text-gray-400">
            Información relevante según tu perfil
        </p>
    </div>

    @php
        /*
         * Se define únicamente la información de las tarjetas.
         * El HTML de cada tarjeta se mantiene en un solo lugar.
         */
        $stats = [];

        if ($user->canApproveRequests()) {
            $stats = [
                [
                    'label' => 'Solicitudes',
                    'key' => 'solicitudes',
                    'icon' => 'fas fa-clipboard-list',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                ],
                [
                    'label' => 'Requisiciones',
                    'key' => 'requisiciones',
                    'icon' => 'fas fa-file-invoice',
                    'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                    'text' => 'text-purple-600 dark:text-purple-400',
                ],
                [
                    'label' => 'Pendientes',
                    'key' => 'pendientes',
                    'icon' => 'fas fa-clock',
                    'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                    'text' => 'text-yellow-600 dark:text-yellow-400',
                ],
            ];
        } elseif ($user->canApproveFinanzas()) {
            $stats = [
                [
                    'label' => 'Requisiciones',
                    'key' => 'requisiciones',
                    'icon' => 'fas fa-file-invoice',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                ],
                [
                    'label' => 'Pendientes',
                    'key' => 'pendientes',
                    'icon' => 'fas fa-clock',
                    'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                    'text' => 'text-yellow-600 dark:text-yellow-400',
                ],
                [
                    'label' => 'Aprobadas',
                    'key' => 'aprobadas',
                    'icon' => 'fas fa-check-circle',
                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                    'text' => 'text-green-600 dark:text-green-400',
                ],
                [
                    'label' => 'Denegadas',
                    'key' => 'denegadas',
                    'icon' => 'fas fa-times-circle',
                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                    'text' => 'text-red-600 dark:text-red-400',
                ],
            ];
        } elseif ($user->canManageInventory()) {
            $stats = [
                [
                    'label' => 'Inventario',
                    'key' => 'inventario',
                    'icon' => 'fas fa-boxes-stacked',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                ],
                [
                    'label' => 'Agotados',
                    'key' => 'agotados',
                    'icon' => 'fas fa-box-open',
                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                    'text' => 'text-red-600 dark:text-red-400',
                ],
                [
                    'label' => 'Bajo stock',
                    'key' => 'bajoStock',
                    'icon' => 'fas fa-triangle-exclamation',
                    'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                    'text' => 'text-yellow-600 dark:text-yellow-400',
                ],
                [
                    'label' => 'Solicitudes',
                    'key' => 'solicitudes',
                    'icon' => 'fas fa-clipboard-list',
                    'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                    'text' => 'text-purple-600 dark:text-purple-400',
                ],
            ];
        } elseif ($user->canManagePersonal()) {
            $stats = [
                [
                    'label' => 'Personal activo',
                    'key' => 'personalActivo',
                    'icon' => 'fas fa-users',
                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                    'text' => 'text-green-600 dark:text-green-400',
                ],
                [
                    'label' => 'Personal dado de baja',
                    'key' => 'personalBaja',
                    'icon' => 'fas fa-user-minus',
                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                    'text' => 'text-red-600 dark:text-red-400',
                ],
            ];
        } elseif ($user->canManageValeEPP()) {
            $stats = [
                [
                    'label' => 'Vales pendientes',
                    'key' => 'valesPendientes',
                    'icon' => 'fas fa-file-signature',
                    'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                    'text' => 'text-yellow-600 dark:text-yellow-400',
                ],
                [
                    'label' => 'Vales aprobados',
                    'key' => 'valesAprobados',
                    'icon' => 'fas fa-check-circle',
                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                    'text' => 'text-green-600 dark:text-green-400',
                ],
                [
                    'label' => 'Vales entregados',
                    'key' => 'valesEntregados',
                    'icon' => 'fas fa-box',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                ],
            ];
        } elseif ($user->canManageUsers()) {
            $stats = [
                [
                    'label' => 'Usuarios',
                    'key' => 'usuarios',
                    'icon' => 'fas fa-users-gear',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                ],
                [
                    'label' => 'Solicitudes',
                    'key' => 'solicitudes',
                    'icon' => 'fas fa-clipboard-list',
                    'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                    'text' => 'text-purple-600 dark:text-purple-400',
                ],
                [
                    'label' => 'Requisiciones',
                    'key' => 'requisiciones',
                    'icon' => 'fas fa-file-invoice',
                    'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                    'text' => 'text-yellow-600 dark:text-yellow-400',
                ],
            ];
        } else {
            $stats = [
                [
                    'label' => 'Solicitudes pendientes',
                    'key' => 'solicitudesPendientes',
                    'icon' => 'fas fa-clock',
                    'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                    'text' => 'text-yellow-600 dark:text-yellow-400',
                ],
                [
                    'label' => 'Solicitudes aprobadas',
                    'key' => 'solicitudesAprobadas',
                    'icon' => 'fas fa-check-circle',
                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                    'text' => 'text-green-600 dark:text-green-400',
                ],
                [
                    'label' => 'Requisiciones pendientes',
                    'key' => 'requisicionesPendientes',
                    'icon' => 'fas fa-file-invoice',
                    'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                    'text' => 'text-purple-600 dark:text-purple-400',
                ],
                [
                    'label' => 'Requisiciones aprobadas',
                    'key' => 'requisicionesAprobadas',
                    'icon' => 'fas fa-circle-check',
                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                    'text' => 'text-green-600 dark:text-green-400',
                ],
            ];
        }

        $gridColumns = match (count($stats)) {
            2 => 'sm:grid-cols-2',
            3 => 'sm:grid-cols-2 lg:grid-cols-3',
            default => 'sm:grid-cols-2 lg:grid-cols-4',
        };
    @endphp

    <div class="grid grid-cols-1 {{ $gridColumns }} gap-4">
        @foreach($stats as $stat)
            @php
                $cardBackground = match (true) {
                    str_contains($stat['bg'], 'red') => 'bg-rose-50 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900/40',
                    str_contains($stat['bg'], 'yellow') => 'bg-amber-50 dark:bg-amber-950/20 border-amber-100 dark:border-amber-900/40',
                    str_contains($stat['bg'], 'green') => 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/40',
                    str_contains($stat['bg'], 'purple') => 'bg-violet-50 dark:bg-violet-950/20 border-violet-100 dark:border-violet-900/40',
                    default => 'bg-blue-50 dark:bg-blue-950/20 border-blue-100 dark:border-blue-900/40',
                };
            @endphp
            <div
                data-dashboard-animate
                class="{{ $cardBackground }} rounded-xl border shadow-sm p-5"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $stat['label'] }}
                        </p>

                        <p class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ $dashboard['resumen'][$stat['key']] ?? 0 }}
                        </p>
                    </div>

                    <div class="w-12 h-12 rounded-lg {{ $stat['bg'] }} flex items-center justify-center">
                        <i class="{{ $stat['icon'] }} {{ $stat['text'] }} text-xl"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
