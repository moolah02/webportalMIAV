<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Ticket;
use App\Models\PosTerminal;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class TicketController extends Controller
{
    /**
     * GET /tickets  (no pagination)
     * Scoped to logged-in user:
     * - Field technician: tickets where assigned_to = me OR technician_id = me
     * - Client user: tickets where client_id = my client_id
     * - Admin/staff: all
     */
    public function index(Request $request)
    {
        $user  = $request->user();

        $query = Ticket::with([
            'technician:id,first_name,last_name',
            'posTerminal:id,terminal_id,merchant_name',
            'client:id,company_name'
        ]);

        // Scope by role
        if (method_exists($user, 'isFieldTechnician') && $user->isFieldTechnician()) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('technician_id', $user->id);
            });
        } elseif (method_exists($user, 'isClientUser') && $user->isClientUser()) {
            if (!empty($user->client_id)) {
                $query->where('client_id', $user->client_id);
            } else {
                $query->whereRaw('1 = 0'); // no client scoped
            }
        } else {
            // admins/staff: see all
        }

        // Optional filters
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('issue_type')) $query->where('issue_type', $request->issue_type);
        if ($request->filled('terminal_id')) $query->where('pos_terminal_id', $request->terminal_id);

        $tickets = $query->latest()->get()->map(fn ($ticket) => $this->mapTicketRow($ticket));

        return response()->json([
            'success' => true,
            'data'    => ['tickets' => $tickets],
        ]);
    }

    /**
     * POST /tickets
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'issue_type'   => 'required|string|max:100',
            'priority'     => ['required', Rule::in(['low','medium','high','urgent'])],
            'pos_terminal_id' => 'nullable|exists:pos_terminals,id',
            'estimated_resolution_time' => 'nullable|integer|min:0',
            'attachments'  => 'nullable', // json or array, depending on how you send it
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()
            ], 422);
        }

        // Derive client_id from terminal if provided
        $clientId = null;
        if ($request->filled('pos_terminal_id')) {
            $terminal = PosTerminal::select('id','client_id')->find($request->pos_terminal_id);
            $clientId = $terminal?->client_id;
        } elseif (property_exists($user, 'client_id')) {
            $clientId = $user->client_id;
        }

        $ticket = new Ticket();
        $ticket->ticket_id = $this->generateTicketId();
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->issue_type = $request->issue_type;
        $ticket->priority = $request->priority;
        $ticket->status = 'open';
        $ticket->estimated_resolution_time = $request->estimated_resolution_time;
        $ticket->pos_terminal_id = $request->pos_terminal_id;
        $ticket->client_id = $clientId;
        $user = $request->user();

// If the creator is a field tech, auto-assign:
if (method_exists($user, 'isFieldTechnician') && $user->isFieldTechnician()) {
    $ticket->technician_id = $user->id;
    $ticket->assigned_to   = $user->id;
}

        // Optionally attribute creator:
        if (Schema::hasColumn('tickets', 'created_by')) {
            $ticket->created_by = $user->id;
        }
        // Attachments handling (json)
        if ($request->filled('attachments')) {
            $ticket->attachments = is_string($request->attachments)
                ? $request->attachments
                : json_encode($request->attachments);
        }

        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Ticket created',
            'data'    => ['ticket' => $this->mapTicketDetail($ticket->fresh(['technician','posTerminal','client']))],
        ], 201);
    }

    /**
     * GET /tickets/{ticket}
     */
    public function show(Request $request, Ticket $ticket)
    {
        $this->authorizeView($request->user(), $ticket);

        $ticket->load([
            'technician:id,first_name,last_name',
            'posTerminal:id,terminal_id,merchant_name,physical_address',
            'client:id,company_name,contact_person,phone'
        ]);

        return response()->json([
            'success' => true,
            'data' => ['ticket' => $this->mapTicketDetail($ticket)],
        ]);
    }

    /**
     * PUT /tickets/{ticket}
     */
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorizeUpdate($request->user(), $ticket);

        $validator = Validator::make($request->all(), [
            'title'        => 'sometimes|required|string|max:255',
            'description'  => 'nullable|string',
            'issue_type'   => 'sometimes|required|string|max:100',
            'priority'     => ['sometimes','required', Rule::in(['low','medium','high','urgent'])],
            'pos_terminal_id' => 'nullable|exists:pos_terminals,id',
            'estimated_resolution_time' => 'nullable|integer|min:0',
            'attachments'  => 'nullable',
            'assigned_to'  => 'nullable|integer|exists:employees,id',
            'technician_id'=> 'nullable|integer|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()
            ], 422);
        }

        $ticket->fill($request->only([
            'title','description','issue_type','priority','pos_terminal_id',
            'estimated_resolution_time','assigned_to','technician_id'
        ]));

        if ($request->filled('attachments')) {
            $ticket->attachments = is_string($request->attachments)
                ? $request->attachments
                : json_encode($request->attachments);
        }

        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Ticket updated',
            'data'    => ['ticket' => $this->mapTicketDetail($ticket->fresh(['technician','posTerminal','client']))],
        ]);
    }

    /**
     * DELETE /tickets/{ticket}
     */
    public function destroy(Request $request, Ticket $ticket)
    {
        $this->authorizeDelete($request->user(), $ticket);

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted',
        ]);
    }

    /**
     * PATCH /tickets/{ticket}/status
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorizeUpdate($request->user(), $ticket);

        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(['open','in_progress','pending','resolved','closed','cancelled'])],
            'resolution' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()
            ], 422);
        }

        $old = $ticket->status;
        $ticket->status = $request->status;

        if ($request->filled('resolution')) {
            $ticket->resolution = $request->resolution;
        }

        // Set resolved_at when transitioning to resolved/closed
        if (in_array($ticket->status, ['resolved','closed']) && is_null($ticket->resolved_at)) {
            $ticket->resolved_at = now();
        }

        $ticket->save();

        // Lightweight history record if you have a table; otherwise no-op
        if (Schema::hasTable('ticket_status_histories')) {
            DB::table('ticket_status_histories')->insert([
                'ticket_id' => $ticket->id,
                'from' => $old,
                'to' => $ticket->status,
                'changed_by' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
            'data'    => ['ticket' => $this->mapTicketDetail($ticket->fresh(['technician','posTerminal','client']))],
        ]);
    }

    /**
     * POST /tickets/{ticket}/comments
     * Works even if you don't yet have a comments table (returns the comment you sent).
     * If you DO have a table `ticket_comments`, it will persist.
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $this->authorizeView($request->user(), $ticket);

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $payload = [
            'user_id'   => $user->id,
            'user_name' => trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: ($user->name ?? 'User'),
            'comment'   => $request->comment,
            'timestamp' => now()->toISOString(),
        ];

        // If you have a comments table, persist:
        if (Schema::hasTable('ticket_comments')) {
            DB::table('ticket_comments')->insert([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'comment'   => $request->comment,
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data'    => ['comment' => $payload],
        ]);
    }

    /**
     * GET /tickets/{ticket}/history
     * If you have `ticket_status_histories`, we’ll return those; else a minimal fallback.
     */
    public function getHistory(Request $request, Ticket $ticket)
    {
        $this->authorizeView($request->user(), $ticket);

        $history = [];

        if (Schema::hasTable('ticket_status_histories')) {
            $history = DB::table('ticket_status_histories')
                ->where('ticket_id', $ticket->id)
                ->orderByDesc('id')
                ->get()
                ->map(function ($row) {
                    return [
                        'from'       => $row->from,
                        'to'         => $row->to,
                        'changed_by' => $row->changed_by,
                        'changed_at' => optional($row->created_at)->toISOString() ?? (string)$row->created_at,
                    ];
                })->values();
        } else {
            // Fallback minimal history
            $history = [[
                'from'       => null,
                'to'         => $ticket->status,
                'changed_by' => null,
                'changed_at' => optional($ticket->updated_at)->toISOString(),
            ]];
        }

        return response()->json([
            'success' => true,
            'data'    => ['history' => $history],
        ]);
    }

    /**
     * Your original myTickets endpoint (kept, behind /tickets/mine/list)
     */
    public function myTickets(Request $request)
    {
        $user = $request->user();

        $query = Ticket::with(['posTerminal:id,terminal_id,merchant_name', 'client:id,company_name'])
            ->where(function($q) use ($user) {
                $q->where('technician_id', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });

        if ($request->filled('status')) $query->where('status', $request->status);

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

    /* ----------------- helpers & policies ----------------- */

    private function mapTicketRow(Ticket $t): array
    {
        return [
            'id' => $t->id,
            'ticket_id' => $t->ticket_id,
            'title' => $t->title,
            'description' => $t->description,
            'status' => $t->status,
            'priority' => $t->priority,
            'issue_type' => $t->issue_type,
            'estimated_resolution_time' => $t->estimated_resolution_time,
            'terminal' => $t->posTerminal ? [
                'id' => $t->posTerminal->id,
                'terminal_id' => $t->posTerminal->terminal_id,
                'merchant_name' => $t->posTerminal->merchant_name,
            ] : null,
            'client' => $t->client ? [
                'id' => $t->client->id,
                'name' => $t->client->company_name,
            ] : null,
            'technician' => $t->technician ? [
                'id' => $t->technician->id,
                'name' => $t->technician->first_name . ' ' . $t->technician->last_name,
            ] : null,
            'is_overdue' => $t->isOverdue(),
            'created_at' => $t->created_at,
            'resolved_at' => $t->resolved_at,
        ];
    }

    private function mapTicketDetail(Ticket $t): array
    {
        return [
            'id' => $t->id,
            'ticket_id' => $t->ticket_id,
            'title' => $t->title,
            'description' => $t->description,
            'status' => $t->status,
            'priority' => $t->priority,
            'issue_type' => $t->issue_type,
            'resolution' => $t->resolution,
            'estimated_resolution_time' => $t->estimated_resolution_time,
            'terminal' => $t->posTerminal ? [
                'id' => $t->posTerminal->id,
                'terminal_id' => $t->posTerminal->terminal_id,
                'merchant_name' => $t->posTerminal->merchant_name,
                'address' => $t->posTerminal->physical_address ?? null,
            ] : null,
            'client' => $t->client ? [
                'id' => $t->client->id,
                'name' => $t->client->company_name,
                'contact_person' => $t->client->contact_person ?? null,
                'phone' => $t->client->phone ?? null,
            ] : null,
            'technician' => $t->technician ? [
                'id' => $t->technician->id,
                'name' => $t->technician->first_name . ' ' . $t->technician->last_name,
            ] : null,
            'attachments' => $this->decodeJson($t->attachments),
            'is_overdue' => $t->isOverdue(),
            'created_at' => $t->created_at,
            'resolved_at' => $t->resolved_at,
        ];
    }

    private function decodeJson($value)
    {
        if (is_null($value)) return [];
        if (is_array($value)) return $value;
        $decoded = json_decode($value, true);
        return $decoded ?? [];
    }

    private function generateTicketId(): string
    {
        // Example: TCK-2025-ABC123
        return 'TCK-'.date('Y').'-'.strtoupper(Str::random(6));
    }

    private function authorizeView($user, Ticket $ticket): void
    {
        if (method_exists($user, 'isFieldTechnician') && $user->isFieldTechnician()) {
            if ($ticket->assigned_to !== $user->id && $ticket->technician_id !== $user->id) {
                abort(403, 'Not allowed to view this ticket.');
            }
        } elseif (method_exists($user, 'isClientUser') && $user->isClientUser()) {
            if (!empty($user->client_id) && $ticket->client_id !== $user->client_id) {
                abort(403, 'Not allowed to view this ticket.');
            }
        } else {
            // admins/staff allowed
        }
    }

    private function authorizeUpdate($user, Ticket $ticket): void
    {
        // Adjust to your policy. For now: techs can update their assigned tickets; clients cannot; admins/staff can.
        if (method_exists($user, 'isFieldTechnician') && $user->isFieldTechnician()) {
            if ($ticket->assigned_to !== $user->id && $ticket->technician_id !== $user->id) {
                abort(403, 'Not allowed to update this ticket.');
            }
        } elseif (method_exists($user, 'isClientUser') && $user->isClientUser()) {
            abort(403, 'Clients cannot update tickets.');
        }
    }

    private function authorizeDelete($user, Ticket $ticket): void
    {
        // Usually only admins/staff. Adjust as needed.
        if (!(method_exists($user, 'isAdmin') && $user->isAdmin())) {
            abort(403, 'Not allowed to delete this ticket.');
        }
    }
}
