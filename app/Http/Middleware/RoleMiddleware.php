<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Uso en rutas:
     *   ->middleware(['auth','role:company'])
     *   ->middleware(['auth','role:student'])
     *   ->middleware(['auth','role:4']) // si usas ids numéricos (1 admin, 2 teacher, 3 student, 4 company)
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            return abort(403);
        }

        // Datos disponibles (soporta los 3 enfoques: tabla roles, role_id numérico y columna legacy 'role')
        $slug   = $user->role->slug ?? null;                    // p.ej. 'company'
        $id     = isset($user->role_id) ? (string)$user->role_id : null;  // p.ej. '4'
        $legacy = $user->role ?? null;                          // p.ej. 'company' (columna string en users)

        // Fallback "perfil existe"
        $hasCompany = method_exists($user, 'company') && $user->company()->exists();
        $hasStudent = method_exists($user, 'student') && $user->student()->exists();
        $hasTeacher = method_exists($user, 'teacher') && $user->teacher()->exists();

        // Normalización de los roles pedidos en la ruta
        $needles = array_map(function ($r) {
            return is_string($r) ? mb_strtolower(trim($r)) : (string)$r;
        }, $roles);

        $granted = false;

        // Coincidencias directas: slug / id / legacy
        foreach ($needles as $r) {
            if ($r !== '') {
                if ($slug && mb_strtolower($slug) === $r) { $granted = true; break; }
                if ($id   && $id === $r)                 { $granted = true; break; }
                if ($legacy && mb_strtolower($legacy) === $r) { $granted = true; break; }
            }
        }

        // Fallback por existencia de perfil (si se pidió 'company' o '4')
        if (!$granted && (in_array('company', $needles, true) || in_array('4', $needles, true))) {
            if ($hasCompany) { $granted = true; }
        }
        // Fallback por existencia de perfil (si se pidió 'student' o '3')
        if (!$granted && (in_array('student', $needles, true) || in_array('3', $needles, true))) {
            if ($hasStudent) { $granted = true; }
        }
        // Fallback por existencia de perfil (si se pidió 'teacher' o '2')
        if (!$granted && (in_array('teacher', $needles, true) || in_array('2', $needles, true))) {
            if ($hasTeacher) { $granted = true; }
        }

        if ($granted) {
            return $next($request);
        }

        // Log de diagnóstico para que veas rápido qué valor falta
        Log::warning('RoleMiddleware: acceso denegado', [
            'need'       => $needles,
            'user_id'    => $user->id,
            'slug'       => $slug,
            'role_id'    => $id,
            'legacy'     => $legacy,
            'hasCompany' => $hasCompany,
            'hasStudent' => $hasStudent,
            'path'       => $request->path(),
        ]);

        return abort(403);
    }
}
