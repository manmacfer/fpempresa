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
            $user->loadMissing(['student:id,user_id','company:id,user_id']);
        }

        return array_merge(parent::share($request), [
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'csrf_token' => csrf_token(),
            'auth' => [
                'user'      => $user,
                'role'      => $user?->role,          // <-- aquí viene el rol
                'studentId' => $user?->student?->id,
                'companyId' => $user?->company?->id,
            ],
        ]);
    }
}
