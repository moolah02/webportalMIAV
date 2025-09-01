<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get available data sources for the report builder
     */
    public function getDataSources()
    {
        return response()->json([
            'dataSources' => [
                [
                    'id' => 'assets',
                    'name' => 'Assets',
                    'table' => 'assets',
                    'description' => 'Asset inventory data',
                    'fields' => [
                        ['name' => 'id', 'type' => 'number', 'label' => 'Asset ID'],
                        ['name' => 'name', 'type' => 'string', 'label' => 'Asset Name'],
                        ['name' => 'category', 'type' => 'string', 'label' => 'Category'],
                        ['name' => 'brand', 'type' => 'string', 'label' => 'Brand'],
                        ['name' => 'stock_quantity', 'type' => 'number', 'label' => 'Stock Quantity'],
                        ['name' => 'assigned_quantity', 'type' => 'number', 'label' => 'Assigned Quantity'],
                        ['name' => 'available_quantity', 'type' => 'number', 'label' => 'Available Quantity'],
                        ['name' => 'unit_price', 'type' => 'currency', 'label' => 'Unit Price'],
                        ['name' => 'status', 'type' => 'string', 'label' => 'Status'],
                        ['name' => 'created_at', 'type' => 'date', 'label' => 'Created Date']
                    ]
                ],
                [
                    'id' => 'employees',
                    'name' => 'Employees',
                    'table' => 'employees',
                    'description' => 'Employee data',
                    'fields' => [
                        ['name' => 'id', 'type' => 'number', 'label' => 'Employee ID'],
                        ['name' => 'first_name', 'type' => 'string', 'label' => 'First Name'],
                        ['name' => 'last_name', 'type' => 'string', 'label' => 'Last Name'],
                        ['name' => 'email', 'type' => 'string', 'label' => 'Email'],
                        ['name' => 'department_id', 'type' => 'number', 'label' => 'Department ID'],
                        ['name' => 'status', 'type' => 'string', 'label' => 'Status'],
                        ['name' => 'hire_date', 'type' => 'date', 'label' => 'Hire Date']
                    ]
                ],
                [
                    'id' => 'pos_terminals',
                    'name' => 'POS Terminals',
                    'table' => 'pos_terminals',
                    'description' => 'POS terminal data',
                    'fields' => [
                        ['name' => 'id', 'type' => 'number', 'label' => 'Terminal ID'],
                        ['name' => 'terminal_id', 'type' => 'string', 'label' => 'Terminal Code'],
                        ['name' => 'merchant_name', 'type' => 'string', 'label' => 'Merchant Name'],
                        ['name' => 'city', 'type' => 'string', 'label' => 'City'],
                        ['name' => 'province', 'type' => 'string', 'label' => 'Province'],
                        ['name' => 'status', 'type' => 'string', 'label' => 'Status'],
                        ['name' => 'terminal_model', 'type' => 'string', 'label' => 'Terminal Model'],
                        ['name' => 'installation_date', 'type' => 'date', 'label' => 'Installation Date']
                    ]
                ],
                [
                    'id' => 'tickets',
                    'name' => 'Tickets',
                    'table' => 'tickets',
                    'description' => 'Support tickets',
                    'fields' => [
                        ['name' => 'id', 'type' => 'number', 'label' => 'Ticket ID'],
                        ['name' => 'title', 'type' => 'string', 'label' => 'Ticket Title'],
                        ['name' => 'status', 'type' => 'string', 'label' => 'Status'],
                        ['name' => 'priority', 'type' => 'string', 'label' => 'Priority'],
                        ['name' => 'issue_type', 'type' => 'string', 'label' => 'Issue Type'],
                        ['name' => 'created_at', 'type' => 'date', 'label' => 'Created Date'],
                        ['name' => 'resolved_at', 'type' => 'date', 'label' => 'Resolved Date']
                    ]
                ],
                [
                    'id' => 'asset_requests',
                    'name' => 'Asset Requests',
                    'table' => 'asset_requests',
                    'description' => 'Asset request data',
                    'fields' => [
                        ['name' => 'id', 'type' => 'number', 'label' => 'Request ID'],
                        ['name' => 'status', 'type' => 'string', 'label' => 'Status'],
                        ['name' => 'priority', 'type' => 'string', 'label' => 'Priority'],
                        ['name' => 'total_estimated_cost', 'type' => 'currency', 'label' => 'Estimated Cost'],
                        ['name' => 'created_at', 'type' => 'date', 'label' => 'Request Date'],
                        ['name' => 'approved_at', 'type' => 'date', 'label' => 'Approved Date']
                    ]
                ]
            ],
            'chartTypes' => [
                ['id' => 'bar', 'name' => 'Bar Chart', 'icon' => 'bar-chart'],
                ['id' => 'line', 'name' => 'Line Chart', 'icon' => 'line-chart'],
                ['id' => 'pie', 'name' => 'Pie Chart', 'icon' => 'pie-chart'],
                ['id' => 'donut', 'name' => 'Donut Chart', 'icon' => 'donut-chart'],
                ['id' => 'area', 'name' => 'Area Chart', 'icon' => 'area-chart'],
                ['id' => 'table', 'name' => 'Data Table', 'icon' => 'table']
            ]
        ]);
    }

    /**
     * Generate chart data based on configuration
     */
    public function generateChart(Request $request)
    {
        $config = $request->validate([
            'dataSource' => 'required|string',
            'chartType' => 'required|string',
            'xAxis' => 'required|string',
            'yAxis' => 'required|string',
            'groupBy' => 'nullable|string',
            'filters' => 'nullable|array'
        ]);

        try {
            $query = DB::table($config['dataSource']);

            // Apply filters if provided
            if (!empty($config['filters'])) {
                foreach ($config['filters'] as $filter) {
                    $query->where($filter['field'], $filter['operator'], $filter['value']);
                }
            }

            // Build query based on chart type
            switch ($config['chartType']) {
                case 'bar':
                case 'line':
                case 'area':
                    $data = $this->buildSeriesChart($query, $config);
                    break;

                case 'pie':
                case 'donut':
                    $data = $this->buildPieChart($query, $config);
                    break;

                case 'table':
                    $data = $this->buildTableData($query, $config);
                    break;

                default:
                    return response()->json(['error' => 'Unsupported chart type'], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'config' => $config
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function buildSeriesChart($query, $config)
    {
        // For aggregated data (COUNT, SUM, AVG)
        if ($config['yAxis'] === 'count') {
            $results = $query->select($config['xAxis'])
                ->selectRaw('COUNT(*) as count')
                ->groupBy($config['xAxis'])
                ->orderBy('count', 'desc')
                ->limit(20)
                ->get();

            return [
                'categories' => $results->pluck($config['xAxis'])->toArray(),
                'series' => [
                    [
                        'name' => 'Count',
                        'data' => $results->pluck('count')->toArray()
                    ]
                ]
            ];
        } else {
            // For specific numeric fields
            $results = $query->select($config['xAxis'], $config['yAxis'])
                ->whereNotNull($config['xAxis'])
                ->whereNotNull($config['yAxis'])
                ->orderBy($config['yAxis'], 'desc')
                ->limit(20)
                ->get();

            return [
                'categories' => $results->pluck($config['xAxis'])->toArray(),
                'series' => [
                    [
                        'name' => ucfirst($config['yAxis']),
                        'data' => $results->pluck($config['yAxis'])->toArray()
                    ]
                ]
            ];
        }
    }

    private function buildPieChart($query, $config)
    {
        $results = $query->select($config['xAxis'])
            ->selectRaw('COUNT(*) as count')
            ->groupBy($config['xAxis'])
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $results->pluck($config['xAxis'])->toArray(),
            'series' => $results->pluck('count')->toArray()
        ];
    }

    private function buildTableData($query, $config)
    {
        $fields = [$config['xAxis'], $config['yAxis']];

        if ($config['groupBy']) {
            $fields[] = $config['groupBy'];
        }

        $results = $query->select($fields)
            ->limit(100)
            ->get();

        return [
            'columns' => array_map(function($field) {
                return ['key' => $field, 'label' => ucwords(str_replace('_', ' ', $field))];
            }, $fields),
            'rows' => $results->toArray()
        ];
    }

    /**
     * Get sample data for a specific table
     */
    public function getSampleData(Request $request, $table)
    {
        $allowedTables = ['assets', 'employees', 'pos_terminals', 'tickets', 'asset_requests'];

        if (!in_array($table, $allowedTables)) {
            return response()->json(['error' => 'Table not allowed'], 400);
        }

        $sampleData = DB::table($table)->limit(5)->get();

        return response()->json([
            'table' => $table,
            'sample' => $sampleData
        ]);
    }
}
