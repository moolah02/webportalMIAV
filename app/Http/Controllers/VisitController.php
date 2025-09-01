<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    /**
     * List visits for the web UI with light filters:
     * ?merchantId=&employeeId=&dateFrom=&dateTo=&q=
     */
    public function index(Request $request)
{
    $q = \App\Models\Visit::query()
        ->with(['visitTerminals', 'employee'])  // 👈 so we can show employee name
        ->orderByDesc('completed_at');

    // NEW: filter by merchant name (string)
    if ($request->filled('merchant')) {
        $q->where('merchant_name', 'like', '%'.$request->input('merchant').'%');
    }

    // NEW: filter by employee name (join via relation)
    if ($request->filled('employee')) {
        $name = $request->input('employee');
        $q->whereHas('employee', function ($qq) use ($name) {
            $qq->whereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) LIKE ?", ["%{$name}%"])
               ->orWhere('first_name', 'like', "%{$name}%")
               ->orWhere('last_name', 'like', "%{$name}%");
        });
    }

    // Keep your date & free-text filters if you want:
    if ($request->filled('dateFrom')) {
        $q->where('completed_at', '>=', $request->input('dateFrom').' 00:00:00');
    }
    if ($request->filled('dateTo')) {
        $q->where('completed_at', '<=', $request->input('dateTo').' 23:59:59');
    }
    if ($request->filled('q')) {
        $term = $request->input('q');
        $q->where(function ($sub) use ($term) {
            $sub->where('merchant_name', 'like', "%{$term}%")
                ->orWhere('visit_summary', 'like', "%{$term}%")
                ->orWhere('action_points', 'like', "%{$term}%");
        });
    }

    $visits = $q->get(); // still no pagination per your preference
    return view('visits.index', compact('visits'));
}


    /**
     * Show a single visit.
     */
    public function show(Visit $visit)
    {
        $visit->load('visitTerminals');
        return view('visits.show', compact('visit'));
    }
    public function suggestMerchants(Request $request)
{
    $term = (string) $request->query('q', '');
    if (mb_strlen($term) < 1) return response()->json([]);

    // distinct merchant names from visits table
    $names = \App\Models\Visit::query()
        ->where('merchant_name', 'like', "%{$term}%")
        ->select('merchant_name')
        ->distinct()
        ->orderBy('merchant_name')
        ->limit(10)
        ->pluck('merchant_name');

    return response()->json($names);
}

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


}
