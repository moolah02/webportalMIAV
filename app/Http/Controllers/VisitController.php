<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VisitController extends Controller
{
    /**
     * List visits for the web UI with light filters:
     * ?merchant=&employee=&dateFrom=&dateTo=&q=
     */
    public function index(Request $request)
    {
        $q = Visit::query()
            ->with(['employee'])  // Only load employee relation since terminal data is now JSON
            ->orderByDesc('completed_at');

        // Filter by merchant name (string)
        if ($request->filled('merchant')) {
            $q->where('merchant_name', 'like', '%'.$request->input('merchant').'%');
        }

        // Filter by employee name (join via relation)
        if ($request->filled('employee')) {
            $name = $request->input('employee');
            $q->whereHas('employee', function ($qq) use ($name) {
                $qq->whereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) LIKE ?", ["%{$name}%"])
                   ->orWhere('first_name', 'like', "%{$name}%")
                   ->orWhere('last_name', 'like', "%{$name}%");
            });
        }

        // Filter by terminal ID (partial match against JSON terminal snapshot)
        if ($request->filled('terminal')) {
            $t = $request->input('terminal');
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(terminal, '$.terminal_id')) LIKE ?", ["%{$t}%"]);
        }

        // Filter by terminal status if provided
        if ($request->filled('terminal_status')) {
            $status = $request->input('terminal_status');
            $q->whereRaw("JSON_EXTRACT(terminal, '$.status') = ?", [$status]);
        }

        // Date filters
        if ($request->filled('dateFrom')) {
            $q->where('completed_at', '>=', $request->input('dateFrom').' 00:00:00');
        }
        if ($request->filled('dateTo')) {
            $q->where('completed_at', '<=', $request->input('dateTo').' 23:59:59');
        }

        // Free text search
        if ($request->filled('q')) {
            $term = $request->input('q');
            $q->where(function ($sub) use ($term) {
                $sub->where('merchant_name', 'like', "%{$term}%")
                    ->orWhere('visit_summary', 'like', "%{$term}%")
                    ->orWhere('action_points', 'like', "%{$term}%")
                    ->orWhere('contact_person', 'like', "%{$term}%");
            });
        }

        $visits = $q->get();
        return view('visits.index', compact('visits'));
    }
