<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFinanzasAccess
{
    /**
     * Solo finanzas y auxiliar de finanzas pueden acceder a las órdenes de compra.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowedRoles = ['finanzas', 'aux_finanzas'];

        if (in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'No tienes permisos para acceder a las Órdenes de Compra. Tu rol actual es: "' . $user->role . '"');
    }
}