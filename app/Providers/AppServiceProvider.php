<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir gates para los permisos
        Gate::define('manage-users', function ($user) {
            return in_array($user->role, ['ti', 'direccion']);
        });

        Gate::define('manage-inventory', function ($user) {
            return in_array($user->role, ['almacen', 'aux_almacen']);
        });

        Gate::define('approve-requests', function ($user) {
            return $user->role === 'direccion';
        });

        // ✅ NUEVO: Gate para aprobación de finanzas
        Gate::define('approve-finanzas', function ($user) {
            return in_array($user->role, ['finanzas', 'aux_finanzas']);
        });

        // Gate adicional para debug
        Gate::define('debug-role', function ($user) {
            \Log::info('Usuario: ' . $user->name . ' - Rol: ' . $user->role);
            return true;
        });
    }
}