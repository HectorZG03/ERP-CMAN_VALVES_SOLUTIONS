<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Solo TI y dirección pueden gestionar usuarios
        $allowedRoles = ['ti', 'direccion'];
        
        if (in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'No tienes permisos para gestionar usuarios. Tu rol actual es: "' . $user->role . '"');
    }
}