<?php

namespace App\Policies;

use App\Models\Matching;
use App\Models\User;

class MatchingPolicy
{
    /**
     * Admin puede todo.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Listar matchings.
     * Permitimos a todos los roles relevantes; filtraremos por alcance en el controlador.
     */
    public function viewAny(User $user): bool
    {
        return $user->isProfessor() || $user->isCompany() || $user->isStudent();
    }

    /**
     * Ver un matching concreto.
     */
    public function view(User $user, Matching $matching): bool
    {
        if ($user->isProfessor()) {
            return true;
        }

        // Es del estudiante
        if ($user->isStudent() && optional($user->student)->id === $matching->student_id) {
            return true;
        }

        // Es de la empresa
        if ($user->isCompany() && optional($user->company)->id === $matching->company_id) {
            return true;
        }

        return false;
    }

    /**
     * Crear matching.
     * Estudiante o Empresa pueden iniciar (p.ej., solicitud/propuesta).
     */
    public function create(User $user): bool
    {
        return $user->isProfessor() || $user->isCompany() || $user->isStudent();
    }

    /**
     * Actualizar matching (p.ej., cambiar estado).
     */
    public function update(User $user, Matching $matching): bool
    {
        if ($user->isProfessor()) {
            return true;
        }

        // El estudiante dueño puede actualizar (p.ej. cancelar, responder)
        if ($user->isStudent() && optional($user->student)->id === $matching->student_id) {
            return true;
        }

        // La empresa dueña puede actualizar (p.ej. aceptar/rechazar)
        if ($user->isCompany() && optional($user->company)->id === $matching->company_id) {
            return true;
        }

        return false;
    }

    /**
     * Borrar matching.
     */
    public function delete(User $user, Matching $matching): bool
    {
        // Misma lógica que update; ajusta si quieres restringir más.
        return $this->update($user, $matching);
    }

    public function restore(User $user, Matching $matching): bool
    {
        return $this->update($user, $matching);
    }

    public function forceDelete(User $user, Matching $matching): bool
    {
        // Normalmente solo admin (ya cubierto por before()) o nadie:
        return false;
    }
}
