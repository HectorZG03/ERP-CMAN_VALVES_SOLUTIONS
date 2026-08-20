<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-users', function ($user) {
            return in_array($user->role, ['ti', 'direccion']);
        });

        Gate::define('manage-inventory', function ($user) {
            return in_array($user->role, ['almacen', 'aux_almacen']);
        });

        Gate::define('approve-requests', function ($user) {
            return $user->role === 'direccion';
        });

        Gate::define('approve-finanzas', function ($user) {
            return in_array($user->role, ['finanzas', 'aux_finanzas']);
        });

        Gate::define('debug-role', function ($user) {
            \Log::info('Usuario: ' . $user->name . ' - Rol: ' . $user->role);

            return true;
        });
    }
}