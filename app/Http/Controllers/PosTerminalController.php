<?php

// ==============================================
// 2. POS TERMINAL CONTROLLER
// File: app/Http/Controllers/PosTerminalController.php
// ==============================================

namespace App\Http\Controllers;

use App\Models\PosTerminal;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosTerminalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active.employee']);
        $this->middleware('permission:view_terminals,manage_team,all')->only(['index', 'show']);
        $this->middleware('permission:update_terminals,manage_team,all')->only(['create', 'store', 'edit', 'update']);
        $this->middleware('permission:all')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = PosTerminal::with(['client']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('terminal_id', 'like', "%{$search}%")
                  ->orWhere('merchant_name', 'like', "%{$search}%")
                  ->orWhere('merchant_contact_person', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Client filter
        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Region filter
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        $terminals = $query->latest()->paginate(20);
        
        // Get data for filters
        $clients = Client::active()->orderBy('company_name')->get();
        $regions = PosTerminal::select('region')->distinct()->whereNotNull('region')->orderBy('region')->pluck('region');
        
        // Statistics
        $stats = [
            'total_terminals' => PosTerminal::count(),
            'active_terminals' => PosTerminal::active()->count(),
            'offline_terminals' => PosTerminal::offline()->count(),
            'faulty_terminals' => PosTerminal::faulty()->count(),
        ];

        return view('pos-terminals.index', compact('terminals', 'clients', 'regions', 'stats'));
    }

    public function show(PosTerminal $posTerminal)
    {
        $posTerminal->load(['client', 'jobAssignments.technician.employee', 'serviceReports', 'tickets']);
        
        $recentActivity = collect()
            ->merge($posTerminal->jobAssignments->take(5))
            ->merge($posTerminal->tickets->take(5))
            ->sortByDesc('created_at')
            ->take(10);

        return view('pos-terminals.show', compact('posTerminal', 'recentActivity'));
    }

    public function create()
    {
        $clients = Client::active()->orderBy('company_name')->get();
        $regions = ['North', 'South', 'East', 'West', 'Central'];
        
        return view('pos-terminals.create', compact('clients', 'regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id' => 'required|string|unique:pos_terminals,terminal_id|max:50',
            'client_id' => 'required|exists:clients,id',
            'merchant_name' => 'required|string|max:255',
            'merchant_contact_person' => 'nullable|string|max:255',
            'merchant_phone' => 'nullable|string|max:20',
            'merchant_email' => 'nullable|email|max:255',
            'physical_address' => 'nullable|string',
            'region' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'business_type' => 'nullable|string|max:100',
            'installation_date' => 'nullable|date',
            'terminal_model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'contract_details' => 'nullable|string',
        ]);

        $terminal = PosTerminal::create($validated);

        return redirect()->route('pos-terminals.show', $terminal)
            ->with('success', 'POS Terminal created successfully.');
    }

    public function edit(PosTerminal $posTerminal)
    {
        $clients = Client::active()->orderBy('company_name')->get();
        $regions = ['North', 'South', 'East', 'West', 'Central'];
        
        return view('pos-terminals.edit', compact('posTerminal', 'clients', 'regions'));
    }

    public function update(Request $request, PosTerminal $posTerminal)
    {
        $validated = $request->validate([
            'terminal_id' => 'required|string|max:50|unique:pos_terminals,terminal_id,' . $posTerminal->id,
            'client_id' => 'required|exists:clients,id',
            'merchant_name' => 'required|string|max:255',
            'merchant_contact_person' => 'nullable|string|max:255',
            'merchant_phone' => 'nullable|string|max:20',
            'merchant_email' => 'nullable|email|max:255',
            'physical_address' => 'nullable|string',
            'region' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'business_type' => 'nullable|string|max:100',
            'installation_date' => 'nullable|date',
            'terminal_model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'contract_details' => 'nullable|string',
            'status' => 'required|in:active,offline,maintenance,faulty,decommissioned',
            'last_service_date' => 'nullable|date',
            'next_service_due' => 'nullable|date',
        ]);

        $posTerminal->update($validated);

        return redirect()->route('pos-terminals.show', $posTerminal)
            ->with('success', 'POS Terminal updated successfully.');
    }

    public function updateStatus(Request $request, PosTerminal $posTerminal)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,offline,maintenance,faulty,decommissioned',
            'notes' => 'nullable|string|max:500',
        ]);

        $posTerminal->update([
            'status' => $validated['status'],
            'last_service_date' => now(),
        ]);

        // Log this activity (we'll implement activity logging later)
        
        return back()->with('success', 'Terminal status updated successfully.');
    }

    public function destroy(PosTerminal $posTerminal)
    {
        if ($posTerminal->jobAssignments()->count() > 0) {
            return back()->with('error', 'Cannot delete terminal with existing job assignments.');
        }

        $posTerminal->delete();

        return redirect()->route('pos-terminals.index')
            ->with('success', 'POS Terminal deleted successfully.');
    }

    // Import functionality
    public function showImport()
    {
        return view('pos-terminals.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
            'client_id' => 'required|exists:clients,id',
        ]);

        try {
            DB::beginTransaction();

            // Here we would implement the actual import logic
            // For now, we'll just simulate it
            
            $importedCount = 0;
            $errorCount = 0;
            
            // TODO: Implement actual CSV/Excel parsing and import
            
            DB::commit();

            return redirect()->route('pos-terminals.index')
                ->with('success', "Import completed. {$importedCount} terminals imported, {$errorCount} errors.");

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->with('error', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }
}