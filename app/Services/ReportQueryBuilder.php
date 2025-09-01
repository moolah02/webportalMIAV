<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReportQueryBuilder
{
    // Allowlisted tables and columns
    private const ALLOWED_TABLES = [
        'pos_terminals' => [
            'id', 'terminal_id', 'client_id', 'region_id', 'merchant_name', 'merchant_contact_person',
            'merchant_phone', 'physical_address', 'city', 'province', 'area', 'business_type',
            'installation_date', 'terminal_model', 'serial_number', 'status', 'last_service_date',
            'created_at', 'updated_at', 'current_status', 'deployment_status'
        ],
        'clients' => [
            'id', 'client_code', 'company_name', 'email', 'phone', 'address', 'city', 'region',
            'contract_start_date', 'contract_end_date', 'status', 'priority', 'created_at', 'updated_at'
        ],
        'regions' => [
            'id', 'name', 'description', 'region_code', 'is_active', 'created_at', 'updated_at'
        ],
        'visits' => [
            'id', 'merchant_id', 'merchant_name', 'employee_id', 'assignment_id', 'completed_at',
            'contact_person', 'phone_number', 'visit_summary', 'action_points', 'created_at', 'updated_at'
        ],
        'visit_terminals' => [
            'id', 'visit_id', 'terminal_id', 'status', 'condition', 'serial_number', 'device_type',
            'comments', 'created_at', 'updated_at'
        ],
        'tickets' => [
            'id', 'ticket_id', 'technician_id', 'pos_terminal_id', 'client_id', 'visit_id',
            'issue_type', 'priority', 'title', 'description', 'status', 'assigned_to',
            'created_at', 'updated_at', 'resolved_at'
        ],
        'job_assignments' => [
            'id', 'assignment_id', 'technician_id', 'region_id', 'client_id', 'project_id',
            'scheduled_date', 'service_type', 'priority', 'status', 'created_at', 'updated_at'
        ],
        'projects' => [
            'id', 'project_code', 'project_name', 'client_id', 'project_type', 'description',
            'start_date', 'end_date', 'status', 'priority', 'created_by', 'created_at', 'updated_at'
        ]
    ];

    // Allowlisted joins
    private const ALLOWED_JOINS = [
        'pos_terminals.client_id = clients.id',
        'pos_terminals.region_id = regions.id',
        'visit_terminals.visit_id = visits.id',
        'visit_terminals.terminal_id = pos_terminals.id',
        'visits.client_id = clients.id',
        'tickets.pos_terminal_id = pos_terminals.id',
        'tickets.client_id = clients.id',
        'job_assignments.project_id = projects.id',
        'job_assignments.client_id = clients.id'
    ];

    private const AGGREGATE_FUNCTIONS = ['COUNT', 'SUM', 'AVG', 'MIN', 'MAX'];

    public function buildQuery(array $config): array
    {
        $this->validateConfig($config);

        $query = DB::table($config['base']['table']);

        // Apply joins
        if (!empty($config['joins'])) {
            foreach ($config['joins'] as $join) {
                $this->applyJoin($query, $join);
            }
        }

        // Build select fields
        $selectFields = [];
        foreach ($config['select'] as $field) {
            $selectFields[] = $this->buildSelectField($field);
        }
        $query->select($selectFields);

        // Apply grouping
        if (!empty($config['group_by'])) {
            $query->groupBy($config['group_by']);
        }

        // Apply ordering
        if (!empty($config['order_by'])) {
            foreach ($config['order_by'] as $order) {
                $query->orderBy($order['expr'], $order['dir'] ?? 'ASC');
            }
        }

        // Apply limit
        $limit = min($config['limit'] ?? 100, 10000);
        if (!empty($config['download_all']) && $config['download_all']) {
            $limit = null; // No limit for downloads
        }

        if ($limit) {
            $query->limit($limit);
        }

        return [
            'query' => $query,
            'sql' => $query->toSql()
        ];
    }

    private function validateConfig(array $config): void
    {
        if (!isset($config['base']['table'])) {
            throw new InvalidArgumentException('Base table is required');
        }

        if (!array_key_exists($config['base']['table'], self::ALLOWED_TABLES)) {
            throw new InvalidArgumentException('Invalid base table: ' . $config['base']['table']);
        }

        if (empty($config['select'])) {
            throw new InvalidArgumentException('Select fields are required');
        }

        foreach ($config['select'] as $field) {
            $this->validateSelectField($field);
        }

        if (!empty($config['joins'])) {
            foreach ($config['joins'] as $join) {
                $this->validateJoin($join);
            }
        }
    }

    private function validateSelectField(array $field): void
    {
        if (isset($field['aggregate'])) {
            if (!in_array(strtoupper($field['aggregate']), self::AGGREGATE_FUNCTIONS)) {
                throw new InvalidArgumentException('Invalid aggregate function: ' . $field['aggregate']);
            }
        }

        if (strpos($field['expr'], '.') !== false) {
            [$table, $column] = explode('.', $field['expr'], 2);

            if (!array_key_exists($table, self::ALLOWED_TABLES)) {
                throw new InvalidArgumentException('Invalid table in select: ' . $table);
            }

            if (!in_array($column, self::ALLOWED_TABLES[$table])) {
                throw new InvalidArgumentException('Invalid column in select: ' . $field['expr']);
            }
        }
    }

    private function validateJoin(array $join): void
    {
        if (!isset($join['on'])) {
            throw new InvalidArgumentException('Join condition is required');
        }

        if (!in_array($join['on'], self::ALLOWED_JOINS)) {
            throw new InvalidArgumentException('Join not allowed: ' . $join['on']);
        }
    }

    private function applyJoin($query, array $join): void
    {
        $type = $join['type'] ?? 'inner';
        [$leftSide, $rightSide] = explode(' = ', $join['on']);
        [$rightTable] = explode('.', $rightSide);

        switch (strtolower($type)) {
            case 'left':
                $query->leftJoin($rightTable, $leftSide, '=', $rightSide);
                break;
            case 'right':
                $query->rightJoin($rightTable, $leftSide, '=', $rightSide);
                break;
            default:
                $query->join($rightTable, $leftSide, '=', $rightSide);
                break;
        }
    }

    private function buildSelectField(array $field): string
    {
        if (isset($field['aggregate'])) {
            $expr = strtoupper($field['aggregate']) . '(' . $field['expr'] . ')';
        } else {
            $expr = $field['expr'];
        }

        if (isset($field['as'])) {
            $expr .= ' as `' . $field['as'] . '`';
        }

        return $expr;
    }

    public function getAvailableFields(): array
    {
        $fields = [];

        foreach (self::ALLOWED_TABLES as $table => $columns) {
            $tableFields = [];

            foreach ($columns as $column) {
                $type = $this->getColumnType($column);
                $tableFields[] = [
                    'name' => $column,
                    'expression' => "{$table}.{$column}",
                    'type' => $type,
                    'category' => $this->getFieldCategory($type),
                    'label' => $this->humanizeColumnName($column)
                ];
            }

            $fields[$table] = [
                'label' => $this->humanizeTableName($table),
                'fields' => $tableFields
            ];
        }

        return $fields;
    }

    private function getColumnType(string $column): string
    {
        $dateColumns = ['created_at', 'updated_at', 'installation_date', 'last_service_date',
                       'contract_start_date', 'contract_end_date', 'scheduled_date',
                       'start_date', 'end_date', 'completed_at', 'resolved_at'];

        $numericColumns = ['id', 'client_id', 'region_id', 'employee_id', 'technician_id'];

        $enumColumns = ['status', 'current_status', 'deployment_status', 'priority',
                       'project_type', 'service_type', 'issue_type'];

        if (in_array($column, $dateColumns)) {
            return 'date';
        }
        if (in_array($column, $numericColumns)) {
            return 'numeric';
        }
        if (in_array($column, $enumColumns)) {
            return 'enum';
        }

        return 'text';
    }

    private function getFieldCategory(string $type): string
    {
        return in_array($type, ['numeric']) ? 'measures' : 'dimensions';
    }

    private function humanizeColumnName(string $column): string
    {
        return ucwords(str_replace('_', ' ', $column));
    }

    private function humanizeTableName(string $table): string
    {
        $labels = [
            'pos_terminals' => 'POS Terminals',
            'clients' => 'Clients',
            'regions' => 'Regions',
            'visits' => 'Visits',
            'visit_terminals' => 'Visit Terminals',
            'tickets' => 'Tickets',
            'job_assignments' => 'Job Assignments',
            'projects' => 'Projects'
        ];

        return $labels[$table] ?? ucwords(str_replace('_', ' ', $table));
    }

    public function getFilterOptions(): array
    {
        return [
            'date_ranges' => [
                'today' => 'Today',
                'last_7_days' => 'Last 7 Days',
                'last_30_days' => 'Last 30 Days',
                'this_month' => 'This Month',
                'custom' => 'Custom Range'
            ],
            'regions' => DB::table('regions')->where('is_active', 1)->pluck('name', 'id'),
            'clients' => DB::table('clients')->where('status', 'active')->pluck('company_name', 'id'),
            'projects' => DB::table('projects')->where('status', 'active')->pluck('project_name', 'id')
        ];
    }
}
