<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;


class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user) {
            $user->loadMissing([
                'student:id,user_id,first_name,last_name,avatar_path',
                'company:id,user_id,trade_name,legal_name,logo_path',
                'teacher:id,user_id,full_name',
                'role:id,slug,name',
            ]);
        }

        // Construir el nombre a mostrar
        $displayName = 'Usuario';
        $studentId = null;
        $companyId = null;
        $teacherId = null;
        $roleSlug = null;
        
        if ($user) {
            // Determinar el slug del rol (puede ser un objeto Role o un string directo)
            if (is_object($user->role) && isset($user->role->slug)) {
                $roleSlug = $user->role->slug;
            } elseif (is_string($user->role)) {
                $roleSlug = $user->role;
            } elseif ($user->role_id) {
                // Fallback: mapeo directo por role_id
                $roleMap = [1 => 'admin', 2 => 'teacher', 3 => 'student', 4 => 'company'];
                $roleSlug = $roleMap[$user->role_id] ?? null;
            }
            
            if ($user->student) {
                $displayName = trim(($user->student->first_name ?? '') . ' ' . ($user->student->last_name ?? '')) ?: ($user->name ?? 'Usuario');
                $studentId = $user->student->id;
            } elseif ($user->company) {
                $displayName = $user->company->trade_name ?? $user->company->legal_name ?? ($user->name ?? 'Usuario');
                $companyId = $user->company->id;
            } elseif ($user->teacher) {
                $displayName = $user->teacher->full_name ?? ($user->name ?? 'Usuario');
                $teacherId = $user->teacher->id;
            } else {
                $displayName = $user->name ?? 'Usuario';
            }
        }

        return array_merge(parent::share($request), [
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'csrf_token' => csrf_token(),

            'auth' => [
                'id'        => $user?->id,
                'name'      => $displayName,
                'email'     => $user?->email,
                'user'      => $user ? [
                    'id'    => $user->id,
                    'name'  => $displayName,
                    'email' => $user->email,
                ] : null,
                // numérico + slug + nombre
                'roleId'    => $user?->role_id,
                'roleSlug'  => is_object($user?->role) ? $user->role->slug : $roleSlug,
                'roleName'  => is_object($user?->role) ? $user->role->name : null,
                'role'      => $roleSlug,
                'studentId' => $studentId,
                'companyId' => $companyId,
                'teacherId' => $teacherId,
                // Añadir foto/logo
                'student'   => $user?->student ? [
                    'id'         => $user->student->id,
                    'avatar_path' => $user->student->avatar_path,
                    'avatar_url'  => $user->student->avatar_url,
                ] : null,
                'company'   => $user?->company ? [
                    'id'        => $user->company->id,
                    'logo_path' => $user->company->logo_path,
                ] : null,
            ],
        ]);
    }
}
