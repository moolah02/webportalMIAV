<?php

// ============================================
// CLIENT API CONTROLLER
// ============================================

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * Get paginated list of clients
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $clients = $query->withCount('posTerminals')
                        ->orderBy('company_name')
                        ->paginate($request->get('per_page', 15));

        $clients->getCollection()->transform(function($client) {
            return [
                'id' => $client->id,
                'company_name' => $client->company_name,
                'contact_person' => $client->contact_person,
                'phone' => $client->phone,
                'email' => $client->email,
                'address' => $client->address,
                'status' => $client->status,
                'contract_start_date' => $client->contract_start_date,
                'contract_end_date' => $client->contract_end_date,
                'terminals_count' => $client->pos_terminals_count,
                'created_at' => $client->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'clients' => $clients->items(),
                'pagination' => [
                    'current_page' => $clients->currentPage(),
                    'last_page' => $clients->lastPage(),
                    'per_page' => $clients->perPage(),
                    'total' => $clients->total(),
                ]
            ]
        ]);
    }

    /**
     * Get single client details
     */
    public function show($id)
    {
        $client = Client::withCount('posTerminals')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'client' => [
                    'id' => $client->id,
                    'company_name' => $client->company_name,
                    'contact_person' => $client->contact_person,
                    'phone' => $client->phone,
                    'email' => $client->email,
                    'address' => $client->address,
                    'status' => $client->status,
                    'contract_start_date' => $client->contract_start_date,
                    'contract_end_date' => $client->contract_end_date,
                    'terminals_count' => $client->pos_terminals_count,
                    'created_at' => $client->created_at,
                ]
            ]
        ]);
    }

    /**
     * Get client's terminals
     */
    public function getTerminals($id)
    {
        $client = Client::findOrFail($id);
        
        $terminals = $client->posTerminals()
                           ->select('id', 'terminal_id', 'merchant_name', 'status', 'region', 'city', 'physical_address')
                           ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'client' => [
                    'id' => $client->id,
                    'name' => $client->company_name,
                ],
                'terminals' => $terminals,
                'count' => $terminals->count(),
                'stats' => [
                    'active' => $terminals->where('status', 'active')->count(),
                    'offline' => $terminals->where('status', 'offline')->count(),
                    'faulty' => $terminals->whereIn('status', ['faulty', 'maintenance'])->count(),
                ]
            ]
        ]);
    }

    /**
     * Get client's projects (if applicable)
     */
    public function getProjects($id)
    {
        $client = Client::findOrFail($id);
        
        $projects = [];
        try {
            if (method_exists($client, 'projects')) {
                $projects = $client->projects()->get();
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load client projects: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data' => [
                'client' => [
                    'id' => $client->id,
                    'name' => $client->company_name,
                ],
                'projects' => $projects,
                'count' => count($projects)
            ]
        ]);
    }
}