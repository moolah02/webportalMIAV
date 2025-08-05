<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicianVisit;
use App\Models\Ticket;
use App\Models\AssetRequest;
use App\Models\PosTerminal;
use App\Models\JobAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MobileApiController extends Controller
{
    /**
     * Get technician dashboard data
     */
    public function getDashboard(Request $request)
    {
        $technicianId = $request->user()->id;

        $todayVisits = TechnicianVisit::where('technician_id', $technicianId)
            ->whereDate('visit_date', Carbon::today())
            ->count();

        $pendingAssignments = JobAssignment::where('technician_id', $technicianId)
            ->where('status', 'assigned')
            ->count();

        $recentActivity = TechnicianVisit::where('technician_id', $technicianId)
            ->with('posTerminal')
            ->latest('visit_date')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'today_visits'        => $todayVisits,
                'pending_assignments' => $pendingAssignments,
                'recent_activity'     => $recentActivity,
            ],
        ]);
    }

    /**
     * Get assigned sites/terminals for technician
     */
    public function getAssignedSites(Request $request)
    {
        $technicianId = $request->user()->id;
        $assignments  = JobAssignment::where('technician_id', $technicianId)
            ->where('status', 'assigned')
            ->with('region')
            ->get();

        $sites = [];
        foreach ($assignments as $assignment) {
            $terminals = PosTerminal::whereIn('id', $assignment->pos_terminals)
                ->with(['client', 'region', 'latestVisit'])
                ->get();

            foreach ($terminals as $terminal) {
                $sites[] = [
                    'id'             => $terminal->id,
                    'terminal_id'    => $terminal->terminal_id,
                    'merchant_name'  => $terminal->merchant_name,
                    'contact_person' => $terminal->merchant_contact_person,
                    'phone'          => $terminal->merchant_phone,
                    'address'        => $terminal->physical_address,
                    'region'         => $terminal->region->name ?? null,
                    'client'         => $terminal->client->name ?? null,
                    'current_status' => $terminal->current_status,
                    'last_visit'     => $terminal->latestVisit
                        ? [
                            'date'       => $terminal->latestVisit->visit_date,
                            'status'     => $terminal->latestVisit->terminal_status,
                            'technician' => $terminal->latestVisit->technician->name,
                        ]
                        : null,
                    'assignment'     => [
                        'id'             => $assignment->id,
                        'scheduled_date' => $assignment->scheduled_date,
                        'service_type'   => $assignment->service_type,
                        'priority'       => $assignment->priority,
                        'notes'          => $assignment->notes,
                    ],
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $sites,
        ]);
    }

    /**
     * Submit visit report
     */
    public function submitVisit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pos_terminal_id'     => 'required|exists:pos_terminals,id',
            'visit_date'          => 'required|date',
            'asset_type'          => 'required|in:pos_terminal,vehicle,it_equipment,furniture,other',
            'asset_id'            => 'nullable|string',
            'terminal_status'     => 'required|in:seen_working,seen_issues,not_seen,relocated,missing',
            'technician_feedback' => 'nullable|string',
            'comments'            => 'nullable|string',
            'duration_minutes'    => 'nullable|integer|min:1',
            'issues_found'        => 'nullable|array',
            'merchant_feedback'   => 'nullable|string',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'photos.*'            => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $photos = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $i => $photo) {
                    $filename = time() . "_{$i}." . $photo->getClientOriginalExtension();
                    $path     = $photo->storeAs('visit-photos', $filename, 'public');
                    $photos[] = [
                        'filename' => $filename,
                        'path'     => $path,
                        'caption'  => $request->input("photo_captions.{$i}"),
                    ];
                }
            }

            $visit = TechnicianVisit::create([
                'visit_id'            => TechnicianVisit::generateVisitId(),
                'technician_id'       => $request->user()->id,
                'pos_terminal_id'     => $request->pos_terminal_id,
                'client_id'           => PosTerminal::find($request->pos_terminal_id)->client_id,
                'visit_date'          => $request->visit_date,
                'asset_type'          => $request->asset_type,
                'asset_id'            => $request->asset_id,
                'terminal_status'     => $request->terminal_status,
                'technician_feedback' => $request->technician_feedback,
                'comments'            => $request->comments,
                'photos'              => $photos,
                'duration_minutes'    => $request->duration_minutes,
                'issues_found'        => $request->issues_found,
                'merchant_feedback'   => $request->merchant_feedback,
                'latitude'            => $request->latitude,
                'longitude'           => $request->longitude,
            ]);

            $terminal = PosTerminal::find($request->pos_terminal_id);
            $terminal->update([
                'last_service_date' => Carbon::parse($request->visit_date)->toDateString(),
                'current_status'    => $this->mapTerminalStatus($request->terminal_status),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visit submitted successfully',
                'data'    => ['visit_id' => $visit->visit_id, 'id' => $visit->id],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting visit: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create ticket
     */
    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pos_terminal_id' => 'nullable|exists:pos_terminals,id',
            'visit_id'        => 'nullable|exists:technician_visits,id',
            'issue_type'      => 'required|in:hardware_malfunction,software_issue,network_connectivity,user_training,maintenance_required,replacement_needed,other',
            'priority'        => 'required|in:critical,high,medium,low',
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'attachments.*'   => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $i => $file) {
                    $filename    = time() . "_{$i}." . $file->getClientOriginalExtension();
                    $path        = $file->storeAs('ticket-attachments', $filename, 'public');
                    $attachments[] = [
                        'filename'      => $filename,
                        'original_name' => $file->getClientOriginalName(),
                        'path'          => $path,
                        'size'          => $file->getSize(),
                    ];
                }
            }

            $ticket = Ticket::create([
                'ticket_id'      => Ticket::generateTicketId(),
                'technician_id'  => $request->user()->id,
                'pos_terminal_id'=> $request->pos_terminal_id,
                'visit_id'       => $request->visit_id,
                'client_id'      => $request->pos_terminal_id
                    ? PosTerminal::find($request->pos_terminal_id)->client_id
                    : null,
                'issue_type'     => $request->issue_type,
                'priority'       => $request->priority,
                'title'          => $request->title,
                'description'    => $request->description,
                'attachments'    => $attachments,
                'status'         => 'open',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
                'data'    => ['ticket_id' => $ticket->ticket_id, 'id' => $ticket->id],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating ticket: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit asset request
     */
    public function submitAssetRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_type'    => 'required|string',
            'priority'      => 'required|in:high,medium,low',
            'site_location' => 'nullable|string',
            'justification' => 'required|string',
            'required_date' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $assetRequest = AssetRequest::create([
                'requester_id'  => $request->user()->id,
                'asset_type'    => $request->asset_type,
                'priority'      => $request->priority,
                'site_location' => $request->site_location,
                'justification' => $request->justification,
                'required_date' => $request->required_date,
                'status'        => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Asset request submitted successfully',
                'data'    => ['request_id' => $assetRequest->id],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting asset request: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get technician's asset requests
     */
    public function getAssetRequests(Request $request)
    {
        $requests = AssetRequest::where('requester_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $requests,
        ]);
    }

    /**
     * Helper method to map terminal status
     */
    private function mapTerminalStatus($visitStatus)
    {
        $mapping = [
            'seen_working' => 'active',
            'seen_issues'  => 'faulty',
            'not_seen'     => 'offline',
            'relocated'    => 'offline',
            'missing'      => 'offline',
        ];

        return $mapping[$visitStatus] ?? 'offline';
    }

    /**
     * Get visit history for technician
     */
    public function getVisitHistory(Request $request)
    {
        $visits = TechnicianVisit::where('technician_id', $request->user()->id)
            ->with([
                'posTerminal:id,terminal_id,merchant_name',
                'posTerminal.region:id,name'
            ])
            ->orderBy('visit_date', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $visits,
        ]);
    }

    /**
     * Get analytics data for technician
     */
    public function getAnalytics(Request $request)
    {
        $technicianId = $request->user()->id;

        $stats = [
            'sites_covered_percent' => $this->calculateSitesCoveredPercent($technicianId),
            'total_visits'          => TechnicianVisit::where('technician_id', $technicianId)->count(),
            'issues_resolved'       => TechnicianVisit::where('technician_id', $technicianId)
                                        ->where('terminal_status', 'seen_working')->count(),
            'open_tickets'          => Ticket::where('technician_id', $technicianId)
                                        ->whereIn('status', ['open','in_progress'])->count(),
        ];

        return response()->json([
            'success' => true,
            'data'    => $stats,
        ]);
    }

    /**
     * Calculate sites covered percentage
     */
    private function calculateSitesCoveredPercent($technicianId)
    {
        $assignments      = JobAssignment::where('technician_id', $technicianId)
                                ->where('status', 'assigned')
                                ->get();
        $totalTerminals   = 0;
        $visitedTerminals = 0;

        foreach ($assignments as $assignment) {
            $terminals         = $assignment->pos_terminals;
            $totalTerminals   += count($terminals);
            $visited          = TechnicianVisit::where('technician_id', $technicianId)
                ->whereIn('pos_terminal_id', $terminals)
                ->whereDate('visit_date', '>=', $assignment->scheduled_date)
                ->distinct('pos_terminal_id')
                ->count();
            $visitedTerminals += $visited;
        }

        return $totalTerminals > 0
            ? round(($visitedTerminals / $totalTerminals) * 100)
            : 0;
    }
}
