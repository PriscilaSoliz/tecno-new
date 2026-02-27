<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PageVisit;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;

class TrackPageVisits
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('get') && !$this->shouldIgnore($request)) {
            try {
                $route = Route::current();
                $pageName = $route ? $route->getName() : null;
                $pagePath = $request->path();
                
                // Generar un título basado en el nombre o path
                $pageTitle = $pageName 
                    ? ucwords(str_replace(['.', '-', '_'], ' ', $pageName)) 
                    : ucwords(str_replace(['/', '-', '_'], ' ', $pagePath));

                $userId = $request->user() ? $request->user()->id : null;

                // Registrar la visita usando el método del modelo
                PageVisit::recordVisit($pageName, $pageTitle, $pagePath, $userId);

            } catch (\Exception $e) {
                // Ignorar errores de registro para no afectar la navegación
                \Log::error('Error tracking page visit: ' . $e->getMessage());
            }
        }

        return $response;
    }

    /**
     * Determinar si la ruta debe ser ignorada
     */
    protected function shouldIgnore(Request $request): bool
    {
        $path = $request->path();
        
        // Ignorar assets y rutas de API/sistema
        if (preg_match('/\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot|map)$/i', $path)) {
            return true;
        }

        $ignoredPrefixes = ['api', 'sanctum', 'livewire', '_ignition', 'build', 'assets'];
        foreach ($ignoredPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
