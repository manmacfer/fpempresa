<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Uso:
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

        // Compat total: slug (roles table), id numérico, string legacy
        $slug   = $user->role->slug ?? null;                 // p.ej. 'company'
        $id     = isset($user->role_id) ? (string)$user->role_id : null; // p.ej. '4'
        $legacy = $user->role ?? null;                       // p.ej. 'company'

        foreach ($roles as $r) {
            if ($r === $slug || $r === $id || $r === $legacy) {
                return $next($request);
            }
        }

        abort(403);
    }
}
