<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PosTerminal;
use App\Models\Client;
use App\Models\Employee;
use App\Models\JobAssignment;
use App\Models\Ticket;
use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\TechnicianVisit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard data for authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Update last login
        $user->updateLastLogin();
        
        // Get role-specific dashboard data
        if ($user->isFieldTechnician()) {
            return $this->getTechnicianDashboard($user);
        } elseif ($user->isManager() || $user->isAdmin()) {
            return $this->getManagerDashboard($user);
        } else {
            return $this->getEmployeeDashboard($user);
        }
    }

    /**
     * Get comprehensive dashboard stats for managers/admins
     */
    private function getManagerDashboard($user)
    {
        // Terminal statistics
        $terminalStats = [
            'total' => PosTerminal::count(),
            'active' => PosTerminal::where('status', 'active')->count(),
            'offline' => PosTerminal::where('status', 'offline')->count(),
            'faulty' => PosTerminal::whereIn('status', ['faulty', 'maintenance'])->count(),
            'need_attention' => PosTerminal::whereIn('status', ['offline', 'faulty', 'maintenance'])->count(),
        ];

        // Client statistics
        $clientStats = [
            'total' => Client::where('status', 'active')->count(),
            'new_this_month' => Client::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
        ];

        // Job assignment statistics
        $jobStats = [
            'pending' => JobAssignment::where('status', 'assigned')->count(),
            'in_progress' => JobAssignment::where('status', 'in_progress')->count(),
            'completed_today' => JobAssignment::where('status', 'completed')
                                             ->whereDate('actual_end_time', today())
                                             ->count(),
            'overdue' => JobAssignment::where('status', 'assigned')
                                     ->where('scheduled_date', '<', now())
                                     ->count(),
        ];

        // Ticket statistics
        $ticketStats = [
            'open' => Ticket::where('status', 'open')->count(),
            'critical' => Ticket::where('priority', 'critical')
                                ->whereIn('status', ['open', 'in_progress'])
                                ->count(),
            'resolved_today' => Ticket::where('status', 'resolved')
                                     ->whereDate('resolved_at', today())
                                     ->count(),
        ];

        // Asset statistics
        $assetStats = [
            'total_assets' => Asset::count(),
            'low_stock' => Asset::whereColumn('stock_quantity', '<=', 'min_stock_level')
                               ->where('stock_quantity', '>', 0)
                               ->count(),
            'assigned' => Asset::where('assigned_quantity', '>', 0)->count(),
            'pending_requests' => AssetRequest::where('status', 'pending')->count(),
        ];

        // Recent activity
        $recentActivity = $this->getRecentActivity();

        // Regional performance
        $regionalData = $this->getRegionalPerformance();

        // Alerts
        $alerts = $this->getSystemAlerts();

        return response()->json([
            'success' => true,
            'data' => [
                'user_type' => 'manager',
                'terminals' => $terminalStats,
                'clients' => $clientStats,
                'jobs' => $jobStats,
                'tickets' => $ticketStats,
                'assets' => $assetStats,
                'recent_activity' => $recentActivity,
                'regional_data' => $regionalData,
                'alerts' => $alerts,
                'quick_stats' => [
                    'network_uptime' => $terminalStats['total'] > 0 ? 
                        round(($terminalStats['active'] / $terminalStats['total']) * 100, 1) : 100,
                    'total_employees' => Employee::where('status', 'active')->count(),
                    'active_technicians' => Employee::fieldTechnicians()->active()->count(),
                ]
            ]
        ]);
    }

    /**
     * Get technician-specific dashboard
     */
    private function getTechnicianDashboard($user)
    {
        // My job assignments
        $myJobs = [
            'assigned' => JobAssignment::where('technician_id', $user->id)
                                      ->where('status', 'assigned')
                                      ->count(),
            'in_progress' => JobAssignment::where('technician_id', $user->id)
                                         ->where('status', 'in_progress')
                                         ->count(),
            'completed_today' => JobAssignment::where('technician_id', $user->id)
                                             ->where('status', 'completed')
                                             ->whereDate('actual_end_time', today())
                                             ->count(),
            'completed_this_week' => JobAssignment::where('technician_id', $user->id)
                                                 ->where('status', 'completed')
                                                 ->whereBetween('actual_end_time', [
                                                     now()->startOfWeek(),
                                                     now()->endOfWeek()
                                                 ])
                                                 ->count(),
        ];

        // Upcoming assignments
        $upcomingJobs = JobAssignment::where('technician_id', $user->id)
                                   ->whereIn('status', ['assigned', 'in_progress'])
                                   ->with(['client:id,company_name', 'region:id,name'])
                                   ->orderBy('scheduled_date')
                                   ->limit(5)
                                   ->get()
                                   ->map(function($job) {
                                       return [
                                           'id' => $job->id,
                                           'assignment_id' => $job->assignment_id,
                                           'client_name' => $job->client->company_name ?? 'Unknown',
                                           'region' => $job->region->name ?? $job->region,
                                           'scheduled_date' => $job->scheduled_date,
                                           'status' => $job->status,
                                           'priority' => $job->priority,
                                           'terminal_count' => count($job->pos_terminals ?? []),
                                           'is_overdue' => $job->scheduled_date < now()->toDateString(),
                                       ];
                                   });

        // My territories (regions where I work)
        $territories = JobAssignment::where('technician_id', $user->id)
                                   ->with('region:id,name')
                                   ->get()
                                   ->pluck('region.name')
                                   ->filter()
                                   ->unique()
                                   ->values();

        // Recent visits/completions
        $recentVisits = collect();
        try {
            if (class_exists('App\Models\TechnicianVisit')) {
                $recentVisits = TechnicianVisit::where('technician_id', $user->id)
                                              ->with(['posTerminal:id,terminal_id,merchant_name', 'client:id,company_name'])
                                              ->latest('visit_date')
                                              ->limit(5)
                                              ->get()
                                              ->map(function($visit) {
                                                  return [
                                                      'id' => $visit->id,
                                                      'terminal_id' => $visit->posTerminal->terminal_id ?? 'Unknown',
                                                      'merchant_name' => $visit->posTerminal->merchant_name ?? 'Unknown',
                                                      'client_name' => $visit->client->company_name ?? 'Unknown',
                                                      'visit_date' => $visit->visit_date,
                                                      'service_type' => $visit->service_type,
                                                      'status' => $visit->status,
                                                  ];
                                              });
            }
        } catch (\Exception $e) {
            \Log::warning('TechnicianVisit model not available: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user_type' => 'technician',
                'my_jobs' => $myJobs,
                'upcoming_assignments' => $upcomingJobs,
                'territories' => $territories,
                'recent_visits' => $recentVisits,
                'performance' => [
                    'jobs_this_month' => JobAssignment::where('technician_id', $user->id)
                                                     ->where('status', 'completed')
                                                     ->whereMonth('actual_end_time', now()->month)
                                                     ->count(),
                    'avg_completion_time' => $this->getAvgCompletionTime($user->id),
                    'customer_rating' => 4.5, // Placeholder - implement rating system
                ]
            ]
        ]);
    }

    /**
     * Get employee-specific dashboard
     */
    private function getEmployeeDashboard($user)
    {
        // Asset requests
        $assetRequests = [
            'total' => AssetRequest::where('employee_id', $user->id)->count(),
            'pending' => AssetRequest::where('employee_id', $user->id)
                                    ->where('status', 'pending')
                                    ->count(),
            'approved' => AssetRequest::where('employee_id', $user->id)
                                     ->where('status', 'approved')
                                     ->count(),
            'recent' => AssetRequest::where('employee_id', $user->id)
                                   ->latest()
                                   ->limit(5)
                                   ->get()
                                   ->map(function($request) {
                                       return [
                                           'id' => $request->id,
                                           'request_number' => $request->request_number,
                                           'status' => $request->status,
                                           'total_items' => $request->items->count(),
                                           'requested_at' => $request->created_at,
                                       ];
                                   })
        ];

        // My assigned assets
        $myAssets = [];
        try {
            if (class_exists('App\Models\AssetAssignment')) {
                $myAssets = $user->currentAssetAssignments()
                                ->with('asset:id,name,category,brand,model')
                                ->get()
                                ->map(function($assignment) {
                                    return [
                                        'id' => $assignment->id,
                                        'asset_name' => $assignment->asset->name,
                                        'category' => $assignment->asset->category,
                                        'brand' => $assignment->asset->brand,
                                        'model' => $assignment->asset->model,
                                        'quantity' => $assignment->quantity_assigned,
                                        'assigned_date' => $assignment->assignment_date,
                                        'expected_return' => $assignment->expected_return_date,
                                        'is_overdue' => $assignment->expected_return_date && 
                                                       $assignment->expected_return_date->isPast(),
                                    ];
                                });
            }
        } catch (\Exception $e) {
            \Log::warning('AssetAssignment not available: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user_type' => 'employee',
                'asset_requests' => $assetRequests,
                'my_assets' => $myAssets,
                'profile' => [
                    'department' => $user->department->name ?? 'Not Assigned',
                    'role' => $user->role->name ?? 'Employee',
                    'hire_date' => $user->hire_date,
                    'last_login' => $user->last_login_at,
                ]
            ]
        ]);
    }

    /**
     * Get system-wide statistics (for API consumers)
     */
    public function getStats(Request $request)
    {
        $stats = [
            'terminals' => [
                'total' => PosTerminal::count(),
                'active' => PosTerminal::where('status', 'active')->count(),
                'offline' => PosTerminal::where('status', 'offline')->count(),
                'faulty' => PosTerminal::whereIn('status', ['faulty', 'maintenance'])->count(),
            ],
            'clients' => [
                'total' => Client::where('status', 'active')->count(),
                'new_this_month' => Client::whereMonth('created_at', now()->month)->count(),
            ],
            'jobs' => [
                'total' => JobAssignment::count(),
                'pending' => JobAssignment::where('status', 'assigned')->count(),
                'completed_today' => JobAssignment::where('status', 'completed')
                                                 ->whereDate('actual_end_time', today())
                                                 ->count(),
            ],
            'tickets' => [
                'open' => Ticket::where('status', 'open')->count(),
                'critical' => Ticket::where('priority', 'critical')->count(),
            ],
            'employees' => [
                'total' => Employee::where('status', 'active')->count(),
                'technicians' => Employee::fieldTechnicians()->active()->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get recent activity for dashboard
     */
    private function getRecentActivity()
    {
        $activities = collect();

        // Recent job completions
        $recentJobs = JobAssignment::where('status', 'completed')
                                  ->with(['technician:id,first_name,last_name', 'client:id,company_name'])
                                  ->latest('actual_end_time')
                                  ->limit(5)
                                  ->get();

        foreach ($recentJobs as $job) {
            $activities->push([
                'type' => 'job_completed',
                'title' => 'Job Completed',
                'description' => "{$job->technician->first_name} {$job->technician->last_name} completed job for {$job->client->company_name}",
                'time' => $job->actual_end_time->diffForHumans(),
                'icon' => '✅',
                'color' => '#4caf50'
            ]);
        }

        // Recent terminals added
        $recentTerminals = PosTerminal::with('client:id,company_name')
                                    ->latest()
                                    ->limit(3)
                                    ->get();

        foreach ($recentTerminals as $terminal) {
            $activities->push([
                'type' => 'terminal_added',
                'title' => 'New Terminal',
                'description' => "Terminal {$terminal->terminal_id} added for {$terminal->client->company_name}",
                'time' => $terminal->created_at->diffForHumans(),
                'icon' => '🖥️',
                'color' => '#2196f3'
            ]);
        }

        // Recent tickets resolved
        $recentTickets = Ticket::where('status', 'resolved')
                              ->latest('resolved_at')
                              ->limit(3)
                              ->get();

        foreach ($recentTickets as $ticket) {
            $activities->push([
                'type' => 'ticket_resolved',
                'title' => 'Ticket Resolved',
                'description' => "Ticket {$ticket->ticket_id} resolved",
                'time' => $ticket->resolved_at->diffForHumans(),
                'icon' => '🎫',
                'color' => '#4caf50'
            ]);
        }

        return $activities->sortByDesc('time')->take(10)->values();
    }

    /**
     * Get regional performance data
     */
    private function getRegionalPerformance()
    {
        $regional = PosTerminal::select('region', 'status', DB::raw('count(*) as count'))
                              ->whereNotNull('region')
                              ->groupBy('region', 'status')
                              ->get()
                              ->groupBy('region')
                              ->map(function($regionTerminals, $region) {
                                  $total = $regionTerminals->sum('count');
                                  $active = $regionTerminals->where('status', 'active')->sum('count');
                                  $issues = $regionTerminals->whereIn('status', ['offline', 'faulty', 'maintenance'])->sum('count');
                                  
                                  return [
                                      'region' => $region,
                                      'total_terminals' => $total,
                                      'active_terminals' => $active,
                                      'issues' => $issues,
                                      'uptime_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
                                  ];
                              })
                              ->values();

        return $regional;
    }

    /**
     * Get system alerts
     */
    private function getSystemAlerts()
    {
        $alerts = collect();

        // Critical tickets
        $criticalTickets = Ticket::where('priority', 'critical')
                                ->whereIn('status', ['open', 'in_progress'])
                                ->count();
        if ($criticalTickets > 0) {
            $alerts->push([
                'type' => 'critical',
                'message' => "{$criticalTickets} critical tickets need immediate attention",
                'icon' => '🚨'
            ]);
        }

        // Faulty terminals
        $faultyTerminals = PosTerminal::where('status', 'faulty')->count();
        if ($faultyTerminals > 0) {
            $alerts->push([
                'type' => 'warning',
                'message' => "{$faultyTerminals} terminals are faulty",
                'icon' => '⚠️'
            ]);
        }

        // Overdue jobs
        $overdueJobs = JobAssignment::where('status', 'assigned')
                                   ->where('scheduled_date', '<', now())
                                   ->count();
        if ($overdueJobs > 0) {
            $alerts->push([
                'type' => 'warning',
                'message' => "{$overdueJobs} jobs are overdue",
                'icon' => '📅'
            ]);
        }

        // Low stock assets
        $lowStockAssets = Asset::whereColumn('stock_quantity', '<=', 'min_stock_level')
                              ->where('stock_quantity', '>', 0)
                              ->count();
        if ($lowStockAssets > 0) {
            $alerts->push([
                'type' => 'info',
                'message' => "{$lowStockAssets} assets are low in stock",
                'icon' => '📦'
            ]);
        }

        return $alerts;
    }

    /**
     * Get average completion time for technician
     */
    private function getAvgCompletionTime($technicianId)
    {
        $avgHours = JobAssignment::where('technician_id', $technicianId)
                                ->where('status', 'completed')
                                ->whereNotNull('actual_start_time')
                                ->whereNotNull('actual_end_time')
                                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, actual_start_time, actual_end_time)) as avg_hours')
                                ->value('avg_hours');

        return round($avgHours ?: 4, 1); // Default to 4 hours if no data
    }
}