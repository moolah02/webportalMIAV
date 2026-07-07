<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    private array $allowedRoutes = [
        'profile.show',
        'profile.password',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $employee = $request->user();

        if (
            $employee &&
            $employee->must_change_password &&
            !$request->routeIs(...$this->allowedRoutes) &&
            !$request->is('api/*', 'logout')
        ) {
            return redirect()->route('profile.show')
                ->with('warning', 'You must change your password before continuing.');
        }

        return $next($request);
    }
}
