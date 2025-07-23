<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $employee = Auth::user();
        $employeeRole = $employee->role->name ?? '';

        if (!in_array($employeeRole, $roles)) {
            abort(403, 'Unauthorized. Required roles: ' . implode(', ', $roles));
        }

        return $next($request);
    }
}