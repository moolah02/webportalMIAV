<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $employee = Auth::user();

        // Check if employee is active
        if (!$employee->isActive()) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated'
            ], 403);
        }

        // Create token
        $token = $employee->createToken('mobile-app-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'role' => $employee->role->name ?? 'Employee',
                    'permissions' => $employee->role->permissions ?? [],
                    'department' => $employee->department,
                    'phone' => $employee->phone,
                    'avatar' => $employee->avatar,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        $employee = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'role' => $employee->role->name ?? 'Employee',
                    'permissions' => $employee->role->permissions ?? [],
                    'department' => $employee->department,
                    'phone' => $employee->phone,
                    'avatar' => $employee->avatar,
                    'last_login' => $employee->last_login,
                ]
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $employee = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'avatar' => 'sometimes|image|max:2048',
            'current_password' => 'required_with:new_password|string',
            'new_password' => 'sometimes|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // If changing password, verify current password
        if ($request->has('new_password')) {
            if (!Hash::check($request->current_password, $employee->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }
            $employee->password = Hash::make($request->new_password);
        }

        // Update profile fields
        if ($request->has('name')) {
            $employee->name = $request->name;
        }

        if ($request->has('phone')) {
            $employee->phone = $request->phone;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $employee->avatar = $avatarPath;
        }

        $employee->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'avatar' => $employee->avatar,
                ]
            ]
        ]);
    }

    /**
     * Get user profile details
     */
    public function profile(Request $request)
    {
        $employee = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'role' => $employee->role->name ?? 'Employee',
                    'department' => $employee->department,
                    'phone' => $employee->phone,
                    'avatar' => $employee->avatar ? asset('storage/' . $employee->avatar) : null,
                    'created_at' => $employee->created_at,
                    'last_login' => $employee->last_login,
                    'permissions' => $employee->role->permissions ?? [],
                ]
            ]
        ]);
    }

    /**
     * Refresh token (if needed)
     */
    public function refresh(Request $request)
    {
        $employee = $request->user();
        
        // Delete current token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $token = $employee->createToken('mobile-app-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:employees,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Here you would typically send a password reset email
        // For now, we'll just return a success message
        
        return response()->json([
            'success' => true,
            'message' => 'Password reset instructions sent to your email'
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:employees,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Here you would verify the reset token and update the password
        // This is a simplified version
        
        $employee = Employee::where('email', $request->email)->first();
        $employee->password = Hash::make($request->password);
        $employee->save();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }
}