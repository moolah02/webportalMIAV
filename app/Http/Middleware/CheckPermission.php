<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Add this import

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = Auth::user(); // Changed from auth()->user()

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user is active
        if (!$user->isActive()) {
            Auth::logout(); // Changed from auth()->logout()
            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        // If no specific permissions required, just continue
        if (empty($permissions)) {
            return $next($request);
        }

        // Check if user has any of the required permissions
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        // If no permissions matched, deny access
        abort(403, 'You do not have permission to access this resource.');
    }
}
