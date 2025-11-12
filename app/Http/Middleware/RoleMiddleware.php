<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Uso en rutas:
     *   ->middleware(['auth','role:company'])
     *   ->middleware(['auth','role:student'])
     *   ->middleware(['auth','role:4']) // si usas ids numéricos
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        // Compatibilidad con ambos enfoques:
        $slug = $user->role->slug ?? null;            // si tienes tabla roles (role_id + relación)
        $id   = isset($user->role_id) ? (string)$user->role_id : null;  // id numérico
        $legacy = $user->role ?? null;                // legacy: columna users.role (string)

        foreach ($roles as $r) {
            if ($r === $slug || $r === $id || $r === $legacy) {
                return $next($request);
            }
        }

        abort(403);
    }
}
