<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PosTerminal;
use App\Models\Employee;
use App\Models\AssetRequest;
use App\Models\JobAssignment;
use App\Models\TechnicianVisit;
use App\Models\Ticket;
use App\Models\Category;
// use App\Models\BusinessLicense; // Disabled - model not available
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Update user's last login when they access dashboard
        auth()->user()->updateLastLogin();

        // Get comprehensive dashboard statistics (enhanced with licenses)
        $stats = $this->getDashboardStats();

        return view('dashboard', compact('stats'));
    }

    public function technician()
    {
        $employee = auth()->user();

        // Technician-specific stats from real data
        $recentVisits = collect([]);
        if (class_exists(\App\Models\TechnicianVisit::class)) {
            try {
                $recentVisits = TechnicianVisit::where('technician_id', $employee->id)
                    ->with(['posTerminal', 'client'])
                    ->latest('visit_date')
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                // Table doesn't exist, use empty collection
            }
        }

        $stats = [
            'assigned_jobs' => 0,
            'completed_today' => 0,
            'pending_jobs' => 0,
            'recent_visits' => $recentVisits,
            'territories' => $this->getTechnicianTerritories($employee->id),
        ];

        // Try to get job stats, but don't crash if columns don't exist
        try {
            $stats['assigned_jobs'] = JobAssignment::where('technician_id', $employee->id)
                ->where('status', 'assigned')
                ->count();
            $stats['completed_today'] = JobAssignment::where('technician_id', $employee->id)
                ->where('status', 'completed')
                ->whereDate('actual_end_time', today())
                ->count();
            $stats['pending_jobs'] = JobAssignment::where('technician_id', $employee->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count();
        } catch (\Exception $e) {
            // Columns don't exist, use default values
        }

        return view('technician.dashboard', compact('stats'));
    }

    private function getDashboardStats()
    {
        // Basic counts from real data
        $totalTerminals = PosTerminal::count();
        $totalClients = Client::where('status', 'active')->count();

        // Terminal status breakdown using real data
        $terminalStats = PosTerminal::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $activeTerminals = $terminalStats['active'] ?? 0;
        $offlineTerminals = $terminalStats['offline'] ?? 0;
        $maintenanceTerminals = $terminalStats['maintenance'] ?? 0;
        $faultyTerminals = $terminalStats['faulty'] ?? 0;
        $decommissionedTerminals = $terminalStats['decommissioned'] ?? 0;

        // Calculate metrics
        $needAttention = $offlineTerminals + $faultyTerminals + $maintenanceTerminals;
        $urgentIssues = $faultyTerminals;

        // Real monthly growth
        $newTerminalsThisMonth = PosTerminal::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $newClientsThisMonth = Client::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Real regional distribution
        $regionalData = $this->getRegionalData();

        // Recent activity from real data (enhanced with licenses)
        $recentActivity = $this->getRecentActivity();

        // Top clients by terminal count (real data)
        $topClients = $this->getTopClients();

        // System health metrics
        $networkUptime = $totalTerminals > 0 ? round(($activeTerminals / $totalTerminals) * 100, 1) : 100;

        // Service metrics from real data
        $avgResponseTime = $this->getAverageResponseTime();
        $serviceLevel = $this->getServiceLevel();

        // Real alerts (enhanced with license alerts)
        $alerts = $this->getSystemAlerts();

        // Monthly trends (enhanced with licenses)
        $monthlyTrends = $this->getMonthlyTrends();

        // Client contract status
        $contractStats = $this->getContractStats();

        // Employee/Technician stats
        $employeeStats = $this->getEmployeeStats();

        // NEW: License statistics
        $licenseStats = $this->getLicenseStats();

        // NEW: Upcoming license renewals
        $upcomingRenewals = $this->getUpcomingRenewals();

        return [
            // Main metrics
            'total_terminals' => $totalTerminals,
            'active_terminals' => $activeTerminals,
            'offline_terminals' => $offlineTerminals,
            'maintenance_terminals' => $maintenanceTerminals,
            'faulty_terminals' => $faultyTerminals,
            'decommissioned_terminals' => $decommissionedTerminals,
            'need_attention' => $needAttention,
            'urgent_issues' => $urgentIssues,
            'total_clients' => $totalClients,

            // Growth metrics
            'new_terminals_this_month' => $newTerminalsThisMonth,
            'new_clients_this_month' => $newClientsThisMonth,

            // Regional data
            'regional_data' => $regionalData,

            // Activity and clients
            'recent_activity' => $recentActivity,
            'top_clients' => $topClients,

            // System health
            'network_uptime' => $networkUptime,
            'service_level' => $serviceLevel,
            'avg_response_time' => $avgResponseTime,

            // Alerts
            'alerts' => $alerts,

            // Trends and analytics
            'monthly_trends' => $monthlyTrends,
            'contract_stats' => $contractStats,
            'employee_stats' => $employeeStats,

            // NEW: License data
            'license_stats' => $licenseStats,
            'upcoming_renewals' => $upcomingRenewals,
        ];
    }

    // NEW: License Statistics Method
    private function getLicenseStats()
    {
        // Check if BusinessLicense model exists
        if (!class_exists(\App\Models\BusinessLicense::class)) {
            return [
                'total_licenses' => 0,
                'active_licenses' => 0,
                'expired' => 0,
                'expiring_soon' => 0,
                'critical_licenses' => 0,
                'critical_expired' => 0,
                'suspended' => 0,
                'cancelled' => 0,
                'compliance_rate' => 100,
                'annual_cost' => 0,
            ];
        }

        $totalLicenses = \App\Models\BusinessLicense::count();
        $activeLicenses = \App\Models\BusinessLicense::where('status', 'active')->count();
        $expiredLicenses = \App\Models\BusinessLicense::expired()->count();
        $expiringSoon = \App\Models\BusinessLicense::expiringSoon(15)->count();
        $criticalLicenses = \App\Models\BusinessLicense::where('priority_level', 'critical')->count();
        $criticalExpired = \App\Models\BusinessLicense::where('priority_level', 'critical')
            ->expired()->count();
        $suspendedLicenses = \App\Models\BusinessLicense::where('status', 'suspended')->count();
        $cancelledLicenses = \App\Models\BusinessLicense::where('status', 'cancelled')->count();

        // Calculate compliance rate
        $compliantLicenses = \App\Models\BusinessLicense::where('status', 'active')
            ->where('expiry_date', '>', now()->addDays(30))
            ->count();
        $complianceRate = $totalLicenses > 0 ? round(($compliantLicenses / $totalLicenses) * 100) : 100;

        // Calculate annual cost
        $annualCost = \App\Models\BusinessLicense::where('status', 'active')
            ->sum('renewal_cost') ?: \App\Models\BusinessLicense::where('status', 'active')->sum('cost');

        return [
            'total_licenses' => $totalLicenses,
            'active_licenses' => $activeLicenses,
            'expired' => $expiredLicenses,
            'expiring_soon' => $expiringSoon,
            'critical_licenses' => $criticalLicenses,
            'critical_expired' => $criticalExpired,
            'suspended' => $suspendedLicenses,
            'cancelled' => $cancelledLicenses,
            'compliance_rate' => $complianceRate,
            'annual_cost' => $annualCost,
        ];
    }

    // NEW: Upcoming License Renewals Method
    private function getUpcomingRenewals()
    {
        // Check if BusinessLicense model exists
        if (!class_exists(\App\Models\BusinessLicense::class)) {
            return collect([]);
        }

        return \App\Models\BusinessLicense::with(['department', 'responsibleEmployee'])
            ->where(function($query) {
                $query->expiringSoon(60) // Next 60 days
                      ->orWhere('status', 'expired');
            })
            ->orderBy('expiry_date', 'asc')
            ->limit(10)
            ->get();
    }

    private function getRegionalData()
    {
        // Get real regional distribution
        $regionalData = PosTerminal::select('region', 'status', DB::raw('count(*) as count'))
            ->whereNotNull('region')
            ->groupBy('region', 'status')
            ->get()
            ->groupBy('region')
            ->map(function ($regionTerminals, $region) {
                $total = $regionTerminals->sum('count');
                $active = $regionTerminals->where('status', 'active')->sum('count');
                $issues = $regionTerminals->whereIn('status', ['offline', 'faulty', 'maintenance'])->sum('count');

                return [
                    'total' => $total,
                    'active' => $active,
                    'issues' => $issues,
                    'uptime_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
                ];
            });

        // Add regions with no terminals
        $allRegions = ['North Region', 'South Region', 'East Region', 'West Region', 'Central Region', 'HATFIELD', 'EPWORTH', 'CBD', 'MT PLEASANT'];

        foreach ($allRegions as $region) {
            if (!$regionalData->has($region)) {
                $regionalData[$region] = [
                    'total' => 0,
                    'active' => 0,
                    'issues' => 0,
                    'uptime_percentage' => 0,
                ];
            }
        }

        return $regionalData->take(8); // Limit to 8 regions for display
    }

    private function getRecentActivity()
    {
        $activities = collect();

        // Recent terminals added (last 10)
        $recentTerminals = PosTerminal::with('client')
            ->latest()
            ->limit(5)
            ->get();

        foreach ($recentTerminals as $terminal) {
            $activities->push([
                'icon' => '🖥️',
                'color' => '#4caf50',
                'title' => 'New Terminal Added',
                'description' => "Terminal {$terminal->terminal_id} added for {$terminal->client->company_name}",
                'time' => $terminal->created_at->diffForHumans(),
                'action' => [
                    'url' => route('pos-terminals.show', $terminal),
                    'label' => 'View'
                ]
            ]);
        }

        // Recent clients added
        $recentClients = Client::latest()->limit(3)->get();

        foreach ($recentClients as $client) {
            $activities->push([
                'icon' => '🏢',
                'color' => '#2196f3',
                'title' => 'New Client Registered',
                'description' => "{$client->company_name} has been added to the system",
                'time' => $client->created_at->diffForHumans(),
                'action' => [
                    'url' => route('clients.show', $client),
                    'label' => 'View'
                ]
            ]);
        }

        // NEW: Recent license activities
        if (class_exists(\App\Models\BusinessLicense::class)) {
            $recentLicenses = \App\Models\BusinessLicense::with(['creator', 'department'])
                ->latest()
                ->limit(3)
                ->get();

            foreach ($recentLicenses as $license) {
                $activities->push([
                    'icon' => '📋',
                    'color' => '#2196f3',
                    'title' => 'License Added',
                    'description' => "New {$license->license_type_name}: {$license->license_name}",
                    'time' => $license->created_at->diffForHumans(),
                    'action' => [
                        'url' => route('business-licenses.show', $license),
                        'label' => 'View'
                    ]
                ]);
            }

            // NEW: Recent renewals
            $recentRenewals = \App\Models\BusinessLicense::whereNotNull('renewal_date')
                ->where('renewal_date', '>=', now()->subDays(30))
                ->with(['department'])
                ->latest('renewal_date')
                ->limit(2)
                ->get();

            foreach ($recentRenewals as $license) {
                $activities->push([
                    'icon' => '🔄',
                    'color' => '#4caf50',
                    'title' => 'License Renewed',
                    'description' => "Renewed: {$license->license_name} until {$license->expiry_date->format('M Y')}",
                    'time' => $license->renewal_date->diffForHumans(),
                    'action' => [
                        'url' => route('business-licenses.show', $license),
                        'label' => 'View'
                    ]
                ]);
            }

            // NEW: Expired licenses today
            $expiredToday = \App\Models\BusinessLicense::whereDate('expiry_date', today())
                ->get();

            foreach ($expiredToday as $license) {
                $activities->push([
                    'icon' => '⚠️',
                    'color' => '#f44336',
                    'title' => 'License Expired',
                    'description' => "EXPIRED: {$license->license_name}",
                    'time' => 'Today',
                    'action' => [
                        'url' => route('business-licenses.renew', $license),
                        'label' => 'Renew'
                    ]
                ]);
            }
        }

        // Recent job assignments
        try {
            $recentJobs = JobAssignment::with('technician')
                ->latest()
                ->limit(3)
                ->get();

            foreach ($recentJobs as $job) {
                $activities->push([
                    'icon' => '📋',
                    'color' => '#ff9800',
                    'title' => 'Job Assignment Created',
                    'description' => "Assignment {$job->assignment_id} created for {$job->technician->name}",
                    'time' => $job->created_at->diffForHumans(),
                ]);
            }
        } catch (\Exception $e) {
            // Columns don't exist or relationships broken, skip job assignments
        }

        // Recent technician visits
        if (class_exists(\App\Models\TechnicianVisit::class)) {
            try {
                $recentVisits = TechnicianVisit::with(['technician', 'posTerminal'])
                    ->latest('visit_date')
                    ->limit(2)
                    ->get();

                foreach ($recentVisits as $visit) {
                    $activities->push([
                        'icon' => '🔧',
                        'color' => '#9c27b0',
                        'title' => 'Technician Visit Completed',
                        'description' => "{$visit->technician->name} visited terminal {$visit->posTerminal->terminal_id}",
                        'time' => $visit->visit_date->diffForHumans(),
                    ]);
                }
            } catch (\Exception $e) {
                // Table doesn't exist, skip technician visits
            }
        }

        return $activities->sortByDesc('time')->take(15); // Increased to 15 to show more activities
    }

    private function getTopClients()
    {
        return Client::withCount('posTerminals')
            ->orderBy('pos_terminals_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->company_name,
                    'terminals' => $client->pos_terminals_count,
                    'status' => $client->status,
                    'contract_active' => $client->is_contract_active,
                ];
            });
    }

    private function getSystemAlerts()
    {
        $alerts = collect();

        // NEW: License alerts (highest priority)
        if (class_exists(\App\Models\BusinessLicense::class)) {
            $expiredLicenses = \App\Models\BusinessLicense::expired()->count();
            if ($expiredLicenses > 0) {
                $alerts->push([
                    'type' => 'critical',
                    'icon' => '⚠️',
                    'message' => "{$expiredLicenses} business licenses expired - immediate action required!"
                ]);
            }

            $expiringSoon = \App\Models\BusinessLicense::expiringSoon(15)->count();
            if ($expiringSoon > 0) {
                $alerts->push([
                    'type' => 'warning',
                    'icon' => '⏰',
                    'message' => "{$expiringSoon} business licenses expiring within 30 days"
                ]);
            }

            $criticalExpired = \App\Models\BusinessLicense::where('priority_level', 'critical')
                ->expired()->count();
            if ($criticalExpired > 0) {
                $alerts->push([
                    'type' => 'critical',
                    'icon' => '🚨',
                    'message' => "{$criticalExpired} critical business licenses expired - high business impact!"
                ]);
            }

            // High-cost renewals
            $highCostRenewals = \App\Models\BusinessLicense::expiringSoon(15)
                ->where('renewal_cost', '>', 5000)
                ->count();

            if ($highCostRenewals > 0) {
                $alerts->push([
                    'type' => 'info',
                    'icon' => '💰',
                    'message' => "{$highCostRenewals} high-cost license renewals (\$5K+) coming up"
                ]);
            }

            // Compliance rate alert
            $totalLicenses = \App\Models\BusinessLicense::count();
            if ($totalLicenses > 0) {
                $compliantLicenses = \App\Models\BusinessLicense::where('status', 'active')
                    ->where('expiry_date', '>', now()->addDays(30))
                    ->count();
                $complianceRate = round(($compliantLicenses / $totalLicenses) * 100);

                if ($complianceRate < 80) {
                    $alerts->push([
                        'type' => 'warning',
                        'icon' => '📋',
                        'message' => "License compliance rate below 80% ({$complianceRate}%) - review needed"
                    ]);
                }
            }
        }

        // Critical: Faulty terminals
        $faultyCount = PosTerminal::where('status', 'faulty')->count();
        if ($faultyCount > 0) {
            $alerts->push([
                'type' => 'critical',
                'icon' => '⚠️',
                'message' => "{$faultyCount} terminals are faulty and need immediate attention"
            ]);
        }

        // High: Offline terminals
        $offlineCount = PosTerminal::where('status', 'offline')->count();
        if ($offlineCount > 5) {
            $alerts->push([
                'type' => 'warning',
                'icon' => '📶',
                'message' => "{$offlineCount} terminals are offline"
            ]);
        }

        // Contracts expiring soon
        try {
            $expiringContracts = Client::where('contract_end_date', '<=', now()->addDays(30))
                ->where('contract_end_date', '>', now())
                ->count();

            if ($expiringContracts > 0) {
                $alerts->push([
                    'type' => 'warning',
                    'icon' => '📄',
                    'message' => "{$expiringContracts} client contracts expiring within 30 days"
                ]);
            }
        } catch (\Exception $e) {
            // contract_end_date column doesn't exist, skip this alert
        }

        // Pending asset requests
        $pendingRequests = AssetRequest::where('status', 'pending')->count();
        if ($pendingRequests > 10) {
            $alerts->push([
                'type' => 'info',
                'icon' => '📦',
                'message' => "{$pendingRequests} asset requests pending approval"
            ]);
        }

        // Unassigned job assignments
        try {
            $unassignedJobs = JobAssignment::where('status', 'assigned')
                ->where('scheduled_date', '<', now())
                ->count();

            if ($unassignedJobs > 0) {
                $alerts->push([
                    'type' => 'warning',
                    'icon' => '📋',
                    'message' => "{$unassignedJobs} overdue job assignments"
                ]);
            }
        } catch (\Exception $e) {
            // Columns don't exist, skip this alert
        }

        return $alerts;
    }

    private function getMonthlyTrends()
    {
        $months = [];
        $terminals = [];
        $clients = [];
        $licenses = []; // NEW: License trends

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            // Terminals added each month
            $terminals[] = PosTerminal::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            // Clients added each month
            $clients[] = Client::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            // NEW: Licenses added each month
            $licenses[] = class_exists(\App\Models\BusinessLicense::class)
                ? \App\Models\BusinessLicense::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
                : 0;
        }

        return [
            'months' => $months,
            'terminals' => $terminals,
            'clients' => $clients,
            'licenses' => $licenses, // NEW
        ];
    }

    private function getContractStats()
    {
        try {
            $total = Client::whereNotNull('contract_start_date')->count();
            $active = Client::where('status', 'active')
                ->whereNotNull('contract_start_date')
                ->whereNotNull('contract_end_date')
                ->where('contract_start_date', '<=', now())
                ->where('contract_end_date', '>=', now())
                ->count();
            $expired = Client::where('contract_end_date', '<', now())->count();
            $expiringSoon = Client::whereBetween('contract_end_date', [now(), now()->addDays(30)])->count();

            return [
                'total' => $total,
                'active' => $active,
                'expired' => $expired,
                'expiring_soon' => $expiringSoon,
            ];
        } catch (\Exception $e) {
            // contract columns don't exist, return defaults
            return [
                'total' => 0,
                'active' => 0,
                'expired' => 0,
                'expiring_soon' => 0,
            ];
        }
    }

    private function getEmployeeStats()
    {
        $totalEmployees = Employee::where('status', 'active')->count();
        $technicians = Employee::fieldTechnicians()->active()->count();
        $managers = Employee::managers()->active()->count();

        return [
            'total' => $totalEmployees,
            'technicians' => $technicians,
            'managers' => $managers,
        ];
    }

    private function getAverageResponseTime()
    {
        // Calculate from job assignments (hours between creation and start)
        try {
            $avgHours = JobAssignment::whereNotNull('actual_start_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, actual_start_time)) as avg_hours')
                ->value('avg_hours');
            return round($avgHours ?: 24, 1);
        } catch (\Exception $e) {
            return 24; // Default to 24 hours if column doesn't exist
        }
    }

    private function getServiceLevel()
    {
        // Calculate service level based on completed vs assigned jobs
        try {
            $totalJobs = JobAssignment::count();
            $completedJobs = JobAssignment::where('status', 'completed')->count();
            return $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 1) : 100;
        } catch (\Exception $e) {
            return 100; // Default to 100% if column doesn't exist
        }
    }

    private function getTechnicianTerritories($technicianId)
    {
        // Get regions where this technician has worked
        try {
            return JobAssignment::where('technician_id', $technicianId)
                ->join('regions', 'job_assignments.region_id', '=', 'regions.id')
                ->pluck('regions.name')
                ->unique()
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            return []; // Return empty array if columns don't exist
        }
    }

    private function getEmployeeRecentActivity($employeeId)
    {
        $activities = collect();

        // Recent asset requests
        $recentRequests = AssetRequest::where('employee_id', $employeeId)
            ->latest()
            ->limit(5)
            ->get();

        foreach ($recentRequests as $request) {
            $activities->push([
                'type' => 'asset_request',
                'title' => "Asset Request {$request->request_number}",
                'status' => $request->status,
                'date' => $request->created_at,
            ]);
        }

        return $activities->sortByDesc('date')->take(5);
    }
    public function employee()
{
    $me = auth()->user();

    // --- Job assignment metrics for this employee ---
    $jobStats = [
        'assigned' => 0,
        'in_progress' => 0,
        'completed' => 0,
        'today' => 0,
    ];

    $upcomingAssignments = collect([]);

    try {
        $jobStats = [
            'assigned'     => JobAssignment::where('technician_id', $me->id)->where('status', 'assigned')->count(),
            'in_progress'  => JobAssignment::where('technician_id', $me->id)->where('status', 'in_progress')->count(),
            'completed'    => JobAssignment::where('technician_id', $me->id)->where('status', 'completed')->count(),
            'today'        => JobAssignment::where('technician_id', $me->id)
                                    ->whereDate('scheduled_date', today())
                                    ->whereIn('status', ['assigned','in_progress'])
                                    ->count(),
        ];

        // Upcoming assignments (next few), lightweight eager-loads
        $upcomingAssignments = JobAssignment::with([
                'client:id,company_name',
                'project:id,project_name',
                'region:id,name',
            ])
            ->where('technician_id', $me->id)
            ->whereIn('status', ['assigned','in_progress'])
            ->orderBy('scheduled_date', 'asc')
            ->limit(6)
            ->get()
            ->map(function ($a) {
                // Handy display helpers without touching the model:
                $a->terminal_count = is_array($a->pos_terminals) ? count($a->pos_terminals) : 0;
                $a->list_title = implode(' • ', array_filter([
                    optional($a->project)->project_name,
                    optional($a->client)->company_name,
                    optional($a->region)->name,
                ]));
                return $a;
            });
    } catch (\Exception $e) {
        // Columns don't exist, use default values
    }

    // You already had these — keeping them intact
    $stats = [
        'my_requests'       => AssetRequest::where('employee_id', $me->id)->count(),
        'pending_approvals' => AssetRequest::where('employee_id', $me->id)->where('status', 'pending')->count(),
        'recent_activity'   => $this->getEmployeeRecentActivity($me->id),
        'jobs'              => $jobStats, // <= add the job block into your existing stats bag
    ];

    return view('employee.dashboard', [
        'stats' => $stats,
        'upcomingAssignments' => $upcomingAssignments,
        'me' => $me,
    ]);
}
}
