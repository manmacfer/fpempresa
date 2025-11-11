<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        if ($user) {
            // Evita N+1 al compartir ids
            $user->loadMissing([
                'student:id,user_id',
                'company:id,user_id',
            ]);
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user'      => $user,
                'role'      => $user?->role,
                'studentId' => $user?->student?->id,
                'companyId' => $user?->company?->id,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'csrf_token' => csrf_token(),
        ]);
    }
}
