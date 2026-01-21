<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PosTerminal;
use App\Models\Project;
use App\Models\JobAssignment;
use App\Models\TechnicianVisit;
use App\Models\Ticket;
use App\Models\Region;
use App\Models\Employee;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\BusinessLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemReportsController extends Controller
{
    public function index()
    {
        // System Overview Data
        $systemOverview = $this->getSystemOverview();

        // Client Analytics
        $clientAnalytics = $this->getClientAnalytics();

        // Terminal Management
        $terminalData = $this->getTerminalData();

        // Service Activity
        $serviceActivity = $this->getServiceActivity();

        // Asset Management
        $assetData = $this->getAssetData();

        // Employee Performance
        $employeeData = $this->getEmployeeData();

        // Project Management
        $projectData = $this->getProjectData();

        // Regional Analysis
        $regionalData = $this->getRegionalData();

        return view('reports.system-dashboard', [
            'title' => 'System Analytics Dashboard',
            'systemOverview' => $systemOverview,
            'clientAnalytics' => $clientAnalytics,
            'terminalData' => $terminalData,
            'serviceActivity' => $serviceActivity,
            'assetData' => $assetData,
            'employeeData' => $employeeData,
            'projectData' => $projectData,
            'regionalData' => $regionalData,
            'generatedAt' => now(),
        ]);
    }

    private function getSystemOverview()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', 'active')->count(),
            'total_terminals' => PosTerminal::count(),
            'active_terminals' => PosTerminal::where('current_status', 'active')->count(),
            'terminal_uptime' => $this->calculateTerminalUptime(),
            'total_employees' => Employee::where('status', 'active')->count(),
            'total_projects' => Project::count(),
            'active_projects' => Project::whereIn('status', ['active'])->count(),
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
            'total_visits_this_month' => TechnicianVisit::where('visit_date', '>=', $currentMonth)->count(),
            'total_visits_last_month' => TechnicianVisit::where('visit_date', '>=', $lastMonth)
                ->where('visit_date', '<', $currentMonth)->count(),
            'total_assets' => Asset::where('status', 'asset-active')->count(),
            'assigned_assets' => AssetAssignment::where('status', 'assigned')->count(),
            'revenue_impact' => $this->calculateRevenueImpact(),
            'system_health_score' => $this->calculateSystemHealthScore(),
        ];
    }

    private function getClientAnalytics()
    {
        return [
            'client_distribution' => Client::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'clients_by_region' => Client::select('region', DB::raw('count(*) as count'))
                ->groupBy('region')
                ->pluck('count', 'region')
                ->toArray(),
            'client_terminal_counts' => Client::with('posTerminals')
                ->get()
                ->map(function ($client) {
                    return [
                        'name' => $client->company_name,
                        'terminal_count' => $client->posTerminals->count(),
                        'active_terminals' => $client->posTerminals->where('current_status', 'active')->count(),
                        'status' => $client->status,
                    ];
                })
                ->sortByDesc('terminal_count')
                ->values()
                ->toArray(),
            'top_clients_by_activity' => $this->getTopClientsByActivity(),
            'client_growth_trend' => $this->getClientGrowthTrend(),
        ];
    }

   private function getTerminalData()
{
    // Load terminals
    $terminals = PosTerminal::all();

    return [
        'status_distribution' => $terminals->groupBy('current_status')
            ->map->count()
            ->toArray(),
        'model_distribution' => $terminals->groupBy('terminal_model')
            ->map->count()
            ->toArray(),
        'regional_distribution' => $terminals->groupBy('region')
            ->map->count()
            ->toArray(),
        'city_distribution' => $terminals->groupBy('city')
            ->map->count()
            ->toArray(),
        'deployment_status' => $terminals->groupBy('deployment_status')
            ->map->count()
            ->toArray(),
        'terminals_needing_service' => $terminals->whereIn('current_status', ['maintenance', 'faulty'])->count(),
        'average_terminal_age' => $this->calculateAverageTerminalAge(),
        'service_due_analysis' => $this->getServiceDueAnalysis(),
    ];
}

    private function getServiceActivity()
    {
        $currentMonth = Carbon::now()->startOfMonth();

        return [
            'visits_this_month' => TechnicianVisit::where('visit_date', '>=', $currentMonth)->count(),
            'visits_by_technician' => TechnicianVisit::select('technician_id', DB::raw('count(*) as count'))
                ->with('technician:id,first_name,last_name')
                ->groupBy('technician_id')
                ->get()
                ->mapWithKeys(function ($item) {
                    $name = $item->technician ? $item->technician->first_name . ' ' . $item->technician->last_name : 'Unknown';
                    return [$name => $item->count];
                })
                ->toArray(),
            'visit_outcomes' => TechnicianVisit::select('outcome', DB::raw('count(*) as count'))
                ->whereNotNull('outcome')
                ->groupBy('outcome')
                ->pluck('count', 'outcome')
                ->toArray(),
            'tickets_by_priority' => Ticket::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
            'tickets_by_status' => Ticket::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'job_assignments_by_status' => JobAssignment::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'average_resolution_time' => $this->calculateAverageResolutionTime(),
            'technician_productivity' => $this->getTechnicianProductivity(),
        ];
    }

    private function getAssetData()
    {
        return [
            'total_assets' => Asset::where('status', 'asset-active')->count(),
            'assets_by_category' => Asset::where('status', 'asset-active')
                ->select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
            'assignment_status' => [
                'assigned' => AssetAssignment::where('status', 'assigned')->count(),
                'available' => Asset::where('status', 'asset-active')->sum(DB::raw('stock_quantity - assigned_quantity')),
                'total_stock' => Asset::where('status', 'asset-active')->sum('stock_quantity'),
            ],
            'low_stock_alerts' => Asset::where('status', 'asset-active')
                ->whereColumn(DB::raw('stock_quantity - assigned_quantity'), '<=', 'min_stock_level')
                ->count(),
            'asset_utilization' => $this->calculateAssetUtilization(),
            'top_requested_assets' => $this->getTopRequestedAssets(),
            'asset_value_distribution' => $this->getAssetValueDistribution(),
        ];
    }

    private function getEmployeeData()
    {
        return [
            'total_employees' => Employee::where('status', 'active')->count(),
            'employees_by_department' => Employee::select('departments.name', DB::raw('count(*) as count'))
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->where('employees.status', 'active')
                ->groupBy('departments.name')
                ->pluck('count', 'name')
                ->toArray(),
            'employees_by_role' => Employee::select('roles.name', DB::raw('count(*) as count'))
                ->leftJoin('roles', 'employees.role_id', '=', 'roles.id')
                ->where('employees.status', 'active')
                ->groupBy('roles.name')
                ->pluck('count', 'name')
                ->toArray(),
            'technician_workload' => $this->getTechnicianWorkload(),
            'employee_asset_assignments' => AssetAssignment::where('status', 'assigned')
                ->count(),
            'recent_hires' => Employee::where('hire_date', '>=', Carbon::now()->subMonths(3))
                ->where('status', 'active')
                ->count(),
        ];
    }

    private function getProjectData()
    {
        return [
            'projects_by_status' => Project::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'projects_by_type' => Project::select('project_type', DB::raw('count(*) as count'))
                ->groupBy('project_type')
                ->pluck('count', 'project_type')
                ->toArray(),
            'projects_by_client' => Project::select('clients.company_name', DB::raw('count(*) as count'))
                ->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
                ->groupBy('clients.company_name')
                ->pluck('count', 'company_name')
                ->toArray(),
            'project_completion_rate' => $this->calculateProjectCompletionRate(),
            'overdue_projects' => Project::where('end_date', '<', Carbon::now())
                ->whereIn('status', ['active'])
                ->count(),
            'upcoming_deadlines' => Project::where('end_date', '>=', Carbon::now())
                ->where('end_date', '<=', Carbon::now()->addDays(30))
                ->whereIn('status', ['active'])
                ->count(),
        ];
    }

    private function getRegionalData()
    {
        return [
            'terminals_by_region' => PosTerminal::select('region', DB::raw('count(*) as count'))
                ->whereNotNull('region')
                ->groupBy('region')
                ->pluck('count', 'region')
                ->toArray(),
            'service_activity_by_region' => TechnicianVisit::select('pos_terminals.region', DB::raw('count(*) as count'))
                ->leftJoin('pos_terminals', 'technician_visits.pos_terminal_id', '=', 'pos_terminals.id')
                ->where('technician_visits.visit_date', '>=', Carbon::now()->subDays(30))
                ->whereNotNull('pos_terminals.region')
                ->groupBy('pos_terminals.region')
                ->pluck('count', 'region')
                ->toArray(),
            'regional_health_scores' => $this->getRegionalHealthScores(),
            'coverage_analysis' => $this->getCoverageAnalysis(),
        ];
    }

    // Helper methods for calculations
    private function calculateTerminalUptime()
    {
        $totalTerminals = PosTerminal::count();
        if ($totalTerminals === 0) return 0;

        $activeTerminals = PosTerminal::where('current_status', 'active')->count();
        return round(($activeTerminals / $totalTerminals) * 100, 2);
    }

    private function calculateSystemHealthScore()
    {
        $metrics = [
            'terminal_uptime' => $this->calculateTerminalUptime(),
            'ticket_resolution' => $this->getTicketResolutionRate(),
            'asset_availability' => $this->getAssetAvailabilityRate(),
            'employee_utilization' => $this->getEmployeeUtilizationRate(),
        ];

        return round(array_sum($metrics) / count($metrics), 1);
    }

    private function calculateRevenueImpact()
    {
        // Simplified revenue impact calculation
        $activeTerminals = PosTerminal::where('current_status', 'active')->count();
        $estimatedRevenuePerTerminal = 1500; // Monthly revenue estimate
        return $activeTerminals * $estimatedRevenuePerTerminal;
    }

    private function getTopClientsByActivity()
    {
        return TechnicianVisit::select('clients.company_name', DB::raw('count(*) as visit_count'))
            ->leftJoin('pos_terminals', 'technician_visits.pos_terminal_id', '=', 'pos_terminals.id')
            ->leftJoin('clients', 'pos_terminals.client_id', '=', 'clients.id')
            ->where('technician_visits.visit_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('clients.company_name')
            ->orderByDesc('visit_count')
            ->limit(5)
            ->pluck('visit_count', 'company_name')
            ->toArray();
    }

    private function getClientGrowthTrend()
    {
        return Client::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }

    private function calculateAverageTerminalAge()
    {
        $terminals = PosTerminal::whereNotNull('installation_date')->get();
        if ($terminals->isEmpty()) return 0;

        $totalAge = $terminals->sum(function ($terminal) {
            return Carbon::parse($terminal->installation_date)->diffInMonths(Carbon::now());
        });

        return round($totalAge / $terminals->count(), 1);
    }

    private function getServiceDueAnalysis()
    {
        return [
            'overdue' => PosTerminal::where('next_service_due', '<', Carbon::now())->count(),
            'due_this_week' => PosTerminal::whereBetween('next_service_due', [
                Carbon::now(),
                Carbon::now()->addWeek()
            ])->count(),
            'due_this_month' => PosTerminal::whereBetween('next_service_due', [
                Carbon::now(),
                Carbon::now()->addMonth()
            ])->count(),
        ];
    }

    private function calculateAverageResolutionTime()
    {
        $resolvedTickets = Ticket::where('status', 'resolved')
            ->whereNotNull('resolved_at')
            ->get();

        if ($resolvedTickets->isEmpty()) return 0;

        $totalTime = $resolvedTickets->sum(function ($ticket) {
            return Carbon::parse($ticket->created_at)->diffInHours(Carbon::parse($ticket->resolved_at));
        });

        return round($totalTime / $resolvedTickets->count(), 1);
    }

    private function getTechnicianProductivity()
    {
        return TechnicianVisit::select('technician_id', DB::raw('count(*) as visits'))
            ->with('technician:id,first_name,last_name')
            ->where('visit_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('technician_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->technician ? $item->technician->first_name . ' ' . $item->technician->last_name : 'Unknown',
                    'visits' => $item->visits,
                    'productivity_score' => min(100, ($item->visits / 20) * 100) // 20 visits per month = 100%
                ];
            })
            ->sortByDesc('visits')
            ->values()
            ->toArray();
    }

    private function calculateAssetUtilization()
    {
        $totalAssets = Asset::where('status', 'asset-active')->sum('stock_quantity');
        $assignedAssets = Asset::where('status', 'asset-active')->sum('assigned_quantity');

        if ($totalAssets === 0) return 0;
        return round(($assignedAssets / $totalAssets) * 100, 2);
    }

    private function getTopRequestedAssets()
    {
        return DB::table('asset_request_items')
            ->select('assets.name', DB::raw('sum(quantity_requested) as total_requested'))
            ->leftJoin('assets', 'asset_request_items.asset_id', '=', 'assets.id')
            ->groupBy('assets.name')
            ->orderByDesc('total_requested')
            ->limit(5)
            ->pluck('total_requested', 'name')
            ->toArray();
    }

    private function getAssetValueDistribution()
    {
        return [
            'under_500' => Asset::where('unit_price', '<', 500)->count(),
            '500_to_2000' => Asset::whereBetween('unit_price', [500, 2000])->count(),
            '2000_to_5000' => Asset::whereBetween('unit_price', [2000, 5000])->count(),
            'over_5000' => Asset::where('unit_price', '>', 5000)->count(),
        ];
    }

    private function getTechnicianWorkload()
    {
        return JobAssignment::select('technician_id', DB::raw('count(*) as assignments'))
            ->with('technician:id,first_name,last_name')
            ->whereIn('status', ['assigned', 'in_progress'])
            ->groupBy('technician_id')
            ->get()
            ->mapWithKeys(function ($item) {
                $name = $item->technician ? $item->technician->first_name . ' ' . $item->technician->last_name : 'Unknown';
                return [$name => $item->assignments];
            })
            ->toArray();
    }

    private function calculateProjectCompletionRate()
    {
        $totalProjects = Project::count();
        if ($totalProjects === 0) return 0;

        $completedProjects = Project::where('status', 'completed')->count();
        return round(($completedProjects / $totalProjects) * 100, 2);
    }

    private function getRegionalHealthScores()
    {
        // Get unique regions from pos_terminals
        $regions = PosTerminal::whereNotNull('region')
            ->distinct()
            ->pluck('region');

        return $regions->mapWithKeys(function ($regionName) {
            $totalTerminals = PosTerminal::where('region', $regionName)->count();

            if ($totalTerminals === 0) return [$regionName => 0];

            $activeTerminals = PosTerminal::where('region', $regionName)
                ->where('current_status', 'active')
                ->count();
            $healthScore = round(($activeTerminals / $totalTerminals) * 100, 1);

            return [$regionName => $healthScore];
        })->toArray();
    }

    private function getCoverageAnalysis()
    {
        return [
            'total_cities' => PosTerminal::distinct('city')->count('city'),
            'covered_regions' => PosTerminal::whereNotNull('region')->distinct('region')->count('region'),
            'terminals_per_technician' => $this->getTerminalsPerTechnician(),
        ];
    }

    private function getTerminalsPerTechnician()
    {
        $technicians = Employee::where('status', 'active')
            ->whereHas('role', function($q) {
                $q->where('name', 'Technician');
            })->count();

        $terminals = PosTerminal::count();

        return $technicians > 0 ? round($terminals / $technicians, 1) : 0;
    }

    private function getTicketResolutionRate()
    {
        $totalTickets = Ticket::count();
        if ($totalTickets === 0) return 100;

        $resolvedTickets = Ticket::where('status', 'resolved')->count();
        return round(($resolvedTickets / $totalTickets) * 100, 2);
    }

    private function getAssetAvailabilityRate()
    {
        $totalAssets = Asset::where('status', 'asset-active')->sum('stock_quantity');
        if ($totalAssets === 0) return 100;

        $availableAssets = Asset::where('status', 'asset-active')->sum(DB::raw('stock_quantity - assigned_quantity'));
        return round(($availableAssets / $totalAssets) * 100, 2);
    }

    private function getEmployeeUtilizationRate()
    {
        $activeEmployees = Employee::where('status', 'active')->count();
        if ($activeEmployees === 0) return 0;

        $workingEmployees = JobAssignment::distinct('technician_id')
            ->whereIn('status', ['assigned', 'in_progress'])
            ->count();

        return round(($workingEmployees / $activeEmployees) * 100, 2);
    }

    // Export Methods
    public function exportSystemReport()
    {
        $data = [
            'systemOverview' => $this->getSystemOverview(),
            'clientAnalytics' => $this->getClientAnalytics(),
            'terminalData' => $this->getTerminalData(),
            'serviceActivity' => $this->getServiceActivity(),
            'assetData' => $this->getAssetData(),
            'employeeData' => $this->getEmployeeData(),
            'projectData' => $this->getProjectData(),
            'regionalData' => $this->getRegionalData(),
        ];

        return view('reports.system-export', [
            'data' => $data,
            'generatedAt' => now(),
        ]);
    }

    public function exportCsv(Request $request)
    {
        $section = $request->get('section', 'overview');

        $csvData = [];

        switch ($section) {
            case 'clients':
                $csvData = $this->exportClientsData();
                break;
            case 'terminals':
                $csvData = $this->exportTerminalsData();
                break;
            case 'employees':
                $csvData = $this->exportEmployeesData();
                break;
            default:
                $csvData = $this->exportOverviewData();
                break;
        }

        $filename = 'system_report_' . $section . '_' . date('Y-m-d_H-i-s') . '.csv';

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportOverviewData()
    {
        $overview = $this->getSystemOverview();

        return [
            ['Metric', 'Value'],
            ['Total Clients', $overview['total_clients']],
            ['Active Clients', $overview['active_clients']],
            ['Total Terminals', $overview['total_terminals']],
            ['Active Terminals', $overview['active_terminals']],
            ['Terminal Uptime %', $overview['terminal_uptime']],
            ['Total Employees', $overview['total_employees']],
            ['Active Projects', $overview['active_projects']],
            ['Open Tickets', $overview['open_tickets']],
            ['System Health Score', $overview['system_health_score']],
        ];
    }

    private function exportClientsData()
    {
        $clients = Client::with('posTerminals')->get();

        $csvData = [['Client Name', 'Status', 'Total Terminals', 'Active Terminals', 'City', 'Region']];

        foreach ($clients as $client) {
            $csvData[] = [
                $client->company_name,
                strtoupper($client->status),
                $client->posTerminals->count(),
                $client->posTerminals->where('current_status', 'active')->count(),
                $client->city ?? 'N/A',
                $client->region ?? 'N/A',
            ];
        }

        return $csvData;
    }

    private function exportTerminalsData()
    {
        $terminals = PosTerminal::with('client')->get();

        $csvData = [['Terminal ID', 'Client', 'Status', 'Model', 'City', 'Region', 'Installation Date']];

        foreach ($terminals as $terminal) {
            $csvData[] = [
                $terminal->terminal_id,
                $terminal->client->company_name ?? 'N/A',
                strtoupper($terminal->current_status ?? 'UNKNOWN'),
                $terminal->terminal_model ?? 'N/A',
                $terminal->city ?? 'N/A',
                $terminal->region ?? 'N/A',
                $terminal->installation_date ?? 'N/A',
            ];
        }

        return $csvData;
    }

    private function exportEmployeesData()
    {
        $employees = Employee::with('department', 'role')->where('status', 'active')->get();

        $csvData = [['Employee', 'Department', 'Role', 'Hire Date', 'Status']];

        foreach ($employees as $employee) {
            $csvData[] = [
                $employee->first_name . ' ' . $employee->last_name,
                $employee->department->name ?? 'N/A',
                $employee->role->name ?? 'N/A',
                $employee->hire_date ?? 'N/A',
                strtoupper($employee->status),
            ];
        }

        return $csvData;
    }
}
