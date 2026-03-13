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
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\TrackPageVisits::class, // Registrar middleware de visitas
        ]);

        // Registrar middlewares de Spatie Permission
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->header('X-Inertia')) {
                // Error de clave foránea (el del screenshot)
                if ($e instanceof \Illuminate\Database\QueryException && str_contains(strtolower($e->getMessage()), 'foreign key violation')) {
                    return back()->with('error', 'No se puede realizar esta operación porque el registro está relacionado con otros datos (ej. pedidos, ventas, etc).');
                }

                // Errores de servidor generales
                return back()->with('error', 'Error del sistema: ' . $e->getMessage());
            }
            return null;
        });
    })->create();
