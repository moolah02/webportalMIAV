<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function exportAnalysis(Request $request)
    {
        ini_set('memory_limit', '256M');

        $dateFrom = $request->input('date_from') ?: now()->subDays(30)->toDateString();
        $dateTo   = $request->input('date_to')   ?: now()->toDateString();

        $base = ActivityLog::whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo]);

        $total       = $base->count();
        $byCategory  = collect(array_unique(array_values(self::CATEGORY_MAP)))->sort()->values()
            ->mapWithKeys(function ($cat) use ($dateFrom, $dateTo) {
                $models = array_keys(array_filter(self::CATEGORY_MAP, fn($c) => $c === $cat));
                $count  = ActivityLog::whereIn('model_type', $models)
                    ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])->count();
                return [$cat => $count];
            })->sortByDesc(fn($v) => $v);

        $systemCount = ActivityLog::whereNull('model_type')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])->count();
        $byCategory['System'] = $systemCount;

        $byAction = ActivityLog::whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->selectRaw('action, COUNT(*) as total')
            ->groupBy('action')->orderByDesc('total')->get();

        $topEmployees = ActivityLog::whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->whereNotNull('employee_id')
            ->selectRaw('employee_id, COUNT(*) as total')
            ->groupBy('employee_id')->orderByDesc('total')->limit(10)->get()
            ->map(function ($row) {
                $emp = Employee::find($row->employee_id);
                $row->name = $emp ? $emp->first_name . ' ' . $emp->last_name : 'Unknown';
                return $row;
            });

        $dailyActivity = ActivityLog::whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))->orderBy('date')->get();

        $recentEvents = ActivityLog::with('employee')->latest()->limit(20)->get()
            ->map(function ($log) {
                $log->category = self::CATEGORY_MAP[$log->model_type] ?? ($log->model_type ? 'Other' : 'System');
                return $log;
            });

        $pdf = Pdf::loadView('audit.analysis-pdf', compact(
            'total', 'byCategory', 'byAction', 'topEmployees', 'dailyActivity', 'recentEvents', 'dateFrom', 'dateTo'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('audit-trail-analysis-' . now()->format('Y-m-d') . '.pdf');
    }

    public static function getCategory(?string $modelType): string
    {
        return self::CATEGORY_MAP[$modelType] ?? ($modelType ? 'Other' : 'System');
    }
}
