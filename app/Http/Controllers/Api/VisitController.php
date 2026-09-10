<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\VisitTerminal;
use App\Models\TechnicianVisit;
use App\Models\PosTerminal;
use App\Models\JobAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitController extends Controller
{
    // GET /api/visit-templates
    public function templates()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'issues_found' => [
                    'No issues',
                    'Missing Device',
                    'Device relocated',
                    'Merchant Closed',
                    'Merchant Relocated',
                    'Technical Update Failure',
                    'Returned to HQ',
                ],
                'corrective_action' => [
                    'No Action needed',
                    'Follow up needed',
                    'Replacement needed',
                    'Escalate to Merchant HQ',
                ],
            ],
        ]);
    }

    // GET /api/visits
    public function index(Request $request)
    {
        // API endpoint: return JSON (mobile app).
        // NOTE: Do not assume optional columns like `employees.is_active` exist.

        $query = Visit::with(['visitTerminals', 'employee'])
            ->orderByDesc('completed_at');

        // Optional lightweight pagination/limit for mobile performance
        $limit = (int) $request->query('limit', 200);
        if ($limit > 0) {
            $query->limit(min($limit, 500));
        }

        $visits = $query->get();

        return response()->json([
            'success' => true,
            'count'   => $visits->count(),
            'data'    => $visits,
        ]);
    }

    public function myVisits(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.',
        ], 401);
    }

    // Resolve the employee id linked to this user
    $employeeId = $user->id;

    if (!$employeeId) {
        return response()->json([
            'success' => false,
            'message' => 'No employee profile linked to this user.',
        ], 422);
    }

    $visits = \App\Models\Visit::with(['visitTerminals', 'employee'])
        ->where('employee_id', $employeeId)
        ->orderByDesc('completed_at')
        ->get(); // ← no pagination

    return response()->json([
        'success' => true,
        'count'   => $visits->count(),
        'data'    => $visits,
    ]);
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
        // Accept field name aliases from mobile app
        $input = $request->all();
        if (!isset($input['merchant_contact_person']) && isset($input['contact_person'])) {
            $input['merchant_contact_person'] = $input['contact_person'];
        }
        if (!isset($input['merchant_phone']) && isset($input['phone_number'])) {
            $input['merchant_phone'] = $input['phone_number'];
        }
        $request->replace($input);

        $data = $request->validate([
            'merchant_id'              => ['required'],   // string "12" is fine; cast in DB if needed
            'merchant_name'            => ['required','string','max:255'],
            'employee_id'              => ['required','integer'],
            'assignment_id'            => ['required'], // accepts integer or string from mobile app
            'completed_at'             => ['required','date'],

            'merchant_contact_person'  => ['nullable','string','max:255'],
            'merchant_phone'           => ['nullable','string','max:50'],

            'new_contact_person'       => ['nullable','string','max:255'],
            'new_phone_number'         => ['nullable','string','max:50'],
            'new_physical_address'     => ['nullable','string','max:500'],

            'terminal'                        => ['required','array'],
            'terminal.terminal_id'            => ['required'], // int or string
            'terminal.status'                 => ['required','string','max:100'],
            'terminal.state'                  => ['nullable','string','max:50'],
            'terminal.condition'              => ['required','string','max:100'],
            'terminal.serial_number'          => ['nullable','string','max:191'],
            'terminal.terminal_model'         => ['nullable','string','max:191'],
            'terminal.device_type'            => ['nullable','string','max:191'], // mobile sends the model as device_type

            'visit_summary'            => ['nullable','string'],
            'action_points'            => ['nullable','string'],
            'corrective_action'        => ['nullable','string','max:500'],
            'terminal_comments'        => ['nullable','string','max:500'], // corrective action key sent by mobile
            'condition_notes'          => ['nullable','string','max:2000'], // free-text terminal condition notes
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

                'new_contact_person'     => $data['new_contact_person'] ?? null,
                'new_phone_number'       => $data['new_phone_number'] ?? null,
                'new_physical_address'   => $data['new_physical_address'] ?? null,

                'visit_summary'          => $data['visit_summary'] ?? null,
                'action_points'          => $data['action_points'] ?? null,
                'terminal_comments'      => $data['terminal_comments'] ?? ($data['corrective_action'] ?? null),
                'condition_notes'        => $data['condition_notes'] ?? null,
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
                'state'          => $t['state'] ?? null,
                'condition'      => $t['condition'],
                'serial_number'  => $t['serial_number'] ?? null,
                // Mobile sends the model as device_type; keep both columns in sync.
                'terminal_model' => $t['terminal_model'] ?? ($t['device_type'] ?? null),
                'device_type'    => $t['device_type'] ?? ($t['terminal_model'] ?? null),
            ]);

            $visit->load('visitTerminal');

            // Mirror to technician_visits so the report builder has data
            app(\App\Services\TechnicianVisitMirror::class)->sync($visit);

            return response()->json([
                'success' => true,
                'message' => 'Visit recorded successfully.',
                'data'    => $visit,
            ], 201);
        });
    }


    // PUT /api/visits/{visit}
    public function update(Request $request, Visit $visit)
    {
        // Accept field name aliases from mobile app
        $input = $request->all();
        if (!isset($input['merchant_contact_person']) && isset($input['contact_person'])) {
            $input['merchant_contact_person'] = $input['contact_person'];
        }
        if (!isset($input['merchant_phone']) && isset($input['phone_number'])) {
            $input['merchant_phone'] = $input['phone_number'];
        }
        $request->replace($input);

        $data = $request->validate([
            'merchant_contact_person'  => ['sometimes','nullable','string','max:255'],
            'merchant_phone'           => ['sometimes','nullable','string','max:50'],

            'new_contact_person'       => ['sometimes','nullable','string','max:255'],
            'new_phone_number'         => ['sometimes','nullable','string','max:50'],
            'new_physical_address'     => ['sometimes','nullable','string','max:500'],

            'terminal'                     => ['sometimes','array'],
            'terminal.status'              => ['sometimes','string','max:100'],
            'terminal.state'               => ['sometimes','nullable','string','max:50'],
            'terminal.condition'           => ['sometimes','string','max:100'],
            'terminal.serial_number'       => ['sometimes','nullable','string','max:191'],
            'terminal.terminal_model'      => ['sometimes','nullable','string','max:191'],
            'terminal.device_type'         => ['sometimes','nullable','string','max:191'], // mobile sends model as device_type

            'visit_summary'    => ['sometimes','nullable','string'],
            'action_points'    => ['sometimes','nullable','string'],
            'corrective_action'=> ['sometimes','nullable','string','max:500'],
            'terminal_comments'=> ['sometimes','nullable','string','max:500'], // corrective action key sent by mobile
            'condition_notes'  => ['sometimes','nullable','string','max:2000'],
            'evidence'         => ['sometimes','nullable','array'],
            'evidence.*'       => ['nullable','string'],
            'signature'        => ['sometimes','nullable','string'],
        ]);

        // Mobile sends the corrective action as terminal_comments; accept corrective_action as an alias.
        if (!array_key_exists('terminal_comments', $data) && array_key_exists('corrective_action', $data)) {
            $data['terminal_comments'] = $data['corrective_action'];
        }

        return DB::transaction(function () use ($visit, $data) {

            // Collect only the fields that were explicitly sent
            $visitUpdates = [];
            if (array_key_exists('merchant_contact_person', $data)) {
                $visitUpdates['contact_person'] = $data['merchant_contact_person'];
            }
            if (array_key_exists('merchant_phone', $data)) {
                $visitUpdates['phone_number'] = $data['merchant_phone'];
            }
            foreach (['new_contact_person','new_phone_number','new_physical_address','visit_summary','action_points','terminal_comments','condition_notes','evidence','signature'] as $field) {
                if (array_key_exists($field, $data)) {
                    $visitUpdates[$field] = $data[$field];
                }
            }
            if (!empty($visitUpdates)) {
                $visit->update($visitUpdates);
            }

            // Update the VisitTerminal record if terminal data provided
            if (!empty($data['terminal'])) {
                $t = $data['terminal'];
                $visitTerminal = $visit->visitTerminal;
                if ($visitTerminal) {
                    $terminalUpdates = [];
                    if (isset($t['status']))                       $terminalUpdates['status']         = $t['status'];
                    if (array_key_exists('state', $t))             $terminalUpdates['state']          = $t['state'];
                    if (isset($t['condition']))                    $terminalUpdates['condition']      = $t['condition'];
                    if (array_key_exists('serial_number', $t))    $terminalUpdates['serial_number']  = $t['serial_number'];
                    // Model: mobile sends device_type; accept either and keep both columns in sync.
                    $modelVal = $t['terminal_model'] ?? ($t['device_type'] ?? null);
                    if (array_key_exists('terminal_model', $t) || array_key_exists('device_type', $t)) {
                        $terminalUpdates['terminal_model'] = $modelVal;
                        $terminalUpdates['device_type']    = $modelVal;
                    }
                    if (!empty($terminalUpdates)) {
                        $visitTerminal->update($terminalUpdates);
                    }
                }
                // Keep terminal JSON snapshot in sync
                $visit->update(['terminal' => array_merge($visit->terminal ?? [], $t)]);
            }

            // Rebuild the report copy from the saved visit (also repairs a missing one).
            app(\App\Services\TechnicianVisitMirror::class)->sync($visit->fresh());

            $visit->load('visitTerminals');

            return response()->json([
                'success' => true,
                'message' => 'Visit updated successfully.',
                'data'    => $visit,
            ]);
        });
    }

    /**
     * Filter visits based on request parameters
     */
    public function filter(Request $request)
    {
        $query = Visit::with(['visitTerminals', 'employee']);

        // Date range filtering
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('completed_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('completed_at', today()->subDay());
                    break;
                case 'last_7_days':
                    $query->whereDate('completed_at', '>=', today()->subDays(7));
                    break;
                case 'last_30_days':
                    $query->whereDate('completed_at', '>=', today()->subDays(30));
                    break;
                case 'this_month':
                    $query->whereMonth('completed_at', now()->month)
                          ->whereYear('completed_at', now()->year);
                    break;
                case 'custom':
                    if ($request->filled('start_date')) {
                        $query->whereDate('completed_at', '>=', $request->start_date);
                    }
                    if ($request->filled('end_date')) {
                        $query->whereDate('completed_at', '<=', $request->end_date);
                    }
                    break;
            }
        }

        // Technician filter
        if ($request->filled('technician_id')) {
            $query->where('employee_id', $request->technician_id);
        }

        // Region filter (assuming you have a region relationship)
        if ($request->filled('region_id')) {
            $query->whereHas('visitTerminals.posTerminal', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }

        // Status filter (you might need to adjust this based on your Visit model)
        if ($request->filled('terminal_status')) {
            $query->where('visit_status', $request->terminal_status);
        }

        // Client filter
        if ($request->filled('client_id')) {
            $query->whereHas('visitTerminals.posTerminal', function($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('merchant_name', 'like', "%{$search}%")
                  ->orWhere('visit_summary', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $visits = $query->orderByDesc('completed_at')->get();

        // Calculate stats
        $stats = [
            'today_visits' => Visit::whereDate('completed_at', today())->count(),
            'working_terminals' => $visits->where('visit_status', 'completed')->count(),
            'issues_found' => $visits->where('visit_status', 'issues_found')->count(),
            'not_seen' => $visits->where('visit_status', 'not_completed')->count(),
        ];

        return response()->json([
            'success' => true,
            'visits' => $visits,
            'stats' => $stats,
            'total' => $visits->count()
        ]);
    }

    /**
     * Show a specific visit
     */
    public function show(Visit $visit)
    {
        $visit->load(['visitTerminals', 'employee']);

        $html = view('reports.partials.visit-details', compact('visit'))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'visit' => $visit
        ]);
    }

    /**
     * Get photos for a specific visit
     */
    public function getPhotos(Visit $visit)
    {
        // Assuming you store photos in a JSON field or separate table
        $photos = [];

        // If photos are stored as JSON in the visit record
        if ($visit->photos) {
            $photoData = json_decode($visit->photos, true);
            foreach ($photoData as $photo) {
                $photos[] = [
                    'url' => asset('storage/' . $photo['path']),
                    'caption' => $photo['caption'] ?? ''
                ];
            }
        }

        return response()->json([
            'success' => true,
            'photos' => $photos
        ]);
    }

    /**
     * Generate PDF report for a visit
     */
    public function generatePDF(Visit $visit)
    {
        $visit->load(['visitTerminals', 'employee']);

        // You'll need to install a PDF library like barryvdh/laravel-dompdf
        // composer require barryvdh/laravel-dompdf

        $pdf = \PDF::loadView('reports.pdf.visit-report', compact('visit'));

        return $pdf->download("visit-report-{$visit->id}.pdf");
    }

    /**
     * Export filtered visits
     */
    public function export(Request $request)
    {
        // Apply the same filters as the filter method
        $query = Visit::with(['visitTerminals', 'employee']);

        // Copy filtering logic from filter() method here...
        // (Same filtering code as above)

        $visits = $query->orderByDesc('completed_at')->get();

        $filename = 'technician-visits-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($visits) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Visit ID',
                'Date',
                'Technician',
                'Merchant',
                'Terminal Count',
                'Status',
                'Duration',
                'Summary'
            ]);

            // CSV data
            foreach ($visits as $visit) {
                fputcsv($file, [
                    $visit->id,
                    $visit->completed_at->format('Y-m-d H:i:s'),
                    $visit->employee ? $visit->employee->first_name . ' ' . $visit->employee->last_name : 'N/A',
                    $visit->merchant_name,
                    $visit->visitTerminals->count(),
                    $visit->visit_status ?? 'N/A',
                    $visit->duration_minutes ? "{$visit->duration_minutes} minutes" : 'N/A',
                    $visit->visit_summary
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Store a visit evidence photo on the MIAV server (replaces Firebase Storage).
     *
     * Two modes:
     *  - POST /api/uploads/evidence          -> standalone; returns the URL so the
     *                                           mobile app can include it in evidence[]
     *                                           when it creates/updates the visit.
     *  - POST /api/visits/{visit}/evidence   -> also appends the URL to that visit.
     */
    public function uploadEvidence(Request $request, Visit $visit = null)
    {
        $request->validate([
            'photo' => 'required|file|mimes:jpg,jpeg,png,webp,heic|max:12288', // 12MB
        ]);

        $file = $request->file('photo');
        $ext  = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $name = 'visit_' . ($visit->id ?? 'new') . '_' . now()->format('Ymd_His') . '_'
              . substr(bin2hex(random_bytes(4)), 0, 8) . '.' . $ext;

        // storage/app/public/visit_evidence/... served via the /storage symlink
        $path = $file->storeAs('visit_evidence', $name, 'public');
        $url  = url('storage/' . $path);

        if ($visit) {
            $evidence   = $visit->evidence ?? [];
            $evidence[] = $url;
            $visit->update(['evidence' => $evidence]);
        }

        return response()->json([
            'success' => true,
            'url'     => $url,
            'path'    => $path,
        ], 201);
    }
}
