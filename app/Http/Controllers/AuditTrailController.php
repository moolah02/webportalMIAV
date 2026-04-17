<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    const CATEGORY_MAP = [
        'AssetRequest'     => 'Assets',
        'AssetRequestItem' => 'Assets',
        'Asset'            => 'Assets',
        'Ticket'           => 'Tickets',
        'Employee'         => 'Employees',
        'JobAssignment'    => 'Job Assignments',
        'TechnicianVisit'  => 'Site Visits',
        'Client'           => 'Clients',
        'PosTerminal'      => 'Terminals',
        'BusinessLicense'  => 'Business Licenses',
        'Category'         => 'Settings',
        'Role'             => 'Settings',
    ];

    public function index(Request $request)
    {
        $query = ActivityLog::with('employee')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('category')) {
            if ($request->category === 'System') {
                $query->whereNull('model_type');
            } else {
                $models = array_keys(array_filter(self::CATEGORY_MAP, fn($c) => $c === $request->category));
                $query->whereIn('model_type', $models);
            }
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

        // Derive category for each log
        $logs->getCollection()->transform(function ($log) {
            $log->category = self::CATEGORY_MAP[$log->model_type] ?? ($log->model_type ? 'Other' : 'System');
            return $log;
        });

        // Stats
        $total = ActivityLog::count();
        $today = ActivityLog::whereDate('created_at', today())->count();

        $byCategory = collect(array_unique(array_values(self::CATEGORY_MAP)))->sort()->values()
            ->mapWithKeys(function ($category) {
                $models = array_keys(array_filter(self::CATEGORY_MAP, fn($c) => $c === $category));
                return [$category => ActivityLog::whereIn('model_type', $models)->count()];
            });
        $byCategory['System'] = ActivityLog::whereNull('model_type')->count();

        $stats = compact('total', 'today', 'byCategory');

        $actions    = ActivityLog::distinct()->pluck('action')->filter()->sort()->values();
        $categories = collect(array_unique(array_values(self::CATEGORY_MAP)))->sort()->prepend('System')->values();
        $employees  = Employee::whereIn('id', ActivityLog::distinct()->whereNotNull('employee_id')->pluck('employee_id'))
            ->get(['id', 'first_name', 'last_name']);

        return view('audit.index', compact('logs', 'stats', 'actions', 'categories', 'employees'));
    }

    public static function getCategory(?string $modelType): string
    {
        return self::CATEGORY_MAP[$modelType] ?? ($modelType ? 'Other' : 'System');
    }
}