// In Visit model
public function posTerminal()
{
    return $this->belongsTo(\App\Models\PosTerminal::class, 'pos_terminal_id');
}


    public function edit(Visit $visit)
    {
        $visit->load('employee');
        return view('visits.edit', compact('visit'));
    }

    public function update(Request $request, Visit $visit)
    {
        $request->validate([
            'visit_summary'   => ['nullable', 'string', 'max:2000'],
            'action_points'   => ['nullable', 'string', 'in:Resolved,No action needed,To collect device,Follow-up needed,Replacement needed'],
            'completed_at'    => ['nullable', 'date'],
            'new_evidence'    => ['nullable', 'array'],
            'new_evidence.*'  => ['file', 'max:5120', 'mimes:jpeg,jpg,png,gif,webp,pdf,doc,docx'],
            'remove_evidence' => ['nullable', 'array'],
        ]);

        // Build updated evidence array
        $evidence = is_array($visit->evidence) ? $visit->evidence : [];

        // Remove checked items (process in reverse to keep indexes stable)
        $toRemove = array_map('intval', $request->input('remove_evidence', []));
        foreach (array_reverse($toRemove) as $idx) {
            unset($evidence[$idx]);
        }
        $evidence = array_values($evidence);

        // Append newly uploaded files
        if ($request->hasFile('new_evidence')) {
            foreach ($request->file('new_evidence') as $file) {
                $path = $file->store("visits/{$visit->id}", 'public');
                $evidence[] = Storage::url($path);
            }
        }

        $visit->update([
            'visit_summary' => $request->input('visit_summary'),
            'action_points' => $request->input('action_points') ?: null,
            'completed_at'  => $request->input('completed_at') ?: null,
            'evidence'      => $evidence,
        ]);

        return redirect()->route('visits.show', $visit)->with('success', 'Visit updated successfully.');
    }

    public function show(Visit $visit)
    {
        // Load the employee relationship
        $visit->load('employee');

        // Debug: Let's check what terminal data we have
        Log::info('Visit terminal data:', [
            'visit_id' => $visit->id,
            'terminal_json' => $visit->terminal,
            'raw_terminal' => $visit->getAttributes()['terminal'] ?? 'NULL'
        ]);

        // Debug: Check if we can find the terminal in pos_terminals
        if ($visit->hasTerminal()) {
            $terminalId = $visit->getTerminalId();
            $posTerminal = DB::table('pos_terminals')->where('terminal_id', $terminalId)->first();
            Log::info('POS Terminal lookup:', [
                'terminal_id' => $terminalId,
                'found' => $posTerminal ? 'YES' : 'NO',
                'pos_terminal' => $posTerminal
            ]);
        }

        return view('visits.show', compact('visit'));
    }

    /**
     * Get distinct merchant names for autocomplete
     */
    public function suggestMerchants(Request $request)
    {
        $term = (string) $request->query('q', '');
        if (mb_strlen($term) < 1) return response()->json([]);

        $names = Visit::query()
            ->where('merchant_name', 'like', "%{$term}%")
            ->select('merchant_name')
            ->distinct()
            ->orderBy('merchant_name')
            ->limit(10)
            ->pluck('merchant_name');

        return response()->json($names);
    }

    /**
     * Get employees for autocomplete
     */
    public function suggestEmployees(Request $request)
    {
        $term = (string) $request->query('q', '');
        if (mb_strlen($term) < 1) return response()->json([]);

        $employees = \App\Models\Employee::query()
            ->select('id', 'first_name', 'last_name')
            ->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) LIKE ?", ["%{$term}%"]);
            })
            ->orderBy('first_name')
            ->limit(10)
            ->get()
            ->map(fn($e) => ['id' => $e->id, 'name' => trim($e->first_name.' '.$e->last_name)]);

        return response()->json($employees);
    }

    /**
     * Get terminal statuses for filtering
     */
    public function getTerminalStatuses()
    {
        // Get distinct terminal statuses from the JSON data
        $statuses = \App\Models\Visit::query()
            ->whereNotNull('terminal')
            ->get()
            ->pluck('terminal')
            ->map(function ($terminal) {
                return $terminal['status'] ?? null;
            })
            ->filter()
            ->unique()
            ->values();

        return response()->json($statuses);
    }

    /**
     * Autocomplete terminal IDs from the JSON terminal snapshot
     */
    public function suggestTerminals(Request $request)
    {
        $term = (string) $request->query('q', '');
        if (mb_strlen($term) < 1) return response()->json([]);

        $ids = Visit::query()
            ->whereNotNull('terminal')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(terminal, '$.terminal_id')) LIKE ?", ["%{$term}%"])
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(terminal, '$.terminal_id')) as terminal_id")
            ->distinct()
            ->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(terminal, '$.terminal_id'))")
            ->limit(10)
            ->pluck('terminal_id')
            ->filter();

        return response()->json($ids->values());
    }

    /**
     * Get terminal summary statistics
     */
    public function getTerminalStats()
    {
        $visits = \App\Models\Visit::query()->whereNotNull('terminal')->get();

        $stats = [
            'total_visits' => $visits->count(),
            'working_terminals' => 0,
            'maintenance_needed' => 0,
            'other_issues' => 0,
        ];

        foreach ($visits as $visit) {
            $terminal = $visit->terminal;
            $status = strtolower($terminal['status'] ?? '');

            if (in_array($status, ['working', 'found'])) {
                $stats['working_terminals']++;
            } elseif (in_array($status, ['needs maintenance', 'maintenance'])) {
                $stats['maintenance_needed']++;
            } else {
                $stats['other_issues']++;
            }
        }

        return response()->json($stats);
    }
}
