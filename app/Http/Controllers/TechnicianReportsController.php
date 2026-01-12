<?php
// app/Http/Controllers/TechnicianReportsController.php

namespace App\Http\Controllers;

use App\Models\TechnicianVisit;
use App\Models\Employee;
use App\Models\Region;
use App\Models\Client;
use App\Models\PosTerminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TechnicianReportsController extends Controller
{
    /**
     * Display technician visit reports
     */
    public function index(Request $request)
    {
        // Check if TechnicianVisit table exists
        if (!class_exists(\App\Models\TechnicianVisit::class)) {
            return view('technician-reports.index', [
                'visits' => collect([]),
                'technicians' => Employee::select('id', 'first_name', 'last_name')->get(),
                'regions' => Region::where('is_active', true)->get(),
                'clients' => Client::orderBy('name', 'asc')->get(),
                'stats' => [
                    'total_visits' => 0,
                    'completed_visits' => 0,
                    'pending_visits' => 0,
                    'total_terminals' => 0,
                ],
                'message' => 'Technician visits data is not available'
            ]);
        }

        try {
            // Get filter parameters
            $dateRange = $request->get('date_range', 'last_7_days');
            $technicianId = $request->get('technician_id');
            $regionId = $request->get('region_id');
            $terminalStatus = $request->get('terminal_status');
            $clientId = $request->get('client_id');
            $search = $request->get('search');

            // Build query with proper relationships
            $query = TechnicianVisit::with([
                'technician:id,first_name,last_name,phone',
                'posTerminal:id,terminal_id,merchant_name,physical_address,region_id,client_id',
                'posTerminal.region:id,name',
                'posTerminal.client:id,company_name'

            ]);

        // Apply date range filter
        $this->applyDateRangeFilter($query, $dateRange, $request);

        // Apply other filters
        if ($technicianId) {
            $query->where('technician_id', $technicianId);
        }

        if ($regionId) {
            $query->whereHas('posTerminal', function($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }

        if ($terminalStatus) {
            $query->where('terminal_status', $terminalStatus);
        }

        if ($clientId) {
            $query->whereHas('posTerminal', function($q) use ($clientId) {
                $q->where('client_id', $clientId);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('visit_id', 'like', "%{$search}%")
                  ->orWhere('asset_id', 'like', "%{$search}%")
                  ->orWhereHas('posTerminal', function($subQ) use ($search) {
                      $subQ->where('terminal_id', 'like', "%{$search}%")
                           ->orWhere('merchant_name', 'like', "%{$search}%");
                  });
            });
        }

        // Get paginated results
        $visits = $query->orderBy('visit_date', 'desc')->paginate(20);

        // Add computed names to technicians
        $visits->getCollection()->transform(function ($visit) {
            if ($visit->technician) {
                $visit->technician->name = $visit->technician->first_name . ' ' . $visit->technician->last_name;
            }
            return $visit;
        });

       $technicians = Employee::select('id', 'first_name', 'last_name')
    ->orderBy('first_name')
    ->get()
    ->map(function($technician) {
        $technician->name = $technician->first_name . ' ' . $technician->last_name;
        return $technician;
    });


        $regions = Region::where('is_active', true)->orderBy('name')->get();
        // This will work with your current database structure
$clients = Client::orderBy('company_name', 'asc')->get();

            // Calculate stats
            $stats = $this->calculateStats($dateRange, $request);

            return view('reports.technician-visits', compact(
                'visits', 'technicians', 'regions', 'clients', 'stats'
            ));
        } catch (\Exception $e) {
            // TechnicianVisit table doesn't exist or has issues
            return view('technician-reports.index', [
                'visits' => collect([]),
                'technicians' => Employee::select('id', 'first_name', 'last_name')->get(),
                'regions' => Region::where('is_active', true)->get(),
                'clients' => Client::orderBy('name', 'asc')->get(),
                'stats' => [
                    'total_visits' => 0,
                    'completed_visits' => 0,
                    'pending_visits' => 0,
                    'total_terminals' => 0,
                ],
                'error' => 'Unable to load technician visits: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get filtered data via AJAX
     */
    public function filter(Request $request)
    {
        // Same logic as index but return JSON
        $dateRange = $request->get('date_range', 'last_7_days');
        $technicianId = $request->get('technician_id');
        $regionId = $request->get('region_id');
        $terminalStatus = $request->get('terminal_status');
        $clientId = $request->get('client_id');
        $search = $request->get('search');

        $query = TechnicianVisit::with([
            'technician:id,first_name,last_name,phone',
            'posTerminal:id,terminal_id,merchant_name,physical_address,region_id,client_id',
            'posTerminal.region:id,name',
            'posTerminal.client:id,company_name'

        ]);

        $this->applyDateRangeFilter($query, $dateRange, $request);

        if ($technicianId) $query->where('technician_id', $technicianId);
        if ($regionId) {
            $query->whereHas('posTerminal', function($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }
        if ($terminalStatus) $query->where('terminal_status', $terminalStatus);
        if ($clientId) {
            $query->whereHas('posTerminal', function($q) use ($clientId) {
                $q->where('client_id', $clientId);
            });
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('visit_id', 'like', "%{$search}%")
                  ->orWhere('asset_id', 'like', "%{$search}%")
                  ->orWhereHas('posTerminal', function($subQ) use ($search) {
                      $subQ->where('terminal_id', 'like', "%{$search}%")
                           ->orWhere('merchant_name', 'like', "%{$search}%");
                  });
            });
        }

        $visits = $query->orderBy('visit_date', 'desc')->get();

        // Add computed names
        $visits->transform(function ($visit) {
            if ($visit->technician) {
                $visit->technician->name = $visit->technician->first_name . ' ' . $visit->technician->last_name;
            }
            return $visit;
        });

        $stats = $this->calculateStats($dateRange, $request);

        return response()->json([
            'success' => true,
            'visits' => $visits,
            'stats' => $stats
        ]);
    }

    /**
     * Get visit details
     */
    public function show($visitId)
    {
        $visit = TechnicianVisit::with([
            'technician:id,first_name,last_name,phone,specialization',
            'posTerminal.region:id,name',
            'posTerminal.client:id,company_name'

        ])->findOrFail($visitId);

        // Add computed name
        if ($visit->technician) {
            $visit->technician->name = $visit->technician->first_name . ' ' . $visit->technician->last_name;
        }

        $html = view('reports.partials.visit-details', compact('visit'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Get visit photos
     */
    public function getPhotos($visitId)
    {
        $visit = TechnicianVisit::findOrFail($visitId);

        $photos = [];
        if ($visit->photos) {
            $photoData = json_decode($visit->photos, true);
            foreach ($photoData as $photo) {
                $photos[] = [
                    'url' => asset('storage/visit-photos/' . $photo['filename']),
                    'caption' => $photo['caption'] ?? null
                ];
            }
        }

        return response()->json([
            'success' => true,
            'photos' => $photos
        ]);
    }

    /**
     * Export visits to CSV
     */
    public function export(Request $request)
    {
        $dateRange = $request->get('date_range', 'last_7_days');
        $technicianId = $request->get('technician_id');
        $regionId = $request->get('region_id');
        $terminalStatus = $request->get('terminal_status');
        $clientId = $request->get('client_id');
        $search = $request->get('search');

        $query = TechnicianVisit::with([
            'technician:id,first_name,last_name,phone',
            'posTerminal:id,terminal_id,merchant_name,physical_address,region_id,client_id',
            'posTerminal.region:id,name',
            'posTerminal.client:id,company_name'

        ]);

        $this->applyDateRangeFilter($query, $dateRange, $request);

        if ($technicianId) $query->where('technician_id', $technicianId);
        if ($regionId) {
            $query->whereHas('posTerminal', function($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }
        if ($terminalStatus) $query->where('terminal_status', $terminalStatus);
        if ($clientId) {
            $query->whereHas('posTerminal', function($q) use ($clientId) {
                $q->where('client_id', $clientId);
            });
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('visit_id', 'like', "%{$search}%")
                  ->orWhere('asset_id', 'like', "%{$search}%")
                  ->orWhereHas('posTerminal', function($subQ) use ($search) {
                      $subQ->where('terminal_id', 'like', "%{$search}%")
                           ->orWhere('merchant_name', 'like', "%{$search}%");
                  });
            });
        }

        $visits = $query->orderBy('visit_date', 'desc')->get();

        // Create CSV
        $filename = 'technician-visits-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($visits) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Visit ID', 'Date', 'Time', 'Technician', 'Terminal ID',
                'Merchant Name', 'Region', 'Client', 'Status', 'Duration (minutes)',
                'Issues Found', 'Feedback', 'Comments'
            ]);

            foreach ($visits as $visit) {
                $technicianName = '';
                if ($visit->technician) {
                    $technicianName = $visit->technician->first_name . ' ' . $visit->technician->last_name;
                }

                fputcsv($file, [
                    $visit->visit_id,
                    date('Y-m-d', strtotime($visit->visit_date)),
                    date('H:i:s', strtotime($visit->visit_date)),
                    $technicianName,
                    $visit->posTerminal->terminal_id ?? 'N/A',
                    $visit->posTerminal->merchant_name ?? 'N/A',
                    $visit->posTerminal->region->name ?? 'N/A',
                    $visit->posTerminal->client->name ?? 'N/A',
                    $visit->terminal_status,
                    $visit->duration_minutes,
                    $visit->issues_found ? count(json_decode($visit->issues_found)) : 0,
                    $visit->technician_feedback,
                    $visit->comments
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Apply date range filter to query
     */
    private function applyDateRangeFilter($query, $dateRange, $request)
    {
        $now = Carbon::now();

        switch ($dateRange) {
            case 'today':
                $query->whereDate('visit_date', $now->toDateString());
                break;
            case 'yesterday':
                $query->whereDate('visit_date', $now->subDay()->toDateString());
                break;
            case 'last_7_days':
                $query->where('visit_date', '>=', $now->subDays(7));
                break;
            case 'last_30_days':
                $query->where('visit_date', '>=', $now->subDays(30));
                break;
            case 'this_month':
                $query->whereMonth('visit_date', $now->month)
                      ->whereYear('visit_date', $now->year);
                break;
            case 'custom':
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween('visit_date', [
                        $request->start_date . ' 00:00:00',
                        $request->end_date . ' 23:59:59'
                    ]);
                }
                break;
        }
    }

    /**
     * Calculate statistics
     */
    private function calculateStats($dateRange, $request)
    {
        $baseQuery = TechnicianVisit::query();
        $this->applyDateRangeFilter($baseQuery, $dateRange, $request);

        $todayVisits = TechnicianVisit::whereDate('visit_date', Carbon::today())->count();

        $workingTerminals = (clone $baseQuery)->where('terminal_status', 'seen_working')->count();
        $issuesFound = (clone $baseQuery)->where('terminal_status', 'seen_issues')->count();
        $notSeen = (clone $baseQuery)->where('terminal_status', 'not_seen')->count();

        return [
            'today_visits' => $todayVisits,
            'working_terminals' => $workingTerminals,
            'issues_found' => $issuesFound,
            'not_seen' => $notSeen,
            'total_visits' => $baseQuery->count()
        ];
    }
}
