<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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

        // Role blade directive - checks if user has ANY of the given role
        Blade::if('role', function ($role) {
            if (!Auth::check()) return false;
            return Auth::user()->roles->contains('name', $role);
        });

        // Multiple roles blade directive - checks if user has ANY of the given roles
        Blade::if('anyrole', function (...$roles) {
            if (!Auth::check()) return false;
            return Auth::user()->roles->whereIn('name', $roles)->isNotEmpty();
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

        // Session driver fallback: if using DB sessions but DB or sessions table is unavailable, fall back to file driver.
        if (config('session.driver') === 'database') {
            try {
                // Ensure DB connection works
                DB::connection()->getPdo();

                // If the sessions table is missing, fall back to file driver to avoid losing authentication
                if (! Schema::hasTable(config('session.table', 'sessions'))) {
                    Log::warning('Sessions table missing; falling back to file session driver.');
                    Config::set('session.driver', 'file');
                }
            } catch (\Exception $e) {
                Log::warning('Session DB unavailable: '.$e->getMessage().' — falling back to file session driver.');
                Config::set('session.driver', 'file');
            }
        }
    }
}
