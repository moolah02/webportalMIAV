<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Client Management Dashboard
    public function index(Request $request)
    {
        $query = Client::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('client_code', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Region filter
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        $clients = $query->latest()->paginate(15);
        
        // Get regions for filter dropdown
        $regions = Client::select('region')
            ->distinct()
            ->whereNotNull('region')
            ->orderBy('region')
            ->pluck('region');

        // Statistics based on your actual columns
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', 'active')->count(),
            'prospects' => Client::where('status', 'prospect')->count(),
            'under_contract' => Client::whereNotNull('contract_start_date')
                ->where('contract_end_date', '>=', now())->count(),
        ];

        return view('clients.index', compact('clients', 'regions', 'stats'));
    }

    // Create new client form
    public function create()
    {
        return view('clients.create');
    }

    // Store new client
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
        ]);

        try {
            // Generate client code if not provided
            $clientCode = $this->generateClientCode($request->company_name);

            Client::create([
                'client_code' => $clientCode,
                'company_name' => $request->company_name,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'region' => $request->region,
                'status' => $request->status,
                'contract_start_date' => $request->contract_start_date,
                'contract_end_date' => $request->contract_end_date,
            ]);

            return redirect()->route('clients.index')
                ->with('success', 'Client created successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to create client. Please try again.')
                ->withInput();
        }
    }

    // View single client
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    // Edit client form
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    // Update client
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
        ]);

        try {
            $client->update([
                'company_name' => $request->company_name,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'region' => $request->region,
                'status' => $request->status,
                'contract_start_date' => $request->contract_start_date,
                'contract_end_date' => $request->contract_end_date,
            ]);

            return redirect()->route('clients.index')
                ->with('success', 'Client updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update client. Please try again.')
                ->withInput();
        }
    }

    // Delete client
    public function destroy(Client $client)
    {
        try {
            $client->delete();

            return redirect()->route('clients.index')
                ->with('success', 'Client deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete client. Please try again.');
        }
    }

    // Generate unique client code
    private function generateClientCode($companyName)
    {
        // Take first 3 letters of company name + random number
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $companyName), 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }
        
        do {
            $code = $prefix . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Client::where('client_code', $code)->exists());
        
        return $code;
    }
}