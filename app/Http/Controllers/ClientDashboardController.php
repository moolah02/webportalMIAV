<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PosTerminal;
use App\Models\Project;
use App\Models\JobAssignment;
use App\Models\TechnicianVisit;
use App\Models\Ticket;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $clients = Client::with(['posTerminals'])
            ->withCount(['posTerminals'])
            ->orderBy('company_name')
            ->get()
            ->map(function ($client) {
                // Get terminal status breakdown
                $statusBreakdown = $client->posTerminals()
                    ->select('current_status', DB::raw('count(*) as count'))
                    ->groupBy('current_status')
                    ->pluck('count', 'current_status')
                    ->toArray();

                // Get recent activity count (last 30 days)
                $recentActivity = TechnicianVisit::where('client_id', $client->id)
                    ->where('visit_date', '>=', Carbon::now()->subDays(30))
                    ->count();

                $client->status_breakdown = $statusBreakdown;
                $client->recent_activity_count = $recentActivity;

                return $client;
            });

        return view('client-dashboards.index', [
            'title' => 'Client Dashboards',
            'clients' => $clients
        ]);
    }

    public function show(Client $client, Request $request)
    {
        // Load relationships
        $client->load(['posTerminals', 'projects']);

        // Get terminal statistics
        $terminalStats = $this->getTerminalStatistics($client);

        // Get recent visits (last 30 days)
        $recentVisits = TechnicianVisit::with(['technician'])
            ->where('client_id', $client->id)
            ->where('visit_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('visit_date', 'desc')
            ->limit(10)
            ->get();

        // Get open tickets
        $openTickets = Ticket::with(['technician', 'posTerminal'])
            ->where('client_id', $client->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Build terminals query with filters
        $terminalsQuery = PosTerminal::where('client_id', $client->id)
            ->with(['region']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $terminalsQuery->where(function($q) use ($search) {
                $q->where('terminal_id', 'like', "%{$search}%")
                  ->orWhere('merchant_name', 'like', "%{$search}%")
                  ->orWhere('merchant_contact_person', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $terminalsQuery->where('current_status', $request->status);
        }

        // Apply region filter
        if ($request->filled('region')) {
            $terminalsQuery->where('region_id', $request->region);
        }

        // Apply city filter
        if ($request->filled('city')) {
            $terminalsQuery->where('city', $request->city);
        }

        // Get terminals with pagination
        $terminals = $terminalsQuery->orderBy('merchant_name')->paginate(20);

        // Get projects
        $projects = Project::where('client_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get recent job assignments
        $recentAssignments = JobAssignment::with(['technician'])
            ->where('client_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get filter options
        $regions = Region::orderBy('name')->get();
        $cities = PosTerminal::where('client_id', $client->id)
            ->select('city')
            ->distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city');

        return view('client-dashboards.show', [
            'title' => $client->company_name . ' Dashboard',
            'client' => $client,
            'terminalStats' => $terminalStats,
            'terminals' => $terminals,
            'projects' => $projects,
            'recentVisits' => $recentVisits,
            'recentAssignments' => $recentAssignments,
            'openTickets' => $openTickets,
            'regions' => $regions,
            'cities' => $cities,
        ]);
    }

    // Export complete client data
    // Add this method to your ClientDashboardController class

public function exportData(Client $client)
{
    try {
        // Load relationships and get comprehensive data
        $client->load(['posTerminals', 'projects']);
        $terminalStats = $this->getTerminalStatistics($client);

        // Get recent visits (last 30 days)
        $recentVisits = TechnicianVisit::with(['technician', 'posTerminal'])
            ->where('client_id', $client->id)
            ->where('visit_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('visit_date', 'desc')
            ->get();

        // Get open tickets
        $openTickets = Ticket::with(['technician', 'posTerminal'])
            ->where('client_id', $client->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get projects
        $projects = Project::where('client_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Return the professional report view
        return view('reports.client-export', [
            'client' => $client,
            'terminalStats' => $terminalStats,
            'recentVisits' => $recentVisits,
            'openTickets' => $openTickets,
            'projects' => $projects,
        ]);

    } catch (\Exception $e) {
        return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
    }
}

    // Export terminals table as CSV
    public function exportTable(Client $client, Request $request)
    {
        try {
            // Build the same query as the main dashboard with filters
            $terminalsQuery = PosTerminal::where('client_id', $client->id)
                ->with(['region']);

            // Apply the same filters as the main view
            if ($request->filled('search')) {
                $search = $request->search;
                $terminalsQuery->where(function($q) use ($search) {
                    $q->where('terminal_id', 'like', "%{$search}%")
                      ->orWhere('merchant_name', 'like', "%{$search}%")
                      ->orWhere('merchant_contact_person', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $terminalsQuery->where('current_status', $request->status);
            }

            if ($request->filled('region')) {
                $terminalsQuery->where('region_id', $request->region);
            }

            if ($request->filled('city')) {
                $terminalsQuery->where('city', $request->city);
            }

            $terminals = $terminalsQuery->orderBy('merchant_name')->get();

            $csvData = [];
            $csvData[] = [
                'Terminal ID',
                'Client/Bank',
                'Merchant',
                'Contact Person',
                'Contact Phone',
                'City',
                'Region',
                'Status',
                'Last Service Date',
                'Installation Date',
            ];

            foreach ($terminals as $terminal) {
                $csvData[] = [
                    $terminal->terminal_id,
                    $client->company_name,
                    $terminal->merchant_name ?? '',
                    $terminal->merchant_contact_person ?? '',
                    $terminal->merchant_phone ?? '',
                    $terminal->city ?? '',
                    $terminal->region->name ?? 'Unknown',
                    strtoupper($terminal->current_status ?? 'UNKNOWN'),
                    $terminal->last_service_date ?? 'Never',
                    $terminal->installation_date ?? '',
                ];
            }

            $filename = 'terminals_' . $client->client_code . '_' . date('Y-m-d_H-i-s') . '.csv';

            $callback = function() use ($csvData) {
                $file = fopen('php://output', 'w');
                foreach ($csvData as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return Response::stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export table: ' . $e->getMessage());
        }
    }

    // Create new terminal
    public function createTerminal(Client $client)
    {
        $regions = Region::orderBy('name')->get();

        return view('terminals.create', [
            'client' => $client,
            'regions' => $regions,
            'title' => 'Add Terminal for ' . $client->company_name
        ]);
    }

    // Store new terminal
    public function storeTerminal(Request $request, Client $client)
    {
        $request->validate([
            'terminal_id' => 'required|string|unique:pos_terminals,terminal_id',
            'merchant_name' => 'required|string|max:255',
            'merchant_contact_person' => 'nullable|string|max:255',
            'merchant_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'current_status' => 'required|in:active,offline,maintenance,faulty',
            'installation_date' => 'nullable|date',
        ]);

        try {
            PosTerminal::create([
                'client_id' => $client->id,
                'terminal_id' => $request->terminal_id,
                'merchant_name' => $request->merchant_name,
                'merchant_contact_person' => $request->merchant_contact_person,
                'merchant_phone' => $request->merchant_phone,
                'address' => $request->address,
                'city' => $request->city,
                'region_id' => $request->region_id,
                'current_status' => $request->current_status,
                'installation_date' => $request->installation_date,
            ]);

            return redirect()->route('client-dashboards.show', $client)
                ->with('success', 'Terminal added successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to create terminal: ' . $e->getMessage())
                ->withInput();
        }
    }

    // View terminal details
   // Update this method in your ClientDashboardController

public function viewTerminal(Client $client, PosTerminal $terminal)
{
    // Ensure terminal belongs to this client
    if ($terminal->client_id !== $client->id) {
        abort(403, 'Unauthorized access to terminal');
    }

    $terminal->load(['region']);

    // Get terminal history
    $visits = TechnicianVisit::with(['technician'])
        ->where('pos_terminal_id', $terminal->id)
        ->orderBy('visit_date', 'desc')
        ->get();

    // Get related tickets
    $tickets = Ticket::with(['technician'])
        ->where('pos_terminal_id', $terminal->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Instead of trying to use 'terminals.show', redirect back to dashboard with terminal details
    // or create a simple modal/overlay view
    return redirect()->route('client-dashboards.show', $client)
        ->with('selectedTerminal', $terminal->id)
        ->with('success', 'Terminal details for ' . $terminal->terminal_id);
}

    // Edit terminal
    public function editTerminal(Client $client, PosTerminal $terminal)
    {
        // Ensure terminal belongs to this client
        if ($terminal->client_id !== $client->id) {
            abort(403, 'Unauthorized access to terminal');
        }

        $regions = Region::orderBy('name')->get();

        return view('terminals.edit', [
            'client' => $client,
            'terminal' => $terminal,
            'regions' => $regions,
            'title' => 'Edit Terminal ' . $terminal->terminal_id
        ]);
    }

    // Update terminal
    public function updateTerminal(Request $request, Client $client, PosTerminal $terminal)
    {
        // Ensure terminal belongs to this client
        if ($terminal->client_id !== $client->id) {
            abort(403, 'Unauthorized access to terminal');
        }

        $request->validate([
            'terminal_id' => 'required|string|unique:pos_terminals,terminal_id,' . $terminal->id,
            'merchant_name' => 'required|string|max:255',
            'merchant_contact_person' => 'nullable|string|max:255',
            'merchant_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'current_status' => 'required|in:active,offline,maintenance,faulty',
            'installation_date' => 'nullable|date',
            'last_service_date' => 'nullable|date',
        ]);

        try {
            $terminal->update([
                'terminal_id' => $request->terminal_id,
                'merchant_name' => $request->merchant_name,
                'merchant_contact_person' => $request->merchant_contact_person,
                'merchant_phone' => $request->merchant_phone,
                'address' => $request->address,
                'city' => $request->city,
                'region_id' => $request->region_id,
                'current_status' => $request->current_status,
                'installation_date' => $request->installation_date,
                'last_service_date' => $request->last_service_date,
            ]);

            return redirect()->route('client-dashboards.show', $client)
                ->with('success', 'Terminal updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update terminal: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Create new project
    public function createProject(Client $client)
    {
        return view('projects.create', [
            'client' => $client,
            'title' => 'Create Project for ' . $client->company_name
        ]);
    }

    // Store new project
    public function storeProject(Request $request, Client $client)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        try {
            // Generate project code
            $projectCode = $this->generateProjectCode($client, $request->project_name);

            Project::create([
                'client_id' => $client->id,
                'project_name' => $request->project_name,
                'project_code' => $projectCode,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'priority' => $request->priority,
            ]);

            return redirect()->route('client-dashboards.show', $client)
                ->with('success', 'Project created successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to create project: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Generate reports
    public function generateReport(Client $client, $reportType)
    {
        try {
            switch ($reportType) {
                case 'monthly':
                    return $this->generateMonthlyReport($client);
                case 'terminals':
                    return $this->generateTerminalReport($client);
                case 'activity':
                    return $this->generateActivityReport($client);
                default:
                    return back()->with('error', 'Invalid report type');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    // Monthly summary report
    private function generateMonthlyReport(Client $client)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $data = [
            'client' => $client->company_name,
            'report_period' => $currentMonth->format('F Y'),
            'terminal_stats' => $this->getTerminalStatistics($client),
            'visits_current_month' => TechnicianVisit::where('client_id', $client->id)
                ->where('visit_date', '>=', $currentMonth)
                ->count(),
            'visits_last_month' => TechnicianVisit::where('client_id', $client->id)
                ->where('visit_date', '>=', $lastMonth)
                ->where('visit_date', '<', $currentMonth)
                ->count(),
            'tickets_opened' => Ticket::where('client_id', $client->id)
                ->where('created_at', '>=', $currentMonth)
                ->count(),
            'tickets_resolved' => Ticket::where('client_id', $client->id)
                ->where('status', 'resolved')
                ->where('updated_at', '>=', $currentMonth)
                ->count(),
            'generated_at' => now(),
        ];

        return view('reports.monthly', $data);
    }

    // Terminal performance report
    private function generateTerminalReport(Client $client)
    {
        $terminals = $client->posTerminals()->with(['region'])->get();
        $terminalStats = $this->getTerminalStatistics($client);

        $terminalData = $terminals->map(function ($terminal) {
            $recentVisits = TechnicianVisit::where('pos_terminal_id', $terminal->id)
                ->where('visit_date', '>=', Carbon::now()->subDays(90))
                ->count();

            $openTickets = Ticket::where('pos_terminal_id', $terminal->id)
                ->whereIn('status', ['open', 'in_progress'])
                ->count();

            return [
                'terminal_id' => $terminal->terminal_id,
                'merchant_name' => $terminal->merchant_name,
                'city' => $terminal->city,
                'region' => $terminal->region->name ?? 'Unknown',
                'status' => $terminal->current_status,
                'last_service' => $terminal->last_service_date,
                'recent_visits' => $recentVisits,
                'open_tickets' => $openTickets,
                'installation_date' => $terminal->installation_date,
            ];
        });

        return view('reports.terminals', [
            'client' => $client,
            'terminals' => $terminalData,
            'stats' => $terminalStats,
            'generated_at' => now(),
        ]);
    }

    // Activity report
    private function generateActivityReport(Client $client)
    {
        $visits = TechnicianVisit::with(['technician', 'posTerminal'])
            ->where('client_id', $client->id)
            ->where('visit_date', '>=', Carbon::now()->subDays(90))
            ->orderBy('visit_date', 'desc')
            ->get();

        $tickets = Ticket::with(['technician', 'posTerminal'])
            ->where('client_id', $client->id)
            ->where('created_at', '>=', Carbon::now()->subDays(90))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.activity', [
            'client' => $client,
            'visits' => $visits,
            'tickets' => $tickets,
            'generated_at' => now(),
        ]);
    }

    // AJAX endpoint to get filter data
    public function getFilterData(Client $client): JsonResponse
    {
        try {
            $cities = PosTerminal::where('client_id', $client->id)
                ->select('city')
                ->distinct()
                ->whereNotNull('city')
                ->orderBy('city')
                ->pluck('city');

            $regions = Region::orderBy('name')->get(['id', 'name']);

            return response()->json([
                'cities' => $cities,
                'regions' => $regions,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Private helper methods
    private function getTerminalStatistics(Client $client)
    {
        $terminals = $client->posTerminals();

        return [
            'total' => $terminals->count(),
            'by_status' => $terminals->select('current_status', DB::raw('count(*) as count'))
                ->groupBy('current_status')
                ->pluck('count', 'current_status')
                ->toArray(),
            'by_region' => $terminals->with('region')
                ->select('region_id', DB::raw('count(*) as count'))
                ->groupBy('region_id')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->region->name ?? 'Unknown Region' => $item->count
                    ];
                })
                ->toArray(),
            'by_city' => $terminals->select('city', DB::raw('count(*) as count'))
                ->groupBy('city')
                ->orderBy('count', 'desc')
                ->pluck('count', 'city')
                ->toArray(),
        ];
    }

    private function generateProjectCode(Client $client, $projectName)
    {
        $clientPrefix = substr($client->client_code, 0, 3);
        $typePrefix = 'PRJ';
        $dateCode = date('Ym');

        // Get next sequence number for this month
        $lastProject = Project::where('client_id', $client->id)
            ->where('project_code', 'like', $clientPrefix . '-' . $typePrefix . '-' . $dateCode . '-%')
            ->latest('id')
            ->first();

        $sequence = 1;
        if ($lastProject) {
            $lastSequence = intval(substr($lastProject->project_code, -2));
            $sequence = $lastSequence + 1;
        }

        return $clientPrefix . '-' . $typePrefix . '-' . $dateCode . '-' . str_pad($sequence, 2, '0', STR_PAD_LEFT);
    }

}
