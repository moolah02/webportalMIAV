<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// TEMPORARY DEBUG ROUTE - REMOVE AFTER FIXING LOGIN ISSUE
Route::get('/debug-employee-auth', function() {
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        return 'Unauthorized - Admin only';
    }

    $testEmails = [
        'mbonisi.mangena@revival-technologies.com',
        'nicole.chiwomba@revival-technologies.com',
    ];

    $results = [];

    foreach ($testEmails as $email) {
        $emp = Employee::where('email', $email)->first();

        if (!$emp) {
            $results[$email] = 'Employee not found';
            continue;
        }

        $info = [
            'email' => $emp->email,
            'status' => $emp->status,
            'is_active' => $emp->isActive() ? 'YES' : 'NO',
            'role' => $emp->role ? $emp->role->name : 'No role assigned',
            'password_set' => $emp->password ? 'YES' : 'NO',
            'password_length' => strlen($emp->password ?? ''),
            'password_is_bcrypt' => substr($emp->password ?? '', 0, 4) === '$2y$' ? 'YES' : 'NO',
            'password_starts_with' => substr($emp->password ?? '', 0, 10),
        ];

        // Test common passwords
        $testPasswords = ['password', 'Password123', 'Revival@67'];
        foreach ($testPasswords as $testPwd) {
            if (Hash::check($testPwd, $emp->password)) {
                $info['working_password'] = $testPwd;
                break;
            }
        }

        $results[$email] = $info;
    }

    return response()->json([
        'results' => $results,
        'auth_config' => [
            'guard' => config('auth.defaults.guard'),
            'provider' => config('auth.guards.web.provider'),
            'model' => config('auth.providers.employees.model'),
        ]
    ], 200, [], JSON_PRETTY_PRINT);
});
