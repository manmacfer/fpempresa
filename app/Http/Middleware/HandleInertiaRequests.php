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
                'student:id,user_id',
                'company:id,user_id',
                'role:id,slug,name',
            ]);
        }

        return array_merge(parent::share($request), [
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'csrf_token' => csrf_token(),

            'auth' => [
                'user'      => $user,
                'roleId'    => $user?->role_id,        // numérico (1..4)
                'roleSlug'  => $user?->role?->slug,    // 'student' | 'company' | ...
                'roleName'  => $user?->role?->name,    // 'Alumno' | 'Empresa' | ...
                'studentId' => $user?->student?->id,
                'companyId' => $user?->company?->id,
            ],
        ]);
    }
}
