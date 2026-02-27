<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\MenuItem;
use App\Models\PageVisit;
use Illuminate\Support\Facades\Route;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        // Obtener items del menú
        $menuItems = MenuItem::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($item) {
                $roles = $item->roles;
                if (is_string($roles)) {
                    $roles = json_decode($roles, true) ?? [];
                }
                $item->roles = $roles ?: [];
                
                if ($item->children) {
                    $item->children = $item->children->map(function ($child) {
                        $childRoles = $child->roles;
                        if (is_string($childRoles)) {
                            $childRoles = json_decode($childRoles, true) ?? [];
                        }
                        $child->roles = $childRoles ?: [];
                        return $child;
                    });
                }
                return $item;
            });

        // Calcular visitas para la página actual
        $pageVisits = 0;
        try {
            $route = Route::current();
            $pageName = $route ? $route->getName() : null;
            
            if ($pageName) {
                // Sumar todas las visitas a esta ruta (independientemente del usuario)
                $pageVisits = PageVisit::where('page_name', $pageName)->sum('visit_count');
            } else {
                // Fallback por path si no hay nombre de ruta
                $pageVisits = PageVisit::where('page_path', $request->path())->sum('visit_count');
            }
        } catch (\Exception $e) {
            $pageVisits = 0;
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'profile_photo_url' => $request->user()->profile_photo_url ?? null,
                    'roles' => $request->user()->getRoleNames()->toArray(),
                ] : null,
            ],
            'menuItems' => $menuItems,
            'pageVisits' => (int) $pageVisits,
        ];
    }
}
