<div>
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
            Acciones rápidas
        </h2>

        <p class="text-sm text-gray-500 dark:text-gray-400">
            Accesos frecuentes según tu perfil
        </p>
    </div>

    @php
        /*
         * Las acciones se definen como datos para evitar repetir el mismo
         * HTML en cada rol. Se conserva la misma visibilidad y las mismas rutas.
         */
        $actions = [];

        if ($user->canApproveRequests()) {
            $actions = [
                [
                    'label' => 'Solicitudes',
                    'description' => 'Revisar solicitudes',
                    'route' => 'solicitudes.index',
                    'icon' => 'fas fa-clipboard-check',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                    'hover' => 'hover:border-blue-300 dark:hover:border-blue-600',
                ],
                [
                    'label' => 'Requisiciones',
                    'description' => 'Revisar requisiciones',
                    'route' => 'requisiciones.index',
                    'icon' => 'fas fa-file-signature',
                    'bg' => 'bg-orange-100 dark:bg-orange-900/30',
                    'text' => 'text-orange-600 dark:text-orange-400',
                    'hover' => 'hover:border-orange-300 dark:hover:border-orange-600',
                ],
                [
                    'label' => 'Usuarios',
                    'description' => 'Administrar usuarios',
                    'route' => 'users.index',
                    'icon' => 'fas fa-users-cog',
                    'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                    'text' => 'text-purple-600 dark:text-purple-400',
                    'hover' => 'hover:border-purple-300 dark:hover:border-purple-600',
                ],
            ];
        } elseif ($user->canApproveFinanzas()) {
            $actions = [
                [
                    'label' => 'Requisiciones',
                    'description' => 'Revisar requisiciones',
                    'route' => 'requisiciones.index',
                    'icon' => 'fas fa-file-invoice-dollar',
                    'bg' => 'bg-orange-100 dark:bg-orange-900/30',
                    'text' => 'text-orange-600 dark:text-orange-400',
                    'hover' => 'hover:border-orange-300 dark:hover:border-orange-600',
                ],
                [
                    'label' => 'Órdenes de compra',
                    'description' => 'Gestionar compras',
                    'route' => 'orden-compra.index',
                    'icon' => 'fas fa-shopping-cart',
                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                    'text' => 'text-green-600 dark:text-green-400',
                    'hover' => 'hover:border-green-300 dark:hover:border-green-600',
                ],
            ];
        } elseif ($user->canManageInventory()) {
            $actions = [
                [
                    'label' => 'Inventario',
                    'description' => 'Gestionar inventario',
                    'route' => 'inventario.index',
                    'icon' => 'fas fa-boxes',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                    'hover' => 'hover:border-blue-300 dark:hover:border-blue-600',
                ],
                [
                    'label' => 'Entradas',
                    'description' => 'Registrar entradas',
                    'route' => 'entradas.index',
                    'icon' => 'fas fa-arrow-circle-down',
                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                    'text' => 'text-green-600 dark:text-green-400',
                    'hover' => 'hover:border-green-300 dark:hover:border-green-600',
                ],
                [
                    'label' => 'Salidas',
                    'description' => 'Registrar salidas',
                    'route' => 'salidas.index',
                    'icon' => 'fas fa-arrow-circle-up',
                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                    'text' => 'text-red-600 dark:text-red-400',
                    'hover' => 'hover:border-red-300 dark:hover:border-red-600',
                ],
                [
                    'label' => 'Solicitudes',
                    'description' => 'Gestionar solicitudes',
                    'route' => 'solicitudes.index',
                    'icon' => 'fas fa-clipboard-list',
                    'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                    'text' => 'text-purple-600 dark:text-purple-400',
                    'hover' => 'hover:border-purple-300 dark:hover:border-purple-600',
                ],
            ];
        } elseif ($user->canManagePersonal()) {
            $actions = [
                [
                    'label' => 'Personal',
                    'description' => 'Gestionar personal',
                    'route' => 'personal.index',
                    'icon' => 'fas fa-users',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                    'hover' => 'hover:border-blue-300 dark:hover:border-blue-600',
                ],
                [
                    'label' => 'Bajas',
                    'description' => 'Gestionar bajas',
                    'route' => 'bajas.index',
                    'icon' => 'fas fa-user-minus',
                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                    'text' => 'text-red-600 dark:text-red-400',
                    'hover' => 'hover:border-red-300 dark:hover:border-red-600',
                ],
            ];
        } elseif ($user->canManageValeEPP()) {
            $actions = [
                [
                    'label' => 'Vales de EPP',
                    'description' => 'Gestionar vales',
                    'route' => 'valepp.index',
                    'icon' => 'fas fa-hard-hat',
                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                    'text' => 'text-green-600 dark:text-green-400',
                    'hover' => 'hover:border-green-300 dark:hover:border-green-600',
                ],
            ];
        } elseif ($user->canManageUsers()) {
            $actions = [
                [
                    'label' => 'Usuarios',
                    'description' => 'Administrar usuarios',
                    'route' => 'users.index',
                    'icon' => 'fas fa-users-cog',
                    'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                    'text' => 'text-purple-600 dark:text-purple-400',
                    'hover' => 'hover:border-purple-300 dark:hover:border-purple-600',
                ],
                [
                    'label' => 'Inventario',
                    'description' => 'Consultar inventario',
                    'route' => 'inventario.index',
                    'icon' => 'fas fa-boxes',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                    'hover' => 'hover:border-blue-300 dark:hover:border-blue-600',
                ],
                [
                    'label' => 'Solicitudes',
                    'description' => 'Consultar solicitudes',
                    'route' => 'solicitudes.index',
                    'icon' => 'fas fa-clipboard-list',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                    'hover' => 'hover:border-blue-300 dark:hover:border-blue-600',
                ],
            ];
        } else {
            $actions = [
                [
                    'label' => 'Mis solicitudes',
                    'description' => 'Consultar mis solicitudes',
                    'route' => 'solicitudes.index',
                    'icon' => 'fas fa-clipboard-list',
                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                    'text' => 'text-blue-600 dark:text-blue-400',
                    'hover' => 'hover:border-blue-300 dark:hover:border-blue-600',
                ],
                [
                    'label' => 'Mis requisiciones',
                    'description' => 'Consultar mis requisiciones',
                    'route' => 'requisiciones.index',
                    'icon' => 'fas fa-file-alt',
                    'bg' => 'bg-orange-100 dark:bg-orange-900/30',
                    'text' => 'text-orange-600 dark:text-orange-400',
                    'hover' => 'hover:border-orange-300 dark:hover:border-orange-600',
                ],
            ];
        }
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($actions as $action)
            @php
                $actionCardBackground = match (true) {
                    str_contains($action['bg'], 'green') => 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/40',
                    str_contains($action['bg'], 'purple') => 'bg-violet-50 dark:bg-violet-950/20 border-violet-100 dark:border-violet-900/40',
                    str_contains($action['bg'], 'orange') => 'bg-orange-50 dark:bg-orange-950/20 border-orange-100 dark:border-orange-900/40',
                    str_contains($action['bg'], 'yellow') => 'bg-amber-50 dark:bg-amber-950/20 border-amber-100 dark:border-amber-900/40',
                    default => 'bg-blue-50 dark:bg-blue-950/20 border-blue-100 dark:border-blue-900/40',
                };
            @endphp
            <a
                data-dashboard-animate
                href="{{ route($action['route']) }}"
                class="group {{ $actionCardBackground }} rounded-xl border p-5 shadow-sm hover:shadow-md {{ $action['hover'] }} transition"
            >
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 shrink-0 rounded-xl {{ $action['bg'] }} flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="{{ $action['icon'] }} {{ $action['text'] }}"></i>
                    </div>

                    <div class="min-w-0">
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            {{ $action['label'] }}
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                            {{ $action['description'] }}
                        </p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
