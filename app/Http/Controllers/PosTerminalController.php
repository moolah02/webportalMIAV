<?php

namespace App\Http\Controllers;

use App\Models\PosTerminal;
use App\Models\Client;
use App\Models\Region;
use App\Models\ImportMapping;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PosTerminalController extends Controller
{
    public function create()
    {
        $clients = Client::orderBy('company_name')->get();
        $regions = PosTerminal::distinct()->pluck('region')->filter()->sort();

        // Get options - handle if Category model doesn't exist
        $statusOptions = collect(['active', 'offline', 'faulty', 'maintenance']);
        $serviceTypes = collect(['maintenance', 'installation', 'repair']);

        try {
            if (class_exists('App\Models\Category')) {
                $statusOptions = Category::getTerminalStatuses();
                $serviceTypes = Category::getServiceTypes();
            }
        } catch (\Exception $e) {
            Log::warning('Category model not available: ' . $e->getMessage());
        }

        return view('pos-terminals.create', compact(
            'clients',
            'regions',
            'statusOptions',
            'serviceTypes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id' => 'required|string|unique:pos_terminals,terminal_id',
            'client_id' => 'required|exists:clients,id',
            'merchant_name' => 'required|string',
            'merchant_contact_person' => 'nullable|string',
            'merchant_phone' => 'nullable|string',
            'merchant_email' => 'nullable|email',
            'physical_address' => 'nullable|string',
            'region' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'business_type' => 'nullable|string',
            'terminal_model' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'installation_date' => 'nullable|date',
            'contract_details' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        // Set default status if not provided
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['current_status'] = $validated['status']; // Sync both status fields

        PosTerminal::create($validated);

        return redirect()->route('pos-terminals.index')
                         ->with('success', 'POS Terminal created successfully.');
    }

    public function show(PosTerminal $posTerminal)
    {
        // Load only existing relationships - remove problematic ones for now
        $posTerminal->load(['client']);

        // Try to load other relationships if they exist, but don't fail if they don't
        try {
            if (method_exists($posTerminal, 'regionModel')) {
                $posTerminal->load(['regionModel']);
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load regionModel: ' . $e->getMessage());
        }

        try {
            if (method_exists($posTerminal, 'jobAssignments')) {
                $posTerminal->load(['jobAssignments']);
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load jobAssignments: ' . $e->getMessage());
        }

        try {
            if (method_exists($posTerminal, 'tickets')) {
                $posTerminal->load(['tickets']);
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load tickets: ' . $e->getMessage());
        }

        // Get related categories for display - handle if Category model doesn't exist
        $statusCategories = collect(['active', 'offline', 'faulty', 'maintenance']);
        $serviceTypes = collect(['maintenance', 'installation', 'repair']);

        try {
            if (class_exists('App\Models\Category')) {
                $statusCategories = Category::getTerminalStatuses();
                $serviceTypes = Category::getServiceTypes();
            }
        } catch (\Exception $e) {
            \Log::warning('Category model not available: ' . $e->getMessage());
        }

        return view('pos-terminals.show', compact(
            'posTerminal',
            'statusCategories',
            'serviceTypes'
        ));
    }

    public function edit(PosTerminal $posTerminal)
    {
        $clients = Client::orderBy('company_name')->get();
        $regions = PosTerminal::distinct()->pluck('region')->filter()->sort();

        // Get options - handle if Category model doesn't exist
        $statusOptions = collect(['active', 'offline', 'faulty', 'maintenance']);
        $serviceTypes = collect(['maintenance', 'installation', 'repair']);

        try {
            if (class_exists('App\Models\Category')) {
                $statusOptions = Category::getTerminalStatuses();
                $serviceTypes = Category::getServiceTypes();
            }
        } catch (\Exception $e) {
            Log::warning('Category model not available: ' . $e->getMessage());
        }

        return view('pos-terminals.edit', compact(
            'posTerminal',
            'clients',
            'regions',
            'statusOptions',
            'serviceTypes'
        ));
    }

    public function update(Request $request, PosTerminal $posTerminal)
    {
        $validated = $request->validate([
            'terminal_id' => 'required|string|unique:pos_terminals,terminal_id,' . $posTerminal->id,
            'client_id' => 'required|exists:clients,id',
            'merchant_name' => 'required|string',
            'merchant_contact_person' => 'nullable|string',
            'merchant_phone' => 'nullable|string',
            'merchant_email' => 'nullable|email',
            'physical_address' => 'nullable|string',
            'region' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'business_type' => 'nullable|string',
            'terminal_model' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'installation_date' => 'nullable|date',
            'last_service_date' => 'nullable|date',
            'next_service_due' => 'nullable|date',
            'contract_details' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        // Sync both status fields
        if (isset($validated['status'])) {
            $validated['current_status'] = $validated['status'];
        }

        $posTerminal->update($validated);

        return redirect()->route('pos-terminals.show', $posTerminal)
                         ->with('success', 'POS Terminal updated successfully.');
    }

    public function updateStatus(Request $request, PosTerminal $posTerminal)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $posTerminal->update([
            'status' => $request->status,
            'current_status' => $request->status,
        ]);

        return back()->with('success', 'Terminal status updated successfully.');
    }

    public function destroy(PosTerminal $posTerminal)
    {
        $posTerminal->delete();

        return redirect()->route('pos-terminals.index')
                         ->with('success', 'POS Terminal deleted successfully.');
    }

    /**
     * Delete a column mapping
     */
    public function deleteColumnMapping(ImportMapping $mapping)
    {
        try {
            $mapping->delete();
            return redirect()->back()->with('success', 'Column mapping deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting column mapping: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting column mapping: ' . $e->getMessage());
        }
    }

    /**
     * Toggle column mapping active status
     */
    public function toggleColumnMapping(ImportMapping $mapping)
    {
        try {
            $mapping->update(['is_active' => !$mapping->is_active]);
            $status = $mapping->is_active ? 'activated' : 'deactivated';
            return redirect()->back()->with('success', "Column mapping {$status} successfully!");
        } catch (\Exception $e) {
            Log::error('Error toggling column mapping: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating column mapping: ' . $e->getMessage());
        }
    }

    /**
     * Get column mapping data for AJAX requests
     */
    public function getColumnMapping(ImportMapping $mapping)
    {
        try {
            return response()->json([
                'success' => true,
                'mapping' => $mapping
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading column mapping: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export terminals to CSV
     */
    public function export(Request $request)
{
    try {
        $query = PosTerminal::with('client');

        // Apply same filters as index (qualified columns to avoid ambiguity)
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('pos_terminals.terminal_id', 'like', "%{$search}%")
                  ->orWhere('pos_terminals.merchant_name', 'like', "%{$search}%")
                  ->orWhere('pos_terminals.merchant_contact_person', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client')) {
            $query->where('pos_terminals.client_id', $request->integer('client'));
        }

        if ($request->filled('status')) {
            $query->where('pos_terminals.status', $request->string('status'));
        }

        if ($request->filled('region')) {
            $query->where('pos_terminals.region', $request->string('region'));
        }

        if ($request->filled('city')) {
            $query->where('pos_terminals.city', $request->string('city'));
        }

        if ($request->filled('province')) {
            $query->where('pos_terminals.province', $request->string('province'));
        }

        $filename = 'pos_terminals_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // (Optional) BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV headers
            fputcsv($handle, [
                'Terminal ID',
                'Client',
                'Merchant Name',
                'Contact Person',
                'Phone',
                'Email',
                'Address',
                'City',
                'Province',
                'Region',
                'Business Type',
                'Terminal Model',
                'Serial Number',
                'Status',
                'Installation Date',
                'Last Service Date',
                'Next Service Due',
                'Contract Details',
            ]);

            // Stream in chunks to avoid memory issues
            $query->orderBy('pos_terminals.id')
                  ->chunk(1000, function ($rows) use ($handle) {
                      foreach ($rows as $terminal) {
                          fputcsv($handle, [
                              $terminal->terminal_id,
                              optional($terminal->client)->company_name,
                              $terminal->merchant_name,
                              $terminal->merchant_contact_person,
                              $terminal->merchant_phone,
                              $terminal->merchant_email,
                              $terminal->physical_address,
                              $terminal->city,
                              $terminal->province,
                              $terminal->region,
                              $terminal->business_type,
                              $terminal->terminal_model,
                              $terminal->serial_number,
                              $terminal->status,
                              (string) $terminal->installation_date,
                              (string) $terminal->last_service_date,
                              (string) $terminal->next_service_due,
                              $terminal->contract_details,
                          ]);
                      }
                  });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    } catch (\Throwable $e) {
        Log::error('Export error: ' . $e->getMessage());
        return back()->with('error', 'Export failed: ' . $e->getMessage());
    }
}


    public function showImport()
    {
        $clients = Client::orderBy('company_name')->get();

        // Get available column mappings
        $mappings = collect(); // Empty collection for now
        try {
            if (class_exists('App\Models\ImportMapping')) {
                $mappings = ImportMapping::where('is_active', true)->get();
            }
        } catch (\Exception $e) {
            Log::warning('ImportMapping model not available: ' . $e->getMessage());
        }

        return view('pos-terminals.import', compact('clients', 'mappings'));
    }

    /**
     * Show column mapping management page
     */
    public function showColumnMapping()
    {
        $clients = Client::orderBy('company_name')->get();

        $mappings = collect(); // Empty collection for now
        try {
            if (class_exists('App\Models\ImportMapping')) {
                $mappings = ImportMapping::with('client')->where('is_active', true)->get();
            }
        } catch (\Exception $e) {
            Log::warning('ImportMapping model not available: ' . $e->getMessage());
        }

        return view('pos-terminals.column-mapping', compact('clients', 'mappings'));
    }

    /**
     * Store new column mapping
     */
    public function storeColumnMapping(Request $request)
    {
        $validated = $request->validate([
            'mapping_name' => 'required|string|unique:import_mappings,mapping_name',
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'column_mappings' => 'required|array',
            'column_mappings.*' => 'nullable|integer|min:0|max:50'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true;

        try {
            if (class_exists('App\Models\ImportMapping')) {
                ImportMapping::create($validated);
                return redirect()->back()->with('success', 'Column mapping saved successfully!');
            } else {
                return redirect()->back()->with('error', 'ImportMapping model not available. Please create the model first.');
            }
        } catch (\Exception $e) {
            Log::error('Error saving column mapping: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error saving column mapping: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $query = PosTerminal::with(['client']);

        // Apply filters with proper column qualification
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pos_terminals.terminal_id', 'like', "%{$search}%")
                  ->orWhere('pos_terminals.merchant_name', 'like', "%{$search}%")
                  ->orWhere('pos_terminals.merchant_contact_person', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client')) {
            $query->where('pos_terminals.client_id', $request->client);
        }

        if ($request->filled('status')) {
            // ENSURE we only accept valid status values
            $validStatuses = ['active', 'offline', 'faulty', 'maintenance'];
            $status = $request->status;

            if (in_array($status, $validStatuses)) {
                $query->where('pos_terminals.status', $status);
            }
        }

        if ($request->filled('region')) {
            $query->where('pos_terminals.region', $request->region);
        }

        if ($request->filled('city')) {
            $query->where('pos_terminals.city', $request->city);
        }

        if ($request->filled('province')) {
            $query->where('pos_terminals.province', $request->province);
        }

        // FIXED: Calculate filtered statistics BEFORE pagination
        $stats = $this->calculateFilteredStats(clone $query);

        // Then paginate
        $terminals = $query->paginate(20);

        // Get filter options
        $clients = Client::orderBy('company_name')->get();
        $regions = PosTerminal::distinct()->pluck('region')->filter()->sort();
        $cities = PosTerminal::distinct()->pluck('city')->filter()->sort();
        $provinces = PosTerminal::distinct()->pluck('province')->filter()->sort();

        // FIXED: Proper status options format
        $statusOptions = [
            'active' => 'Active',
            'offline' => 'Offline',
            'faulty' => 'Faulty',
            'maintenance' => 'Maintenance'
        ];

        // Get mappings for import tab
        $mappings = collect();
        try {
            if (class_exists('App\Models\ImportMapping')) {
                $mappings = ImportMapping::where('is_active', true)->get();
            }
        } catch (\Exception $e) {
            Log::warning('ImportMapping model not available: ' . $e->getMessage());
        }

        // Handle AJAX requests
        if ($request->ajax() || $request->has('ajax')) {
            return $this->handleAjaxRequest($request, clone $query, $stats);
        }

        return view('pos-terminals.index', compact(
            'terminals',
            'clients',
            'regions',
            'cities',
            'provinces',
            'statusOptions',
            'mappings',
            'stats'
        ));
    }

    /**
     * Handle AJAX requests for filtered data
     */
    private function handleAjaxRequest(Request $request, $query, $stats)
    {
        try {
            // Get filtered terminals for charts
            $terminals = $query->get();

            // Generate chart data
            $chartData = $this->generateChartData($terminals);

            // Generate table HTML
            $tableHtml = $this->generateTableHtml($terminals, $request);

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'chartData' => $chartData,
                'tableHtml' => $tableHtml
            ]);

        } catch (\Exception $e) {
            Log::error('AJAX request error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading filtered data'
            ], 500);
        }
    }

    /**
     * Generate chart data from filtered terminals
     */
    private function generateChartData($terminals)
    {
        // Status distribution
        $statusCounts = $terminals->countBy('status');

        // Location distribution (top 10)
        $locationCounts = $terminals->countBy(function($terminal) {
            return $terminal->region ?: 'No Region';
        })->sortDesc()->take(10);

        return [
            'status' => [
                'active' => $statusCounts['active'] ?? 0,
                'offline' => $statusCounts['offline'] ?? 0,
                'faulty' => ($statusCounts['faulty'] ?? 0) + ($statusCounts['maintenance'] ?? 0)
            ],
            'locations' => [
                'labels' => $locationCounts->keys()->toArray(),
                'data' => $locationCounts->values()->toArray()
            ]
        ];
    }

    /**
     * Generate table HTML for filtered results
     */
    private function generateTableHtml($terminals, Request $request)
    {
        // Paginate the terminals
        $currentPage = $request->get('page', 1);
        $perPage = 20;
        $paginatedTerminals = new \Illuminate\Pagination\LengthAwarePaginator(
            $terminals->forPage($currentPage, $perPage),
            $terminals->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        $paginatedTerminals->appends($request->query());

        return view('pos-terminals.partials.table', ['terminals' => $paginatedTerminals])->render();
    }

    /**
     * Get filtered statistics endpoint
     */
    public function getFilteredStats(Request $request)
    {
        $query = PosTerminal::query();

        // Apply same filters as index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('terminal_id', 'like', "%{$search}%")
                  ->orWhere('merchant_name', 'like', "%{$search}%")
                  ->orWhere('merchant_contact_person', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        $stats = $this->calculateFilteredStats($query);
        $chartData = $this->generateChartData($query->get());

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'chartData' => $chartData
        ]);
    }

    public function createTicket(Request $request, PosTerminal $posTerminal)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,critical',
            'issue_type' => 'required|string',
            'description' => 'required|string',
            'reported_by' => 'nullable|string',
            'contact_number' => 'nullable|string'
        ]);

        // Create ticket logic here
        // $ticket = $posTerminal->tickets()->create($validated);

        return response()->json(['success' => true, 'message' => 'Ticket created successfully']);
    }

    public function scheduleService(Request $request, PosTerminal $posTerminal)
    {
        $validated = $request->validate([
            'service_type' => 'required|string',
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required',
            'technician_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string'
        ]);

        if ($request->update_next_service) {
            $posTerminal->update([
                'next_service_due' => $validated['scheduled_date']
            ]);
        }

        // Create service appointment logic

        return response()->json(['success' => true, 'message' => 'Service scheduled successfully']);
    }

    public function addNote(Request $request, PosTerminal $posTerminal)
    {
        $validated = $request->validate([
            'note_type' => 'required|string',
            'notes' => 'required|string',
            'is_important' => 'boolean'
        ]);

        // Add note logic here

        return response()->json(['success' => true, 'message' => 'Note added successfully']);
    }

    public function getStatistics(PosTerminal $posTerminal)
    {
        return response()->json([
            'total_jobs' => $posTerminal->jobs()->count(),
            'service_reports' => $posTerminal->serviceReports()->count(),
            'open_tickets' => $posTerminal->tickets()->where('status', 'open')->count(),
            'days_since_service' => $posTerminal->last_service_date
                ? $posTerminal->last_service_date->diffInDays(now())
                : null
        ]);
    }

    /**
     * Calculate statistics for filtered results - COMPLETE FIX
     */
    private function calculateFilteredStats($baseQuery)
    {
        // Basic status counts - FIX: Qualify ALL column names
        $totalTerminals = $baseQuery->count();
        $activeTerminals = (clone $baseQuery)->where('pos_terminals.status', 'active')->count();
        $offlineTerminals = (clone $baseQuery)->where('pos_terminals.status', 'offline')->count();
        $faultyTerminals = (clone $baseQuery)->whereIn('pos_terminals.status', ['faulty', 'maintenance'])->count();

        // Service-related statistics
        $recentlyServiced = (clone $baseQuery)
            ->whereNotNull('pos_terminals.last_service_date')
            ->where('pos_terminals.last_service_date', '>=', now()->subDays(30))
            ->count();

        $serviceDue = (clone $baseQuery)
            ->where(function($query) {
                $query->whereNull('pos_terminals.last_service_date')
                      ->orWhere('pos_terminals.last_service_date', '<=', now()->subDays(60));
            })
            ->count();

        // More granular service data
        $overdueService = (clone $baseQuery)
            ->where(function($query) {
                $query->whereNull('pos_terminals.last_service_date')
                      ->orWhere('pos_terminals.last_service_date', '<=', now()->subDays(90));
            })
            ->count();

        $neverServiced = (clone $baseQuery)
            ->whereNull('pos_terminals.last_service_date')
            ->count();

        // Installation statistics
        $recentInstallations = (clone $baseQuery)
            ->whereNotNull('pos_terminals.installation_date')
            ->where('pos_terminals.installation_date', '>=', now()->subDays(30))
            ->count();

        // Model distribution for alternative chart - FIX: Column qualification
        $modelDistribution = (clone $baseQuery)
            ->selectRaw('COALESCE(pos_terminals.terminal_model, "Unknown") as model, COUNT(*) as count')
            ->groupBy('pos_terminals.terminal_model')
            ->orderByDesc('count')
            ->limit(6)
            ->pluck('count', 'model')
            ->toArray();

        // Client distribution for alternative chart - FIX: No ambiguous columns
        $clientDistribution = [];
        try {
            $clientDistribution = (clone $baseQuery)
                ->join('clients', 'pos_terminals.client_id', '=', 'clients.id')
                ->selectRaw('clients.company_name, COUNT(pos_terminals.id) as count')
                ->groupBy('clients.id', 'clients.company_name')
                ->orderByDesc('count')
                ->limit(7)
                ->pluck('count', 'company_name')
                ->toArray();
        } catch (\Exception $e) {
            Log::warning('Error calculating client distribution: ' . $e->getMessage());
            // Fallback to simple client count
            $clientDistribution = ['Client Data' => $totalTerminals];
        }

        return [
            // Basic stats (for the 4 cards)
            'total_terminals' => $totalTerminals,
            'active_terminals' => $activeTerminals,
            'offline_terminals' => $offlineTerminals,
            'faulty_terminals' => $faultyTerminals,

            // Service timeline stats (for the new chart)
            'recently_serviced' => $recentlyServiced,
            'service_due' => $serviceDue,
            'overdue_service' => $overdueService,
            'never_serviced' => $neverServiced,

            // Additional useful stats
            'recent_installations' => $recentInstallations,
            'uptime_percentage' => $totalTerminals > 0 ? round(($activeTerminals / $totalTerminals) * 100, 1) : 0,

            // Chart data arrays
            'model_distribution' => $modelDistribution,
            'client_distribution' => $clientDistribution,
        ];
    }

    /**
     * Get chart data for AJAX requests
     */
    public function getChartData(Request $request)
    {
        try {
            $query = PosTerminal::query();

            // Apply same filters as index method with column qualification
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('pos_terminals.terminal_id', 'like', "%{$search}%")
                      ->orWhere('pos_terminals.merchant_name', 'like', "%{$search}%")
                      ->orWhere('pos_terminals.merchant_contact_person', 'like', "%{$search}%");
                });
            }

            if ($request->filled('client')) {
                $query->where('pos_terminals.client_id', $request->client);
            }

            if ($request->filled('status')) {
                $validStatuses = ['active', 'offline', 'faulty', 'maintenance'];
                if (in_array($request->status, $validStatuses)) {
                    $query->where('pos_terminals.status', $request->status);
                }
            }

            if ($request->filled('region')) {
                $query->where('pos_terminals.region', $request->region);
            }

            if ($request->filled('city')) {
                $query->where('pos_terminals.city', $request->city);
            }

            if ($request->filled('province')) {
                $query->where('pos_terminals.province', $request->province);
            }

            $stats = $this->calculateFilteredStats(clone $query);

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'chartData' => [
                    'stats' => $stats,
                    'serviceDue' => [
                        'recentlyServiced' => $stats['recently_serviced'],
                        'serviceDueSoon' => max(0, $stats['service_due'] - $stats['overdue_service']),
                        'overdueService' => $stats['overdue_service'],
                        'neverServiced' => $stats['never_serviced']
                    ],
                    'clientDistribution' => $stats['client_distribution'],
                    'modelDistribution' => $stats['model_distribution']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Chart data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading chart data: ' . $e->getMessage()
            ], 500);
        }
    }
}
