<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ReportQueryBuilder;

class ReportBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the query builder as a singleton
        $this->app->singleton(ReportQueryBuilder::class, function ($app) {
            return new ReportQueryBuilder();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only register gates if we're not in package discovery and auth is available
        if (!$this->isInPackageDiscovery() && $this->canDefineGates()) {
            $this->registerGates();
        }
    }

    /**
     * Check if we're currently running package discovery
     */
    private function isInPackageDiscovery(): bool
    {
        return $this->app->runningInConsole() &&
               isset($_SERVER['argv']) &&
               (in_array('package:discover', $_SERVER['argv']) ||
                str_contains(implode(' ', $_SERVER['argv']), 'package:discover'));
    }

    /**
     * Check if we can safely define gates
     */
    private function canDefineGates(): bool
    {
        return $this->app->bound('gate') &&
               $this->app->bound('auth') &&
               class_exists(\Illuminate\Support\Facades\Gate::class);
    }

    /**
     * Register authorization gates
     */
    private function registerGates(): void
    {
        // Use a try-catch to be extra safe
        try {
            \Illuminate\Support\Facades\Gate::define('use-report-builder', function ($user) {
                return $this->hasReportPermission($user, ['user', 'manager']);
            });

            \Illuminate\Support\Facades\Gate::define('preview-reports', function ($user) {
                return $this->hasReportPermission($user, ['user', 'manager']);
            });

            \Illuminate\Support\Facades\Gate::define('export-reports', function ($user) {
                return $this->hasReportPermission($user, ['user', 'manager']);
            });

            \Illuminate\Support\Facades\Gate::define('manage-report-templates', function ($user) {
                return $this->hasReportPermission($user, ['manager']);
            });
        } catch (\Exception $e) {
            // Silently fail during bootstrap if there are issues
            // This prevents breaking the application during package discovery
        }
    }

    /**
     * Check if user has required role for reports
     */
    private function hasReportPermission($user, array $allowedRoles): bool
    {
        if (!$user || !$user->role) {
            return false;
        }

        $userRoleName = strtolower($user->role->name);

        // Map role names to match the spec
        $roleMapping = [
            'technician' => 'user',
            'employee' => 'user',
            'supervisor' => 'manager',
            'admin' => 'manager',
            'super_admin' => 'manager',
            'bypass_all' => 'manager'
        ];

        $mappedRole = $roleMapping[$userRoleName] ?? $userRoleName;

        return in_array($mappedRole, array_map('strtolower', $allowedRoles));
    }
}
