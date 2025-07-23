<?php

// ==============================================
// ENHANCED DASHBOARD CONTROLLER
// File: app/Http/Controllers/DashboardController.php
// Replace your current DashboardController with this:
// ==============================================

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PosTerminal;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get comprehensive dashboard statistics
        $stats = $this->getDashboardStats();
        
        return view('dashboard', compact('stats'));
    }

    public function technician()
    {
        $employee = auth()->user();
        
        // Technician-specific stats
        $stats = [
            'assigned_jobs' => 0, // Will implement when we build job system
            'completed_today' => 0,
            'pending_jobs' => 0,
            'territories' => ['North', 'Central'], // Example data
        ];
        
        return view('technician.dashboard', compact('stats'));
    }

    public function employee()
    {
        $employee = auth()->user();
        
        // Employee-specific stats
        $stats = [
            'my_requests' => 0,
            'pending_approvals' => 0,
            'recent_activity' => [],
        ];
        
        return view('employee.dashboard', compact('stats'));
    }

    private function getDashboardStats()
    {
        // Basic counts
        $totalTerminals = PosTerminal::count();
        $totalClients = Client::where('status', 'active')->count();
        
        // Terminal status breakdown
        $activeTerminals = PosTerminal::where('status', 'active')->count();
        $offlineTerminals = PosTerminal::where('status', 'offline')->count();
        $maintenanceTerminals = PosTerminal::where('status', 'maintenance')->count();
        $faultyTerminals = PosTerminal::where('status', 'faulty')->count();
        
        // Calculate metrics
        $needAttention = $offlineTerminals + $faultyTerminals;
        $urgentIssues = $faultyTerminals;
        
        // Monthly growth (simulate data for now)
        $newTerminalsThisMonth = PosTerminal::whereMonth('created_at', now()->month)->count();
        $newClientsThisMonth = Client::whereMonth('created_at', now()->month)->count();
        
        // Regional distribution
        $regionalData = $this->getRegionalData();
        
        // Recent activity
        $recentActivity = $this->getRecentActivity();
        
        // Top clients by terminal count
        $topClients = $this->getTopClients();
        
        // System health metrics (simulated)
        $networkUptime = $totalTerminals > 0 ? round(($activeTerminals / $totalTerminals) * 100, 1) : 100;
        $serviceLevel = 98.5; // Simulated
        $avgResponseTime = 2.3; // Simulated hours
        
        // Alerts
        $alerts = $this->getSystemAlerts();
        
        return [
            // Main metrics
            'total_terminals' => $totalTerminals,
            'active_terminals' => $activeTerminals,
            'offline_terminals' => $offlineTerminals,
            'maintenance_terminals' => $maintenanceTerminals,
            'faulty_terminals' => $faultyTerminals,
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
        ];
    }

    private function getRegionalData()
    {
        $regions = ['North', 'South', 'East', 'West', 'Central'];
        $regionalData = [];
        
        foreach ($regions as $region) {
            $totalInRegion = PosTerminal::where('region', $region)->count();
            $activeInRegion = PosTerminal::where('region', $region)->where('status', 'active')->count();
            $issuesInRegion = PosTerminal::where('region', $region)
                ->whereIn('status', ['offline', 'faulty', 'maintenance'])
                ->count();
            
            $regionalData[$region] = [
                'total' => $totalInRegion,
                'active' => $activeInRegion,
                'issues' => $issuesInRegion,
            ];
        }
        
        return $regionalData;
    }

    private function getRecentActivity()
    {
        $activities = collect();
        
        // Recent terminals added
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
        
        // Add some example maintenance activities
        if ($activities->count() < 5) {
            $activities->push([
                'icon' => '🔧',
                'color' => '#ff9800',
                'title' => 'Maintenance Completed',
                'description' => 'Routine maintenance completed on 3 terminals in North region',
                'time' => '2 hours ago',
            ]);
            
            $activities->push([
                'icon' => '⚠️',
                'color' => '#f44336',
                'title' => 'Service Alert',
                'description' => 'Terminal POS-045 reported offline, technician dispatched',
                'time' => '4 hours ago',
            ]);
        }
        
        return $activities->sortByDesc('time')->take(8);
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
                ];
            });
    }

    private function getSystemAlerts()
    {
        $alerts = collect();
        
        // Check for terminals needing attention
        $faultyCount = PosTerminal::where('status', 'faulty')->count();
        if ($faultyCount > 0) {
            $alerts->push([
                'type' => 'critical',
                'message' => "{$faultyCount} terminals require immediate attention"
            ]);
        }
        
        // Check for clients with expiring contracts
        $expiringContracts = Client::where('contract_end_date', '<=', now()->addDays(30))
            ->where('contract_end_date', '>', now())
            ->count();
            
        if ($expiringContracts > 0) {
            $alerts->push([
                'type' => 'warning',
                'message' => "{$expiringContracts} client contracts expiring within 30 days"
            ]);
        }
        
        // Check for high offline terminals in any region
        $regions = $this->getRegionalData();
        foreach ($regions as $region => $data) {
            if ($data['total'] > 0 && ($data['issues'] / $data['total']) > 0.2) {
                $alerts->push([
                    'type' => 'warning',
                    'message' => "High offline rate detected in {$region} region"
                ]);
            }
        }
        
        return $alerts;
    }
}
