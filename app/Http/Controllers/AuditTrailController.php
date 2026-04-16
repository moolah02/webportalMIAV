<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('employee')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50)->appends($request->query());

        // Stats
        $stats = [
            'total'    => ActivityLog::count(),
            'today'    => ActivityLog::whereDate('created_at', today())->count(),
            'approvals' => ActivityLog::where('action', 'approved')->count(),
            'rejections' => ActivityLog::where('action', 'rejected')->count(),
        ];

        // Distinct actions and model types for filters
        $actions    = ActivityLog::distinct()->pluck('action')->sort()->values();
        $modelTypes = ActivityLog::distinct()->whereNotNull('model_type')->pluck('model_type')->sort()->values();

        return view('audit.index', compact('logs', 'stats', 'actions', 'modelTypes'));
    }
}
