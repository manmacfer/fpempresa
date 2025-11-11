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
        return array_merge(parent::share($request), [
            // Datos de autenticación disponibles en $page.props.auth
            'auth' => [
                // Usuario autenticado (subset para no enviar todo el modelo)
                'user' => fn () => $request->user()
                    ? $request->user()->only('id', 'name', 'email', 'role')
                    : null,

                // ID del Student asociado (para enlazar a la ficha pública)
                'studentId' => fn () => $request->user()?->student?->id,
            ],

            // Mensajes flash (éxito / error)
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],

            // CSRF para formularios "normales"
            'csrf_token' => csrf_token(),
        ]);
    }
}
