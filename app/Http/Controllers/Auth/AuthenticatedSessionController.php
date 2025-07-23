<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Update last login time
        Auth::user()->updateLastLogin();

        // Redirect based on role
        return $this->redirectBasedOnRole();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole(): RedirectResponse
    {
        $employee = Auth::user();
        
        if ($employee->hasPermission('all')) {
            return redirect()->intended('/dashboard');
        }
        
        if ($employee->hasPermission('manage_team')) {
            return redirect()->intended('/dashboard');
        }
        
        if ($employee->hasPermission('view_jobs')) {
            return redirect()->intended('/technician/dashboard');
        }
        
        return redirect()->intended('/employee/dashboard');
    }
}
