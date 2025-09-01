<?php

namespace App\Http\Controllers;

use App\Models\ReportTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class ReportTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $isManager = $user->can('manage-report-templates');

            $query = ReportTemplate::with('creator:id,first_name,last_name');

            // Managers can see all templates, users can see global + their own
            if (!$isManager) {
                $query->where(function($q) use ($user) {
                    $q->where('is_global', true)
                      ->orWhere('created_by', $user->id);
                });
            }

            $templates = $query->orderBy('is_global', 'desc')
                              ->orderBy('name')
                              ->get();

            return response()->json([
                'success' => true,
                'data' => $templates
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_global' => 'boolean',
                'payload' => 'required|array'
            ]);

            // Check permissions for global templates
            if (($validated['is_global'] ?? false) && !$user->can('manage-report-templates')) {
                return response()->json([
                    'error' => 'Insufficient permissions to create global templates'
                ], 403);
            }

            $template = ReportTemplate::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_global' => $validated['is_global'] ?? false,
                'payload' => $validated['payload'],
                'created_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'data' => $template,
                'message' => 'Template created successfully'
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $template = ReportTemplate::with('creator:id,first_name,last_name')->findOrFail($id);

            // Check if user can access this template
            if (!$template->is_global && $template->created_by !== $user->id && !$user->can('manage-report-templates')) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $template
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Template not found'
            ], 404);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $template = ReportTemplate::findOrFail($id);

            // Check if user can edit this template
            if ($template->created_by !== $user->id && !$user->can('manage-report-templates')) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_global' => 'boolean',
                'payload' => 'required|array'
            ]);

            $template->update($validated);

            return response()->json([
                'success' => true,
                'data' => $template,
                'message' => 'Template updated successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $template = ReportTemplate::findOrFail($id);

            // Check if user can delete this template
            if ($template->created_by !== $user->id && !$user->can('manage-report-templates')) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Template not found'
            ], 404);
        }
    }
}
