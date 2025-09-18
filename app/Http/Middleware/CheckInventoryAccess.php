<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInventoryAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // AGREGAMOS 'direccion' para que el administrador también tenga acceso
        $allowedRoles = ['almacen', 'aux_almacen', 'direccion', 'ti'];
        
        if (in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'No tienes permisos para acceder a esta sección. Tu rol actual es: "' . $user->role . '"');
    }
}