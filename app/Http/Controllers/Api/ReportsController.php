<?php

// ============================================
// REPORTS API CONTROLLER
// ============================================

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PosTerminal;
use App\Models\JobAssignment;
use App\Models\Ticket;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Get dashboard statistics for reports
     */
    public function dashboardStats(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30));
        $dateTo = $request->get('date_to', now());

        $stats = [
            'terminals' => [
                'total' => PosTerminal::count(),
                'by_status' => PosTerminal::select('status', DB::raw('count(*) as count'))
                                        ->groupBy('status')
                                        ->pluck('count', 'status'),
                'by_region' => PosTerminal::select('region', DB::raw('count(*) as count'))
                                        ->whereNotNull('region')
                                        ->groupBy('region')
                                        ->pluck('count', 'region'),
            ],
            'jobs' => [
                'total_period' => JobAssignment::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                'completed_period' => JobAssignment::where('status', 'completed')
                                                  ->whereBetween('actual_end_time', [$dateFrom, $dateTo])
                                                  ->count(),
                'by_status' => JobAssignment::select('status', DB::raw('count(*) as count'))
                                           ->groupBy('status')
                                           ->pluck('count', 'status'),
                'avg_completion_time' => $this->getAverageCompletionTime($dateFrom, $dateTo),
            ],
            'tickets' => [
                'total_period' => Ticket::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                'resolved_period' => Ticket::where('status', 'resolved')
                                          ->whereBetween('resolved_at', [$dateFrom, $dateTo])
                                          ->count(),
                'by_priority' => Ticket::select('priority', DB::raw('count(*) as count'))
                                      ->groupBy('priority')
                                      ->pluck('count', 'priority'),
                'by_status' => Ticket::select('status', DB::raw('count(*) as count'))
                                    ->groupBy('status')
                                    ->pluck('count', 'status'),
            ],
            'technicians' => [
                'total_active' => Employee::fieldTechnicians()->active()->count(),
                'performance' => $this->getTechnicianPerformance($dateFrom, $dateTo),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ]
            ]
        ]);
    }

    /**
     * Get assignment reports
     */
    public function assignmentReports(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30));
        $dateTo = $request->get('date_to', now());

        $assignments = JobAssignment::with(['technician:id,first_name,last_name', 'client:id,company_name'])
                                   ->whereBetween('created_at', [$dateFrom, $dateTo])
                                   ->get();

        $report = [
            'summary' => [
                'total_assignments' => $assignments->count(),
                'completed' => $assignments->where('status', 'completed')->count(),
                'in_progress' => $assignments->where('status', 'in_progress')->count(),
                'pending' => $assignments->where('status', 'assigned')->count(),
                'overdue' => $assignments->where('scheduled_date', '<', now())
                                       ->whereIn('status', ['assigned', 'in_progress'])
                                       ->count(),
            ],
            'by_technician' => $assignments->groupBy('technician_id')
                                         ->map(function($techJobs, $techId) {
                                             $technician = $techJobs->first()->technician;
                                             return [
                                                 'technician_name' => $technician ? 
                                                     $technician->first_name . ' ' . $technician->last_name : 'Unknown',
                                                 'total_jobs' => $techJobs->count(),
                                                 'completed' => $techJobs->where('status', 'completed')->count(),
                                                 'in_progress' => $techJobs->where('status', 'in_progress')->count(),
                                                 'avg_completion_time' => $this->calculateAvgTime($techJobs->where('status', 'completed')),
                                             ];
                                         }),
            'by_region' => $assignments->groupBy('region')
                                     ->map(function($regionJobs, $region) {
                                         return [
                                             'region' => $region ?: 'Unknown',
                                             'total_jobs' => $regionJobs->count(),
                                             'completed' => $regionJobs->where('status', 'completed')->count(),
                                             'completion_rate' => $regionJobs->count() > 0 ? 
                                                 round(($regionJobs->where('status', 'completed')->count() / $regionJobs->count()) * 100, 1) : 0,
                                         ];
                                     }),
            'assignments' => $assignments->map(function($assignment) {
                return [
                    'id' => $assignment->id,
                    'assignment_id' => $assignment->assignment_id,
                    'technician_name' => $assignment->technician ? 
                        $assignment->technician->first_name . ' ' . $assignment->technician->last_name : 'Unknown',
                    'client_name' => $assignment->client->company_name ?? 'Unknown',
                    'status' => $assignment->status,
                    'scheduled_date' => $assignment->scheduled_date,
                    'actual_start_time' => $assignment->actual_start_time,
                    'actual_end_time' => $assignment->actual_end_time,
                    'duration_hours' => $assignment->actual_start_time && $assignment->actual_end_time ? 
                        $assignment->actual_start_time->diffInHours($assignment->actual_end_time) : null,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'report' => $report,
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ]
            ]
        ]);
    }

    /**
     * Get terminal reports
     */
    public function terminalReports(Request $request)
    {
        $terminals = PosTerminal::with('client:id,company_name')->get();

        $report = [
            'summary' => [
                'total_terminals' => $terminals->count(),
                'active' => $terminals->where('status', 'active')->count(),
                'offline' => $terminals->where('status', 'offline')->count(),
                'faulty' => $terminals->whereIn('status', ['faulty', 'maintenance'])->count(),
                'uptime_percentage' => $terminals->count() > 0 ? 
                    round(($terminals->where('status', 'active')->count() / $terminals->count()) * 100, 1) : 0,
            ],
            'by_region' => $terminals->groupBy('region')
                                   ->map(function($regionTerminals, $region) {
                                       $total = $regionTerminals->count();
                                       $active = $regionTerminals->where('status', 'active')->count();
                                       return [
                                           'region' => $region ?: 'Unknown',
                                           'total' => $total,
                                           'active' => $active,
                                           'offline' => $regionTerminals->where('status', 'offline')->count(),
                                           'faulty' => $regionTerminals->whereIn('status', ['faulty', 'maintenance'])->count(),
                                           'uptime_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
                                       ];
                                   }),
            'by_client' => $terminals->groupBy('client_id')
                                   ->map(function($clientTerminals) {
                                       $client = $clientTerminals->first()->client;
                                       $total = $clientTerminals->count();
                                       $active = $clientTerminals->where('status', 'active')->count();
                                       return [
                                           'client_name' => $client->company_name ?? 'Unknown',
                                           'total' => $total,
                                           'active' => $active,
                                           'uptime_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
                                       ];
                                   }),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'report' => $report
            ]
        ]);
    }

    /**
     * Get technician performance report
     */
    public function technicianPerformance(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30));
        $dateTo = $request->get('date_to', now());

        $performance = $this->getTechnicianPerformance($dateFrom, $dateTo);

        return response()->json([
            'success' => true,
            'data' => [
                'performance' => $performance,
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ]
            ]
        ]);
    }

    /**
     * Get my performance (for technicians)
     */
    public function myPerformance(Request $request)
    {
        $user = $request->user();
        $dateFrom = $request->get('date_from', now()->subDays(30));
        $dateTo = $request->get('date_to', now());

        $myJobs = JobAssignment::where('technician_id', $user->id)
                              ->whereBetween('created_at', [$dateFrom, $dateTo])
                              ->get();

        $performance = [
            'total_jobs' => $myJobs->count(),
            'completed_jobs' => $myJobs->where('status', 'completed')->count(),
            'in_progress_jobs' => $myJobs->where('status', 'in_progress')->count(),
            'completion_rate' => $myJobs->count() > 0 ? 
                round(($myJobs->where('status', 'completed')->count() / $myJobs->count()) * 100, 1) : 0,
            'avg_completion_time' => $this->calculateAvgTime($myJobs->where('status', 'completed')),
            'overdue_jobs' => $myJobs->where('scheduled_date', '<', now())
                                    ->whereIn('status', ['assigned', 'in_progress'])
                                    ->count(),
            'on_time_completion' => $this->calculateOnTimeRate($myJobs),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'performance' => $performance,
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ]
            ]
        ]);
    }

    /**
     * Helper methods
     */
    private function getAverageCompletionTime($dateFrom, $dateTo)
    {
        $avgHours = JobAssignment::where('status', 'completed')
                                ->whereBetween('actual_end_time', [$dateFrom, $dateTo])
                                ->whereNotNull('actual_start_time')
                                ->whereNotNull('actual_end_time')
                                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, actual_start_time, actual_end_time)) as avg_hours')
                                ->value('avg_hours');

        return round($avgHours ?: 0, 1);
    }

    private function getTechnicianPerformance($dateFrom, $dateTo)
    {
        return Employee::fieldTechnicians()
                      ->active()
                      ->with(['jobAssignments' => function($query) use ($dateFrom, $dateTo) {
                          $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                      }])
                      ->get()
                      ->map(function($technician) {
                          $jobs = $technician->jobAssignments;
                          $completedJobs = $jobs->where('status', 'completed');
                          
                          return [
                              'technician_id' => $technician->id,
                              'technician_name' => $technician->first_name . ' ' . $technician->last_name,
                              'total_jobs' => $jobs->count(),
                              'completed_jobs' => $completedJobs->count(),
                              'completion_rate' => $jobs->count() > 0 ? 
                                  round(($completedJobs->count() / $jobs->count()) * 100, 1) : 0,
                              'avg_completion_time' => $this->calculateAvgTime($completedJobs),
                              'on_time_rate' => $this->calculateOnTimeRate($jobs),
                          ];
                      });
    }

    private function calculateAvgTime($jobs)
    {
        $validJobs = $jobs->filter(function($job) {
            return $job->actual_start_time && $job->actual_end_time;
        });

        if ($validJobs->isEmpty()) {
            return 0;
        }

        $totalHours = $validJobs->sum(function($job) {
            return $job->actual_start_time->diffInHours($job->actual_end_time);
        });

        return round($totalHours / $validJobs->count(), 1);
    }

    private function calculateOnTimeRate($jobs)
    {
        $scheduledJobs = $jobs->filter(function($job) {
            return $job->scheduled_date;
        });

        if ($scheduledJobs->isEmpty()) {
            return 100;
        }

        $onTimeJobs = $scheduledJobs->filter(function($job) {
            if ($job->status === 'completed' && $job->actual_end_time) {
                return $job->actual_end_time->toDateString() <= $job->scheduled_date;
            }
            return $job->scheduled_date >= now()->toDateString();
        });

        return round(($onTimeJobs->count() / $scheduledJobs->count()) * 100, 1);
    }
}