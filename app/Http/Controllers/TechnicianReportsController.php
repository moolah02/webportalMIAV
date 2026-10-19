<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TechnicianReportsController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);
        $visits = $query->paginate(25)->withQueryString();

        $stats = $this->calcStats($request);
        $technicians = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return view('reports.technician-visits', compact('visits', 'stats', 'technicians'));
    }

    public function filter(Request $request)
    {
        $visits = $this->buildQuery($request)->get();
        $stats  = $this->calcStats($request);

        return response()->json([
            'success' => true,
            'visits'  => $visits->map(fn($v) => $this->formatVisit($v)),
            'stats'   => $stats,
            'total'   => $visits->count(),
        ]);
    }

    public function show($visitId)
    {
        $visit = Visit::with('employee')->findOrFail($visitId);
        return response()->json(['success' => true, 'visit' => $this->formatVisit($visit)]);
    }

    public function export(Request $request)
    {
        $visits = $this->buildQuery($request)->get();

        $filename = 'visit-reports-' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($visits) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");   // BOM, so Excel reads accented names correctly
            fputcsv($file, ['ID', 'Date', 'Merchant', 'Merchant ID', 'Employee', 'Assignment', 'Terminal ID', 'Status', 'Summary', 'Action Points']);
            foreach ($visits as $v) {
                fputcsv($file, [
                    $v->id,
                    optional($v->completed_at)->format('Y-m-d H:i'),
                    $v->merchant_name,
                    $v->merchant_id,
                    optional($v->employee)->full_name ?? $v->employee_id,
                    $v->assignment_id,
                    $v->terminal['terminal_id'] ?? '',
                    $v->completed_at ? 'Completed' : 'Pending',
                    $v->visit_summary,
                    $v->action_points,
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    // ----------------------------------------------------------------

    private function buildQuery(Request $request)
    {
        $query = Visit::with('employee')->latest('completed_at');

        // Date range
        $range = $request->get('date_range', 'last_7_days');
        if ($range === 'custom') {
            if ($request->filled('start_date')) {
                $query->where('completed_at', '>=', $request->start_date . ' 00:00:00');
            }
            if ($request->filled('end_date')) {
                $query->where('completed_at', '<=', $request->end_date . ' 23:59:59');
            }
        } else {
            // completed_at holds Harare local time, so the boundaries are built
            // on the Harare clock; using the UTC app clock shifted every range
            // by two hours.
            $now = Carbon::now('Africa/Harare');
            $from = match ($range) {
                'today'        => $now->copy()->startOfDay(),
                'yesterday'    => $now->copy()->subDay()->startOfDay(),
                'last_7_days'  => $now->copy()->subDays(7),
                'last_30_days' => $now->copy()->subDays(30),
                'this_month'   => $now->copy()->startOfMonth(),
                default        => $now->copy()->subDays(7),
            };
            $query->where('completed_at', '>=', $from->format('Y-m-d H:i:s'));

            if ($range === 'yesterday') {
                $query->where('completed_at', '<=', $now->copy()->subDay()->endOfDay()->format('Y-m-d H:i:s'));
            }
        }

        // Employee / technician filter
        if ($request->filled('technician_id')) {
            $query->where('employee_id', $request->technician_id);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('completed_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('completed_at');
            }
        }

        // Keyword search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('merchant_name', 'like', "%{$s}%")
                  ->orWhere('visit_summary', 'like', "%{$s}%")
                  ->orWhere('action_points', 'like', "%{$s}%");
            });
        }

        return $query;
    }

    /**
     * The cards sit beside the table, so they count the same filtered set —
     * they used to show all-time totals next to a filtered list.
     *
     * "Today" is today in Harare: completed_at holds local time while the app
     * clock is UTC, so Carbon::today() moved the boundary by two hours.
     */
    private function calcStats(?Request $request = null): array
    {
        $base = fn () => $request ? $this->buildQuery($request) : Visit::query();
        $localToday = Carbon::now('Africa/Harare')->toDateString();

        return [
            'today_visits'    => $base()->whereBetween('completed_at', [$localToday.' 00:00:00', $localToday.' 23:59:59'])->count(),
            'completed'       => $base()->whereNotNull('completed_at')->count(),
            'pending'         => $base()->whereNull('completed_at')->count(),
            'total_visits'    => $base()->count(),
        ];
    }

    private function formatVisit(Visit $v): array
    {
        return [
            'id'           => $v->id,
            'completed_at' => optional($v->completed_at)->toDateTimeString(),
            'merchant_name'=> $v->merchant_name,
            'merchant_id'  => $v->merchant_id,
            'employee_name'=> optional($v->employee)->full_name ?? ('Employee #' . $v->employee_id),
            'assignment_id'=> $v->assignment_id,
            'terminal_id'  => $v->terminal['terminal_id'] ?? null,
            'status'       => $v->completed_at ? 'completed' : 'pending',
            'visit_summary'=> $v->visit_summary,
            'action_points'=> $v->action_points,
            'evidence_count'=> count(is_array($v->evidence) ? $v->evidence : []),
        ];
    }
}
