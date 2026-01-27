<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registrar middlewares personalizados
        $middleware->alias([
            'inventory.access' => \App\Http\Middleware\CheckInventoryAccess::class,
            'user.access' => \App\Http\Middleware\CheckUserAccess::class,
            'rh.access' => \App\Http\Middleware\CheckRHAccess::class,
            'hse.access' => \App\Http\Middleware\CheckHSEAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();