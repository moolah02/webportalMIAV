<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Employee;
use App\Models\PosTerminal;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        // Get tickets with relationships
        $query = Ticket::with(['assignedTo', 'posTerminal', 'technician', 'client']);

        // Apply filters if provided
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('issue_type')) {
            $query->byIssueType($request->issue_type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('ticket_id', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->latest()->get();

        // Calculate statistics
        $stats = [
            'open' => Ticket::open()->count(),
            'in_progress' => Ticket::inProgress()->count(),
            'resolved' => Ticket::resolved()->whereMonth('resolved_at', now())->count(),
            'critical' => Ticket::critical()->count(),
        ];

        // Get data for dropdowns
        $posTerminals = PosTerminal::select('id', 'terminal_id', 'merchant_name')
            ->orderBy('terminal_id')
            ->get();

        $technicians = Employee::whereHas('role', function($query) {
            $query->where('name', 'Technician');
        })->select('id', 'first_name', 'last_name')
        ->orderBy('first_name')
        ->get();

        return view('tickets.index', compact('tickets', 'stats', 'posTerminals', 'technicians'));
    }

    public function create()
    {
        $posTerminals = PosTerminal::select('id', 'terminal_id', 'merchant_name')
            ->orderBy('terminal_id')
            ->get();

        $technicians = Employee::whereHas('role', function($query) {
            $query->where('name', 'Technician');
        })->select('id', 'first_name', 'last_name')
        ->orderBy('first_name')
        ->get();

        $clients = Client::select('id', 'company_name')
            ->where('status', 'active')
            ->orderBy('company_name')
            ->get();

        return view('tickets.create', compact('posTerminals', 'technicians', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'issue_type' => 'required|in:hardware_malfunction,software_issue,network_connectivity,user_training,maintenance_required,replacement_needed,other',
            'pos_terminal_id' => 'nullable|exists:pos_terminals,id',
            'assigned_to' => 'nullable|exists:employees,id',
            'estimated_resolution_time' => 'nullable|integer|min:1',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        // Set the technician_id to the currently logged in user
        $validated['technician_id'] = Auth::id();
        
        // Set default status
        $validated['status'] = 'open';

        $ticket = Ticket::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Ticket created successfully',
                'ticket' => $ticket->load(['assignedTo', 'posTerminal', 'technician'])
            ], 201);
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['assignedTo', 'posTerminal', 'technician', 'client', 'visit']);

        if (request()->expectsJson()) {
            return response()->json($ticket);
        }

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $posTerminals = PosTerminal::select('id', 'terminal_id', 'merchant_name')
            ->orderBy('terminal_id')
            ->get();

        $technicians = Employee::whereHas('role', function($query) {
            $query->where('name', 'Technician');
        })->select('id', 'first_name', 'last_name')
        ->orderBy('first_name')
        ->get();

        $clients = Client::select('id', 'company_name')
            ->where('status', 'active')
            ->orderBy('company_name')
            ->get();

        return view('tickets.edit', compact('ticket', 'posTerminals', 'technicians', 'clients'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'issue_type' => 'required|in:hardware_malfunction,software_issue,network_connectivity,user_training,maintenance_required,replacement_needed,other',
            'pos_terminal_id' => 'nullable|exists:pos_terminals,id',
            'assigned_to' => 'nullable|exists:employees,id',
            'estimated_resolution_time' => 'nullable|integer|min:1',
            'resolution' => 'nullable|string',
            'status' => 'nullable|in:open,in_progress,resolved,closed,cancelled',
        ]);

        // Set resolved_at timestamp if status is being changed to resolved
        if (isset($validated['status']) && $validated['status'] === 'resolved' && $ticket->status !== 'resolved') {
            $validated['resolved_at'] = now();
        }

        $ticket->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Ticket updated successfully',
                'ticket' => $ticket->fresh()->load(['assignedTo', 'posTerminal', 'technician'])
            ]);
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully!');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Ticket deleted successfully']);
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed,cancelled',
            'resolution' => 'nullable|string'
        ]);

        // Set resolved_at timestamp if status is being changed to resolved
        if ($validated['status'] === 'resolved' && $ticket->status !== 'resolved') {
            $validated['resolved_at'] = now();
        }

        $ticket->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Ticket status updated successfully',
                'ticket' => $ticket->fresh()
            ]);
        }

        return redirect()->back()->with('success', 'Ticket status updated successfully!');
    }

    public function assignTicket(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:employees,id'
        ]);

        $ticket->update([
            'assigned_to' => $validated['assigned_to'],
            'status' => 'in_progress' // Automatically set to in progress when assigned
        ]);

        return response()->json([
            'message' => 'Ticket assigned successfully',
            'ticket' => $ticket->fresh()->load(['assignedTo'])
        ]);
    }

    // API endpoint for mobile app
    public function mobileIndex(Request $request)
    {
        $user = $request->user();
        
        // If user is technician, show only their tickets
        $query = Ticket::with(['assignedTo', 'posTerminal', 'technician']);
        
        if ($user->role->name === 'Technician') {
            $query->where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('technician_id', $user->id);
            });
        }

        $tickets = $query->latest()->get();

        return response()->json($tickets);
    }

    public function mobileStore(Request $request)
    {
       $request->validate([
        'technician_id' => 'required|exists:employees,id',
        'project_id' => 'nullable|exists:projects,id',
        'selected_terminals' => 'required|array|min:1',
        'selected_terminals.*' => 'integer|exists:pos_terminals,id',
        'scheduled_date' => 'required|date|after_or_equal:today',
        'service_type' => 'required|string|in:routine_maintenance,emergency_repair', // Use existing ENUM values
        'priority' => 'required|in:low,normal,high,emergency',
        'assignment_type' => 'required|in:individual,team',
        'notes' => 'nullable|string|max:1000'
    ]);

        $validated['technician_id'] = $request->user()->id;
        $validated['mobile_created'] = true;
        $validated['status'] = 'open';

        $ticket = Ticket::create($validated);

        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => $ticket->load(['posTerminal'])
        ], 201);
    }
}