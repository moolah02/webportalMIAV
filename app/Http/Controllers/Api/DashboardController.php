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
use App\Models\Visit;

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

        return $this->getTechnicianDashboard($user);
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

    // --- Recent visits/completions (from visits + visit_terminals) ---
    $recentVisitRows = Visit::where('employee_id', $user->id)
        ->with('visitTerminals')                // relation on Visit model
        ->orderByDesc('completed_at')
        ->limit(5)
        ->get();

    // Collect all referenced POS terminal PKs to enrich with merchant/client info
    $allPtIds = $recentVisitRows->flatMap(fn($v) => $v->visitTerminals->pluck('terminal_id'))
        ->filter()
        ->unique()
        ->values();

    $posTerminalsById = collect();
    $clientsById = collect();

    if ($allPtIds->isNotEmpty()) {
        $posTerminals = PosTerminal::query()
            ->select(['id','terminal_id','merchant_name','client_id'])
            ->whereIn('id', $allPtIds)
            ->get();

        $posTerminalsById = $posTerminals->keyBy('id');

        $clientIds = $posTerminals->pluck('client_id')->filter()->unique()->values();
        if ($clientIds->isNotEmpty()) {
            $clientsById = Client::query()
                ->select(['id','company_name'])
                ->whereIn('id', $clientIds)
                ->get()
                ->keyBy('id');
        }
    }

    $recentVisits = $recentVisitRows->map(function ($visit) use ($posTerminalsById, $clientsById) {
        // Build terminal entries (merge visit_terminal row with POS terminal meta)
        $terminals = $visit->visitTerminals->map(function ($vt) use ($posTerminalsById) {
            $pt = $posTerminalsById->get($vt->terminal_id);
            return [
                'visit_terminal_id' => $vt->id,
                'terminal_pk'       => $vt->terminal_id,                 // FK to pos_terminals.id
                'terminal_id'       => $pt->terminal_id ?? null,         // human-facing TID
                'merchant_name'     => $pt->merchant_name ?? null,
                'status'            => $vt->status,
                'condition'         => $vt->condition,
                'serial_number'     => $vt->serial_number,
                'device_type'       => $vt->device_type,
                'comments'          => $vt->comments,
            ];
        })->values();

        // Try to derive a client name from any included POS terminal
        $clientName = null;
        foreach ($visit->visitTerminals as $vt) {
            $pt = $posTerminalsById->get($vt->terminal_id);
            if ($pt && $pt->client_id && isset($clientsById[$pt->client_id])) {
                $clientName = $clientsById[$pt->client_id]->company_name;
                break;
            }
        }

        return [
            'id'              => $visit->id,
            'assignment_id'   => $visit->assignment_id,
            'merchant_name'   => $visit->merchant_name, // header field from visits
            'client_name'     => $clientName ?? 'Unknown',
            'completed_at'    => optional($visit->completed_at)->toIso8601String(),
            'total_terminals' => $terminals->count(),
            'terminals'       => $terminals,
        ];
    });

    return response()->json([
        'success' => true,
        'data' => [
            'user_type' => 'technician',
            'my_jobs' => $myJobs,
            'upcoming_assignments' => $upcomingJobs,
            'territories' => JobAssignment::where('technician_id', $user->id)
                                          ->with('region:id,name')
                                          ->get()
                                          ->pluck('region.name')
                                          ->filter()
                                          ->unique()
                                          ->values(),
            'recent_visits' => $recentVisits,
            'performance' => [
                'jobs_this_month' => JobAssignment::where('technician_id', $user->id)
                                                 ->where('status', 'completed')
                                                 ->whereMonth('actual_end_time', now()->month)
                                                 ->count(),
                'avg_completion_time' => $this->getAvgCompletionTime($user->id),
                'customer_rating' => 4.5, // TODO: implement ratings
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
