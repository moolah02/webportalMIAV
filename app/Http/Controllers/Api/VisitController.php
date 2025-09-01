<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\VisitTerminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitController extends Controller
{
    // GET /api/visits
    public function index(Request $request)
    {
        $q = Visit::query()
            ->withoutGlobalScopes()
            ->with('visitTerminal') // single terminal commonly used now
            ->orderByDesc('id');

        if (in_array(SoftDeletes::class, class_uses_recursive(Visit::class))) {
            $q->withTrashed();
        }

        $visits = $q->get();

        $totalDb = Visit::withoutGlobalScopes()->when(
            in_array(SoftDeletes::class, class_uses_recursive(Visit::class)),
            fn($qq) => $qq->withTrashed()
        )->count();

        return response()->json([
            'success'        => true,
            'count_returned' => $visits->count(),
            'total_in_db'    => $totalDb,
            'data'           => $visits,
        ]);
    }

    // GET /api/visits/{visit}
    public function show(Visit $visit)
    {
        $visit->load('visitTerminal');
        return response()->json(['success' => true, 'data' => $visit]);
    }

    // GET /api/assignments/{assignmentId}/visits
    public function indexByAssignment($assignmentId)
    {
        $visits = Visit::where('assignment_id', $assignmentId)
            ->with('visitTerminal')
            ->orderByDesc('completed_at')
            ->get();

        return response()->json([
            'success' => true,
            'count'   => $visits->count(),
            'data'    => $visits,
        ]);
    }

    // POST /api/assignments/{assignmentId}/visits
    public function storeForAssignment(Request $request, $assignmentId)
    {
        // Allow path param to override body if missing
        if (!$request->has('assignment_id')) {
            $request->merge(['assignment_id' => (string) $assignmentId]);
        }
        return $this->store($request);
    }

    // POST /api/visits
    public function store(Request $request)
    {
        $data = $request->validate([
            'merchant_id'              => ['required'],   // string "12" is fine; cast in DB if needed
            'merchant_name'            => ['required','string','max:255'],
            'employee_id'              => ['required','integer'],
            'assignment_id'            => ['required','string','max:191'], // allows "ASN-..."
            'completed_at'             => ['required','date'],

            'merchant_contact_person'  => ['nullable','string','max:255'],
            'merchant_phone'           => ['nullable','string','max:50'],

            'terminal'                        => ['required','array'],
            'terminal.terminal_id'            => ['required'], // int or string
            'terminal.status'                 => ['required','string','max:100'],
            'terminal.condition'              => ['required','string','max:100'],
            'terminal.serial_number'          => ['nullable','string','max:191'],
            'terminal.terminal_model'         => ['nullable','string','max:191'],

            'visit_summary'            => ['nullable','string'],
            'action_points'            => ['nullable','string'],
            'evidence'                 => ['nullable','array'],
            'evidence.*'               => ['nullable','string'],
            'signature'                => ['required','string'],
            'other_terminals_found'    => ['nullable','array'],
            'other_terminals_found.*'  => ['nullable'],
        ]);

        return DB::transaction(function () use ($data) {

            $visit = Visit::create([
                'merchant_id'            => $data['merchant_id'],
                'merchant_name'          => $data['merchant_name'],
                'employee_id'            => $data['employee_id'],
                'assignment_id'          => $data['assignment_id'],
                'completed_at'           => $data['completed_at'],

                'contact_person'         => $data['merchant_contact_person'] ?? null,
                'phone_number'           => $data['merchant_phone'] ?? null,

                'visit_summary'          => $data['visit_summary'] ?? null,
                'action_points'          => $data['action_points'] ?? null,
                'evidence'               => $data['evidence'] ?? null,
                'signature'              => $data['signature'],
                'other_terminals_found'  => $data['other_terminals_found'] ?? null,

                // store the snapshot exactly as received
                'terminal'               => $data['terminal'],
            ]);

            // Persist the normalized single terminal row
            $t = $data['terminal'];
            VisitTerminal::create([
                'visit_id'       => $visit->id,
                'terminal_id'    => (string) ($t['terminal_id']), // store as string to be safe
                'status'         => $t['status'],
                'condition'      => $t['condition'],
                'serial_number'  => $t['serial_number'] ?? null,
                'terminal_model' => $t['terminal_model'] ?? null,
            ]);

            $visit->load('visitTerminal');

            return response()->json([
                'success' => true,
                'message' => 'Visit recorded successfully.',
                'data'    => $visit,
            ], 201);
        });
    }

    // POST /api/visits/{visit}/evidence
    public function uploadEvidence(Request $request, Visit $visit)
    {
        $request->validate([
            'files'   => ['required','array','min:1'],
            'files.*' => ['file','max:10240','mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $stored = [];
        foreach ($request->file('files', []) as $file) {
            $path = $file->store('visits/evidence', 'public');
            $stored[] = Storage::disk('public')->url($path);
        }

        $current = $visit->evidence ?? [];
        $visit->evidence = array_values(array_merge($current, $stored));
        $visit->save();

        return response()->json([
            'success'  => true,
            'evidence' => $visit->evidence,
        ]);
    }
}
