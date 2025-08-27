<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 👈 Faltaba esta importación

class RoleMiddleware
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if (!$user || !$user->rol) {
            abort(403);
        }

        // (Opcional) compara sin importar mayúsculas
        $nombreRol = $user->rol->nombre_rol ?? null;
        $roles = array_map('strtolower', $roles);

        if (!in_array(strtolower($nombreRol), $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
