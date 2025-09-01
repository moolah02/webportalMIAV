<?php

namespace App\Http\Controllers;

use App\Models\TechnicianVisit;
use App\Models\TechnicianVisitAttachment;
use App\Models\JobAssignment;
use App\Models\Employee;
use App\Models\PosTerminal;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class SiteVisitController extends Controller
{
    /**
     * Page: Site Visits (top shows job details, table of terminals).
     * Accepts ?assignment_id=#
     */
    public function index(Request $request)
    {
        // Optional — shown in “Recent Visits” or elsewhere if you later re-add it
        $visits = TechnicianVisit::with(['technician', 'posTerminal.client'])
            ->latest('started_at')
            ->paginate(20);

        // Useful for dropdowns/search if you re-add them to the page
        $technicians = Employee::active()
            ->fieldTechnicians()
            ->orderBy('first_name')
            ->get(['id','first_name','last_name']);

        $clients = Client::orderBy('company_name')->get(['id','company_name']);

        // What the Blade needs for header + table
        $prefillAssignment = null;
        $terminals = collect();

        if ($request->assignment_id) {
            $prefillAssignment = JobAssignment::with([
                'technician:id,first_name,last_name',
                'client:id,company_name',
                'project:id,project_name',
            ])->find($request->assignment_id);

            if ($prefillAssignment && is_array($prefillAssignment->pos_terminals)) {
                $terminals = PosTerminal::whereIn('id', $prefillAssignment->pos_terminals)
                    ->get();
            }
        }

        return view('deployment.site-visit', [
            'prefillAssignment' => $prefillAssignment,
            'terminals'         => $terminals,
            // keep these available if the Blade wants them later
            'visits'            => $visits,
            'technicians'       => $technicians,
            'clients'           => $clients,
        ]);
    }

    /**
     * Lookup a terminal by numeric ID.
     * GET /site-visits/lookup/terminal/{id}
     */
    public function terminalLookup($id)
    {
        $t = PosTerminal::with('client:id,company_name')->findOrFail($id);

        return response()->json([
            'id'             => $t->id,
            'terminal_id'    => $t->terminal_id,
            'merchant_id'    => $t->merchant_id,
            'merchant_name'  => $t->merchant_name,
            'client_id'      => $t->client_id,
            'client_name'    => $t->client?->company_name,
            'terminal_model' => $t->terminal_model,
            'serial_number'  => $t->serial_number,
            'address'        => $t->physical_address,
            'city'           => $t->city,
            'province'       => $t->province,
            'area'           => $t->area,
            'contact_person' => $t->merchant_contact_person ?? $t->site_contact_person,
            'contact_phone'  => $t->merchant_phone ?? $t->site_contact_number,
        ]);
    }

    /**
     * Lookup an assignment and return display name + terminals
     * GET /site-visits/lookup/assignment/{id}
     */
    public function assignmentLookup($id)
    {
        $a = JobAssignment::with([
            'technician:id,first_name,last_name',
            'client:id,company_name',
            'region:id,name',
            'project:id,project_name',
        ])->findOrFail($id);

        $display = $a->list_title
            ?? implode(' • ', array_filter([
                $a->project->project_name ?? null,
                $a->client->company_name ?? null,
                $a->region->name ?? null,
            ]))
            ?: ('Assignment '.$a->assignment_id);

        $terminalIds = collect($a->pos_terminals ?? [])->filter()->values();

        $terminals = PosTerminal::whereIn('id', $terminalIds)
            ->with('client:id,company_name')
            ->get()
            ->map(function ($t) {
                return [
                    'id'            => $t->id,
                    'terminal_id'   => $t->terminal_id,
                    'merchant_name' => $t->merchant_name,
                    'client_name'   => $t->client?->company_name,
                    'model'         => $t->terminal_model,
                    'serial'        => $t->serial_number,
                    'address'       => $t->physical_address,
                    'city'          => $t->city,
                    'province'      => $t->province,
                ];
            })
            ->values();

        return response()->json([
            'id'             => $a->id,
            'assignment_id'  => $a->assignment_id,
            'display_name'   => $display,          // Use this as the “Job Assignment” name in UI
            'technician_id'  => $a->technician_id,
            'scheduled_date' => optional($a->scheduled_date)->toDateString(),
            'terminals'      => $terminals,
        ]);
    }

    /**
     * Create 1..N visits (one per terminal).
     * POST /site-visits (batch)
     */
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'technician_id'     => ['required','exists:employees,id'],
            'job_assignment_id' => ['nullable','exists:job_assignments,id'],
            'team_name'         => ['nullable','string','max:120'], // retained for compatibility
            'visit_type'        => ['nullable','string','max:60'],
            'service_type'      => ['nullable','string','max:60'],
            'status'            => ['nullable', Rule::in(['open','in_progress','closed'])],
            'started_at'        => ['nullable','date'],
            'ended_at'          => ['nullable','date','after_or_equal:started_at'],
            'contact_person'    => ['nullable','string','max:120'],
            'phone_number'      => ['nullable','string','max:60'],

            // shared notes
            'condition_notes'          => ['nullable','string'],
            'issues_found'             => ['nullable','array'],
            'comments'                 => ['nullable','string'],
            'corrective_action'        => ['nullable','string'],
            'visit_summary'            => ['nullable','string'],
            'recommended_next_action'  => ['nullable','string'],
            'other_terminals_found'    => ['nullable','array'],

            // per-terminal payload (required)
            'terminals'                    => ['required','array','min:1'],
            'terminals.*.terminal_id'      => ['required','integer','exists:pos_terminals,id'],
            'terminals.*.status'           => ['nullable', Rule::in(['working','not_working','needs_maintenance','not_found'])],
            'terminals.*.comments'         => ['nullable','string'],
        ]);

        // if you still keep team_name at DB level and want to auto-fill from assignment:
        if (empty($validated['team_name']) && !empty($validated['job_assignment_id'])) {
            $validated['team_name'] = JobAssignment::whereKey($validated['job_assignment_id'])->value('team_name');
        }

        $now       = Carbon::now();
        $status    = $validated['status'] ?? 'open';
        $startedAt = $validated['started_at'] ?? $now;

        DB::beginTransaction();
        try {
            $created = [];

            foreach ($validated['terminals'] as $row) {
                $terminal = PosTerminal::with('client:id,company_name')->findOrFail($row['terminal_id']);

                $visit = new TechnicianVisit();
                $visit->fill([
                    // linkages
                    'technician_id'       => $validated['technician_id'],
                    'job_assignment_id'   => $validated['job_assignment_id'] ?? null,
                    'pos_terminal_id'     => $terminal->id,
                    'client_id'           => $terminal->client_id,
                    'merchant_id_snapshot'=> $terminal->merchant_id,

                    // snapshots
                    'device_type_snapshot'=> $terminal->terminal_model,
                    'serial_snapshot'     => $terminal->serial_number,
                    'team_name'           => $validated['team_name'] ?? null,
                    'address_snapshot'    => $terminal->physical_address,

                    // lifecycle
                    'status'     => $status,
                    'started_at' => $startedAt,
                    'ended_at'   => $validated['ended_at'] ?? null,

                    // classification
                    'asset_type' => 'pos_terminal',
                    'asset_id'   => $terminal->id,
                    'terminal_status_during_visit' => $row['status'] ?? null,

                    // notes
                    'condition_notes'         => $validated['condition_notes'] ?? null,
                    'issues_found'            => $validated['issues_found'] ?? null,
                    'comments'                => $row['comments'] ?? ($validated['comments'] ?? null),
                    'corrective_action'       => $validated['corrective_action'] ?? null,
                    'visit_summary'           => $validated['visit_summary'] ?? null,
                    'recommended_next_action' => $validated['recommended_next_action'] ?? null,

                    // misc arrays
                    'other_terminals_found'   => $validated['other_terminals_found'] ?? null,
                ]);

                // optional legacy date
                $visit->visit_date = $visit->started_at ?? $now;

                $visit->save();

                $created[] = [
                    'id'         => $visit->id,
                    'visit_id'   => $visit->visit_id,
                    'terminal_id'=> $terminal->id,
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Site visit(s) created successfully',
                'created' => $created,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create visit(s): '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a single visit
     */
    public function show(TechnicianVisit $visit)
    {
        $visit->load(['technician', 'posTerminal.client', 'attachments']);
        return view('site_visits.show', compact('visit'));
    }

    /**
     * Update a single visit
     */
    public function update(Request $request, TechnicianVisit $visit)
    {
        $validated = $request->validate([
            'status'  => ['nullable', Rule::in(['open','in_progress','closed'])],
            'outcome' => ['nullable', Rule::in(['completed','could_not_access_site','parts_required','reschedule','terminal_not_found','terminal_relocated'])],
            'ended_at'=> ['nullable','date','after_or_equal:started_at'],

            'condition_notes'               => ['nullable','string'],
            'issues_found'                  => ['nullable','array'],
            'comments'                      => ['nullable','string'],
            'corrective_action'             => ['nullable','string'],
            'visit_summary'                 => ['nullable','string'],
            'recommended_next_action'       => ['nullable','string'],
            'terminal_status_during_visit'  => ['nullable', Rule::in(['working','not_working','needs_maintenance','not_found'])],

            'merchant_sign_off_name'        => ['nullable','string','max:120'],
            'require_signature'             => ['nullable','boolean'],
        ]);

        // If closing as completed, signature name is required (image via attachments)
        if (($validated['status'] ?? null) === 'closed' && ($validated['outcome'] ?? null) === 'completed') {
            if (empty($validated['merchant_sign_off_name']) && !$visit->merchant_sign_off_name) {
                return response()->json([
                    'success' => false,
                    'message' => 'Merchant sign-off name is required to close a completed visit.',
                ], 422);
            }
            $validated['ended_at'] = $validated['ended_at'] ?? Carbon::now();
        }

        $visit->fill($validated)->save();

        return response()->json([
            'success' => true,
            'message' => 'Visit updated.',
            'visit'   => $visit->fresh(),
        ]);
    }

    /**
     * Upload an attachment (photo/signature/other) for a visit
     */
    public function uploadAttachment(Request $request, TechnicianVisit $visit)
    {
        $validated = $request->validate([
            'file'    => ['required','file','max:5120'], // 5MB
            'type'    => ['required', Rule::in(['photo','signature','other'])],
            'caption' => ['nullable','string','max:120'],
            'taken_at'=> ['nullable','date'],
        ]);

        $path = $request->file('file')->store("public/visits/{$visit->id}");
        $publicPath = Storage::url($path);

        $att = new TechnicianVisitAttachment();
        $att->technician_visit_id = $visit->id;
        $att->type       = $validated['type'];
        $att->path       = $publicPath;
        $att->caption    = $validated['caption'] ?? null;
        $att->taken_at   = $validated['taken_at'] ?? null;
        $att->uploaded_by= auth()->id();
        $att->metadata   = [
            'original_name' => $request->file('file')->getClientOriginalName(),
            'mime'          => $request->file('file')->getClientMimeType(),
            'size'          => $request->file('file')->getSize(),
        ];
        $att->save();

        if ($att->type === 'photo') {
            $visit->increment('photos_count');
        }
        if ($att->type === 'signature' && !$visit->merchant_signature_path) {
            $visit->merchant_signature_path = $publicPath;
            $visit->save();
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Attachment uploaded.',
            'attachment' => $att,
        ], 201);
    }

    /**
     * JSON feed for “Live Site Visits” or simple polling
     * GET /site-visits/feed?assignment_id=&terminal_id=
     */
    public function feed(Request $request)
    {
        $query = TechnicianVisit::with([
                'technician:id,first_name,last_name',
                'posTerminal:id,terminal_id,merchant_name'
            ])
            ->latest('started_at');

        if ($request->filled('assignment_id')) {
            $query->where('job_assignment_id', (int) $request->assignment_id);
        }
        if ($request->filled('terminal_id')) {
            $query->where('pos_terminal_id', (int) $request->terminal_id);
        }

        $visits = $query->take(25)->get()->map(function ($v) {
            return [
                'id'              => $v->id,
                'visit_id'        => $v->visit_id,
                'status'          => $v->status,
                'outcome'         => $v->outcome,
                'started_at'      => optional($v->started_at)->toDateTimeString(),
                'ended_at'        => optional($v->ended_at)->toDateTimeString(),
                'technician'      => $v->technician ? ($v->technician->first_name.' '.$v->technician->last_name) : null,
                'terminal_id'     => $v->posTerminal?->terminal_id,
                'merchant_name'   => $v->posTerminal?->merchant_name,
                'terminal_status' => $v->terminal_status_during_visit,
                'comments'        => $v->comments,
            ];
        });

        return response()->json([
            'success' => true,
            'visits'  => $visits,
            'count'   => $visits->count(),
        ]);
    }
    public function editTerminal(Request $request)
{
    $assignmentId = $request->assignment_id;
    $terminalId = $request->terminal_id;

    $assignment = JobAssignment::with(['technician', 'client', 'project'])->find($assignmentId);
    $terminal = PosTerminal::find($terminalId);

    // Get existing visit data if any
    $existingVisit = TechnicianVisit::where('job_assignment_id', $assignmentId)
        ->where('pos_terminal_id', $terminalId)
        ->first();

    return view('site_visits.edit_terminal', compact('assignment', 'terminal', 'existingVisit'));
}
}
