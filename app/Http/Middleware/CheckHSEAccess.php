<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHSEAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Solo HSE y Auxiliar de HSE pueden acceder
        $allowedRoles = ['hse', 'aux_hse'];
        
        if (in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'No tienes permisos para acceder a HSE. Tu rol actual es: "' . $user->role . '"');
    }
}