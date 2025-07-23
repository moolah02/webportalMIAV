<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // ─── TEMPORARY: ALLOW ALL ACCESS ─────────────────────────────
        return $next($request);
        // ──────────────────────────────────────────────────────────────

        // ORIGINAL LOGIC (commented out for now)
        // if (! Auth::check()) {
        //     return redirect()->route('login');
        // }
        //
        // $employee = Auth::user();
        // if (! $employee->hasPermission($permission)) {
        //     abort(403, 'Unauthorized. You do not have the required permission: ' . $permission);
        // }
        //
        // return $next($request);
    }
}
