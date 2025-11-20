<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware globales (si quieres añadir alguno, usa $middleware->use([...]);)

        // Aseguramos Inertia en el grupo "web" (por si no estaba)
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // Alias personalizados de middleware de ruta (aquí va 'role')
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'validated.student' => \App\Http\Middleware\EnsureStudentIsValidated::class,
            // puedes añadir otros alias aquí...
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
