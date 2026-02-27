<?php

namespace App\Providers;

use App\Services\MenuService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MenuService::class);
    }

    public function boot(): void
    {
        // Forzar HTTPS cuando está detrás de ngrok o proxy
        if (env('APP_ENV') === 'production' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        Inertia::share([
            'menuItems' => fn() => app(MenuService::class)->getMenu(),
            'auth.user.roles' => fn() => auth()->check() 
                ? auth()->user()->roles->pluck('name')->toArray() 
                : [],
        ]);
    }
}
