<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS for ngrok and secure environments
        if (request()->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            URL::forceScheme('https');
        }

        // Register middleware aliases for Laravel 11
        Route::aliasMiddleware('permission', \App\Http\Middleware\CheckPermission::class);
        Route::aliasMiddleware('role', \App\Http\Middleware\CheckRole::class);
        Route::aliasMiddleware('active.employee', \App\Http\Middleware\EnsureEmployeeIsActive::class);

        // Permission blade directive
        Blade::if('permission', function ($permission) {
            return Auth::check() && Auth::user()->hasPermission($permission);
        });

        // Role blade directive
        Blade::if('role', function ($role) {
            return Auth::check() && Auth::user()->role && Auth::user()->role->name === $role;
        });

        // Multiple roles blade directive
        Blade::if('anyrole', function (...$roles) {
            if (!Auth::check() || !Auth::user()->role) {
                return false;
            }
            return in_array(Auth::user()->role->name, $roles);
        });

        // Module access blade directive
        Blade::if('module', function ($module) {
            return Auth::check() && Auth::user()->canAccessModule($module);
        });

        // Admin check blade directive
        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->hasPermission('all');
        });

        // Manager or higher blade directive
        Blade::if('manager', function () {
            return Auth::check() && (
                Auth::user()->hasPermission('all') ||
                Auth::user()->hasPermission('manage_team')
            );
        });

        // Check if user can approve requests
        Blade::if('canApprove', function () {
            return Auth::check() && Auth::user()->canApprove();
        });

        // Check if user is active
        Blade::if('activeEmployee', function () {
            return Auth::check() && Auth::user()->isActive();
        });
    }
}
