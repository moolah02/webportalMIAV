<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PosTerminal;
use App\Models\Project;
use App\Models\JobAssignment;
use App\Models\TechnicianVisit;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function show(Client $client)
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

        // Get terminals with pagination
        $terminals = PosTerminal::where('client_id', $client->id)
            ->with(['region'])
            ->orderBy('merchant_name')
            ->paginate(20);

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

        return view('client-dashboards.show', [
            'title' => $client->company_name . ' Dashboard',
            'client' => $client,
            'terminalStats' => $terminalStats,
            'terminals' => $terminals,
            'projects' => $projects,
            'recentVisits' => $recentVisits,
            'recentAssignments' => $recentAssignments,
            'openTickets' => $openTickets,
        ]);
    }

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
}
