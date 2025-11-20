<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentIsValidated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Solo aplicar a usuarios con rol de estudiante
        if ($user && $user->role === 'student') {
            // Recargar la relación student desde la base de datos para evitar cache
            $student = $user->student()->first();

            // Si el estudiante no está validado, redirigir a página de espera
            if ($student && !$student->validated) {
                // Permitir acceso a rutas específicas (logout, waiting)
                if (!$request->routeIs('student.waiting', 'logout')) {
                    return redirect()->route('student.waiting');
                }
            }
        }

        return $next($request);
    }
}
