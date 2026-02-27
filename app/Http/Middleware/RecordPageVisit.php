<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PageVisit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RecordPageVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo registrar para usuarios autenticados
        if (Auth::check()) {
            try {
                $route = Route::current();
                
                if ($route) {
                    $pageName = $route->getName() ?? 'unknown';
                    $pageTitle = ucfirst(str_replace('.', ' - ', $pageName));
                    $pagePath = $request->getPathInfo();

                    PageVisit::recordVisit(
                        $pageName,
                        $pageTitle,
                        $pagePath,
                        Auth::id()
                    );
                }
            } catch (\Exception $e) {
                // Silenciosamente ignorar errores de registro
                \Log::error('Error recording page visit: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
