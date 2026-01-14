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

        // Normalize permissions - split comma-separated params and trim whitespace
        $flat = [];
        foreach ($permissions as $p) {
            foreach (explode(',', $p) as $item) {
                $item = trim($item);
                if ($item !== '') {
                    $flat[] = $item;
                }
            }
        }

        // Check if user has any of the required permissions (custom or Spatie fallback)
        foreach ($flat as $permission) {
            // First check our custom Role->permissions system
            if (method_exists($user, 'hasPermission') && $user->hasPermission($permission)) {
                return $next($request);
            }

            // Fallback to Spatie's permissions if available (wrapped in try-catch)
            if (method_exists($user, 'hasPermissionTo')) {
                try {
                    if ($user->hasPermissionTo($permission)) {
                        return $next($request);
                    }
                } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
                    // Permission doesn't exist in Spatie's system, continue checking
                    continue;
                }
            }
        }

        // If no permissions matched, deny access
        abort(403, 'You do not have permission to access this resource.');
    }
}
