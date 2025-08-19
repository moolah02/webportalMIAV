<?php

// ============================================
// TICKET API CONTROLLER
// ============================================

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Ticket;
use App\Models\PosTerminal;

class TicketController extends Controller
{
    /**
     * Get paginated list of tickets
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Ticket::with(['technician:id,first_name,last_name', 'posTerminal:id,terminal_id,merchant_name', 'client:id,company_name']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('issue_type')) {
            $query->where('issue_type', $request->issue_type);
        }

        if ($request->filled('assigned_to_me') && $user->isFieldTechnician()) {
            $query->where('assigned_to', $user->id);
        }

        if ($request->filled('terminal_id')) {
            $query->where('pos_terminal_id', $request->terminal_id);
        }

        $tickets = $query->latest()->paginate($request->get('per_page', 15));

        $tickets->getCollection()->transform(function($ticket) {
            return [
                'id' => $ticket->id,
                'ticket_id' => $ticket->ticket_id,
                'title' => $ticket->title,
                'description' => $ticket->description,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'issue_type' => $ticket->issue_type,
                'estimated_resolution_time' => $ticket->estimated_resolution_time,
                'terminal' => $ticket->posTerminal ? [
                    'id' => $ticket->posTerminal->id,
                    'terminal_id' => $ticket->posTerminal->terminal_id,
                    'merchant_name' => $ticket->posTerminal->merchant_name,
                ] : null,
                'client' => $ticket->client ? [
                    'id' => $ticket->client->id,
                    'name' => $ticket->client->company_name,
                ] : null,
                'technician' => $ticket->technician ? [
                    'id' => $ticket->technician->id,
                    'name' => $ticket->technician->first_name . ' ' . $ticket->technician->last_name,
                ] : null,
                'is_overdue' => $ticket->isOverdue(),
                'created_at' => $ticket->created_at,
                'resolved_at' => $ticket->resolved_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'tickets' => $tickets->items(),
                'pagination' => [
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage(),
                    'per_page' => $tickets->perPage(),
                    'total' => $tickets->total(),
                ]
            ]
        ]);
    }

    /**
     * Create new ticket
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pos_terminal_id' => 'required|exists:pos_terminals,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'issue_type' => 'required|string',
            'estimated_resolution_time' => 'sometimes|integer|min:1',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        try {
            // Handle attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('ticket-attachments', 'public');
                    $attachments[] = [
                        'path' => $path,
                        'url' => asset('storage/' . $path),
                        'name' => $file->getClientOriginalName(),
                        'uploaded_at' => now()->toISOString()
                    ];
                }
            }

            $terminal = PosTerminal::findOrFail($request->pos_terminal_id);

            $ticket = Ticket::create([
                'technician_id' => $user->id,
                'pos_terminal_id' => $request->pos_terminal_id,
                'client_id' => $terminal->client_id,
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'issue_type' => $request->issue_type,
                'estimated_resolution_time' => $request->estimated_resolution_time,
                'status' => 'open',
                'attachments' => $attachments,
                'mobile_created' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
                'data' => [
                    'ticket' => [
                        'id' => $ticket->id,
                        'ticket_id' => $ticket->ticket_id,
                        'status' => $ticket->status,
                        'priority' => $ticket->priority,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Ticket creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket'
            ], 500);
        }
    }

    /**
     * Show specific ticket
     */
    public function show($id)
    {
        $ticket = Ticket::with([
            'technician:id,first_name,last_name',
            'posTerminal:id,terminal_id,merchant_name,physical_address',
            'client:id,company_name,contact_person,phone'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'ticket' => [
                    'id' => $ticket->id,
                    'ticket_id' => $ticket->ticket_id,
                    'title' => $ticket->title,
                    'description' => $ticket->description,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'issue_type' => $ticket->issue_type,
                    'resolution' => $ticket->resolution,
                    'estimated_resolution_time' => $ticket->estimated_resolution_time,
                    'terminal' => $ticket->posTerminal ? [
                        'id' => $ticket->posTerminal->id,
                        'terminal_id' => $ticket->posTerminal->terminal_id,
                        'merchant_name' => $ticket->posTerminal->merchant_name,
                        'address' => $ticket->posTerminal->physical_address,
                    ] : null,
                    'client' => $ticket->client ? [
                        'id' => $ticket->client->id,
                        'name' => $ticket->client->company_name,
                        'contact_person' => $ticket->client->contact_person,
                        'phone' => $ticket->client->phone,
                    ] : null,
                    'technician' => $ticket->technician ? [
                        'id' => $ticket->technician->id,
                        'name' => $ticket->technician->first_name . ' ' . $ticket->technician->last_name,
                    ] : null,
                    'attachments' => $ticket->attachments ?? [],
                    'is_overdue' => $ticket->isOverdue(),
                    'created_at' => $ticket->created_at,
                    'resolved_at' => $ticket->resolved_at,
                ]
            ]
        ]);
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,in_progress,resolved,closed',
            'resolution' => 'required_if:status,resolved|string',
            'notes' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket = Ticket::findOrFail($id);
        $user = $request->user();

        try {
            $updateData = ['status' => $request->status];

            if ($request->status === 'resolved') {
                $updateData['resolution'] = $request->resolution;
                $updateData['resolved_at'] = now();
                $updateData['assigned_to'] = $user->id;
            }

            $ticket->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Ticket status updated successfully',
                'data' => [
                    'ticket_id' => $ticket->ticket_id,
                    'new_status' => $ticket->status,
                    'resolved_at' => $ticket->resolved_at,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Ticket status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket status'
            ], 500);
        }
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket = Ticket::findOrFail($id);
        $user = $request->user();

        try {
            // Add comment to ticket (you might want to create a TicketComment model)
            $commentData = [
                'user_id' => $user->id,
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'comment' => $request->comment,
                'timestamp' => now()->toISOString(),
            ];

            // For now, store in JSON field or log it
            \Log::info("Ticket {$ticket->ticket_id} comment", $commentData);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => [
                    'comment' => $commentData
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Adding comment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment'
            ], 500);
        }
    }

    /**
     * Get my tickets (for technicians)
     */
    public function myTickets(Request $request)
    {
        $user = $request->user();

        $query = Ticket::with(['posTerminal:id,terminal_id,merchant_name', 'client:id,company_name'])
                      ->where(function($q) use ($user) {
                          $q->where('technician_id', $user->id)
                            ->orWhere('assigned_to', $user->id);
                      });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->get()->map(function($ticket) {
            return [
                'id' => $ticket->id,
                'ticket_id' => $ticket->ticket_id,
                'title' => $ticket->title,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'issue_type' => $ticket->issue_type,
                'terminal' => $ticket->posTerminal ? [
                    'terminal_id' => $ticket->posTerminal->terminal_id,
                    'merchant_name' => $ticket->posTerminal->merchant_name,
                ] : null,
                'client_name' => $ticket->client->company_name ?? 'Unknown',
                'is_overdue' => $ticket->isOverdue(),
                'created_at' => $ticket->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'tickets' => $tickets,
                'summary' => [
                    'total' => $tickets->count(),
                    'open' => $tickets->where('status', 'open')->count(),
                    'in_progress' => $tickets->where('status', 'in_progress')->count(),
                    'overdue' => $tickets->where('is_overdue', true)->count(),
                ]
            ]
        ]);
    }
}