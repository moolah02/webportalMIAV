<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $seeds = [
            // ── Ticket Issue Types ───────────────────────────────────
            ['type' => 'ticket_issue_type', 'name' => 'Hardware Malfunction',  'slug' => 'hardware_malfunction',  'color' => '#ef4444', 'icon' => '🔧', 'sort_order' => 1],
            ['type' => 'ticket_issue_type', 'name' => 'Software Issue',        'slug' => 'software_issue',        'color' => '#f59e0b', 'icon' => '💻', 'sort_order' => 2],
            ['type' => 'ticket_issue_type', 'name' => 'Network Connectivity',  'slug' => 'network_connectivity',  'color' => '#3b82f6', 'icon' => '📶', 'sort_order' => 3],
            ['type' => 'ticket_issue_type', 'name' => 'User Training',         'slug' => 'user_training',         'color' => '#8b5cf6', 'icon' => '🎓', 'sort_order' => 4],
            ['type' => 'ticket_issue_type', 'name' => 'Maintenance Required',  'slug' => 'maintenance_required',  'color' => '#f97316', 'icon' => '⚙️', 'sort_order' => 5],
            ['type' => 'ticket_issue_type', 'name' => 'Replacement Needed',    'slug' => 'replacement_needed',    'color' => '#dc2626', 'icon' => '🔄', 'sort_order' => 6],
            ['type' => 'ticket_issue_type', 'name' => 'Other',                 'slug' => 'other',                 'color' => '#6b7280', 'icon' => '📋', 'sort_order' => 7],

            // ── Terminal Models / Brands ─────────────────────────────
            ['type' => 'terminal_model', 'name' => 'Verifone V240m',   'slug' => 'verifone_v240m',   'color' => '#1a3a5c', 'icon' => '🖥️', 'sort_order' => 1],
            ['type' => 'terminal_model', 'name' => 'Ingenico Move 5000','slug' => 'ingenico_move5000','color' => '#1a3a5c', 'icon' => '🖥️', 'sort_order' => 2],
            ['type' => 'terminal_model', 'name' => 'PAX A920',          'slug' => 'pax_a920',         'color' => '#1a3a5c', 'icon' => '🖥️', 'sort_order' => 3],
            ['type' => 'terminal_model', 'name' => 'Sunmi P2',          'slug' => 'sunmi_p2',         'color' => '#1a3a5c', 'icon' => '🖥️', 'sort_order' => 4],
            ['type' => 'terminal_model', 'name' => 'Telpo TPS900',      'slug' => 'telpo_tps900',     'color' => '#1a3a5c', 'icon' => '🖥️', 'sort_order' => 5],
            ['type' => 'terminal_model', 'name' => 'Other',             'slug' => 'terminal_model_other','color' => '#6b7280', 'icon' => '🖥️', 'sort_order' => 99],

            // ── Employee Positions ───────────────────────────────────
            ['type' => 'employee_position', 'name' => 'Field Technician',      'slug' => 'field_technician',      'color' => '#0891b2', 'icon' => '🔧', 'sort_order' => 1],
            ['type' => 'employee_position', 'name' => 'Senior Technician',     'slug' => 'senior_technician',     'color' => '#0891b2', 'icon' => '🔧', 'sort_order' => 2],
            ['type' => 'employee_position', 'name' => 'Field Engineer',        'slug' => 'field_engineer',        'color' => '#0e7490', 'icon' => '⚙️', 'sort_order' => 3],
            ['type' => 'employee_position', 'name' => 'Team Lead',             'slug' => 'team_lead',             'color' => '#1d4ed8', 'icon' => '👤', 'sort_order' => 4],
            ['type' => 'employee_position', 'name' => 'Operations Manager',    'slug' => 'operations_manager',    'color' => '#1a3a5c', 'icon' => '👔', 'sort_order' => 5],
            ['type' => 'employee_position', 'name' => 'Account Manager',       'slug' => 'account_manager',       'color' => '#7c3aed', 'icon' => '📊', 'sort_order' => 6],
            ['type' => 'employee_position', 'name' => 'System Administrator',  'slug' => 'system_administrator',  'color' => '#dc2626', 'icon' => '🛡️', 'sort_order' => 7],
            ['type' => 'employee_position', 'name' => 'Support Specialist',    'slug' => 'support_specialist',    'color' => '#059669', 'icon' => '🎧', 'sort_order' => 8],

            // ── Client Industries ────────────────────────────────────
            ['type' => 'client_industry', 'name' => 'Retail',          'slug' => 'retail',          'color' => '#f59e0b', 'icon' => '🛍️', 'sort_order' => 1],
            ['type' => 'client_industry', 'name' => 'Banking & Finance','slug' => 'banking_finance', 'color' => '#1a3a5c', 'icon' => '🏦', 'sort_order' => 2],
            ['type' => 'client_industry', 'name' => 'Hospitality',     'slug' => 'hospitality',     'color' => '#f97316', 'icon' => '🏨', 'sort_order' => 3],
            ['type' => 'client_industry', 'name' => 'Healthcare',      'slug' => 'healthcare',      'color' => '#10b981', 'icon' => '🏥', 'sort_order' => 4],
            ['type' => 'client_industry', 'name' => 'Education',       'slug' => 'education',       'color' => '#8b5cf6', 'icon' => '🎓', 'sort_order' => 5],
            ['type' => 'client_industry', 'name' => 'Government',      'slug' => 'government',      'color' => '#374151', 'icon' => '🏛️', 'sort_order' => 6],
            ['type' => 'client_industry', 'name' => 'Telecommunications','slug' => 'telecommunications','color' => '#3b82f6', 'icon' => '📡', 'sort_order' => 7],
            ['type' => 'client_industry', 'name' => 'Manufacturing',   'slug' => 'manufacturing',   'color' => '#78716c', 'icon' => '🏭', 'sort_order' => 8],
            ['type' => 'client_industry', 'name' => 'Transport & Logistics','slug' => 'transport_logistics','color' => '#0891b2', 'icon' => '🚚', 'sort_order' => 9],
            ['type' => 'client_industry', 'name' => 'Other',           'slug' => 'industry_other',  'color' => '#6b7280', 'icon' => '🏢', 'sort_order' => 99],

            // ── Project Types ────────────────────────────────────────
            ['type' => 'project_type', 'name' => 'Terminal Deployment', 'slug' => 'terminal_deployment', 'color' => '#1a3a5c', 'icon' => '🖥️', 'sort_order' => 1],
            ['type' => 'project_type', 'name' => 'System Integration',  'slug' => 'system_integration',  'color' => '#0891b2', 'icon' => '🔗', 'sort_order' => 2],
            ['type' => 'project_type', 'name' => 'Infrastructure',      'slug' => 'infrastructure',      'color' => '#374151', 'icon' => '🏗️', 'sort_order' => 3],
            ['type' => 'project_type', 'name' => 'Maintenance Contract','slug' => 'maintenance_contract','color' => '#f59e0b', 'icon' => '🔧', 'sort_order' => 4],
            ['type' => 'project_type', 'name' => 'Client Onboarding',   'slug' => 'client_onboarding',   'color' => '#10b981', 'icon' => '🤝', 'sort_order' => 5],
            ['type' => 'project_type', 'name' => 'Audit & Compliance',  'slug' => 'audit_compliance',    'color' => '#7c3aed', 'icon' => '📋', 'sort_order' => 6],
            ['type' => 'project_type', 'name' => 'Internal',            'slug' => 'project_internal',    'color' => '#6b7280', 'icon' => '🏠', 'sort_order' => 7],

            // ── Visit Purposes ───────────────────────────────────────
            ['type' => 'visit_purpose', 'name' => 'Routine Maintenance', 'slug' => 'routine_maintenance',  'color' => '#0891b2', 'icon' => '🔧', 'sort_order' => 1],
            ['type' => 'visit_purpose', 'name' => 'Emergency Repair',    'slug' => 'visit_emergency_repair','color' => '#ef4444', 'icon' => '🚨', 'sort_order' => 2],
            ['type' => 'visit_purpose', 'name' => 'Installation',        'slug' => 'visit_installation',   'color' => '#10b981', 'icon' => '📦', 'sort_order' => 3],
            ['type' => 'visit_purpose', 'name' => 'Terminal Swap',       'slug' => 'terminal_swap',        'color' => '#f97316', 'icon' => '🔄', 'sort_order' => 4],
            ['type' => 'visit_purpose', 'name' => 'User Training',       'slug' => 'visit_user_training',  'color' => '#8b5cf6', 'icon' => '🎓', 'sort_order' => 5],
            ['type' => 'visit_purpose', 'name' => 'Inspection / Audit',  'slug' => 'visit_inspection',     'color' => '#374151', 'icon' => '🔍', 'sort_order' => 6],
            ['type' => 'visit_purpose', 'name' => 'Decommission',        'slug' => 'visit_decommission',   'color' => '#6b7280', 'icon' => '🗑️', 'sort_order' => 7],

            // ── Business / Merchant Types ────────────────────────────
            ['type' => 'business_type', 'name' => 'Supermarket',        'slug' => 'supermarket',        'color' => '#10b981', 'icon' => '🛒', 'sort_order' => 1],
            ['type' => 'business_type', 'name' => 'Restaurant / Café',  'slug' => 'restaurant_cafe',    'color' => '#f97316', 'icon' => '🍽️', 'sort_order' => 2],
            ['type' => 'business_type', 'name' => 'Fuel Station',       'slug' => 'fuel_station',       'color' => '#f59e0b', 'icon' => '⛽', 'sort_order' => 3],
            ['type' => 'business_type', 'name' => 'Pharmacy',           'slug' => 'pharmacy',           'color' => '#10b981', 'icon' => '💊', 'sort_order' => 4],
            ['type' => 'business_type', 'name' => 'Hotel / Lodge',      'slug' => 'hotel_lodge',        'color' => '#8b5cf6', 'icon' => '🏨', 'sort_order' => 5],
            ['type' => 'business_type', 'name' => 'Bank / ATM Agent',   'slug' => 'bank_atm_agent',     'color' => '#1a3a5c', 'icon' => '🏦', 'sort_order' => 6],
            ['type' => 'business_type', 'name' => 'Wholesale / Distributor','slug' => 'wholesale_distributor','color' => '#0891b2', 'icon' => '📦', 'sort_order' => 7],
            ['type' => 'business_type', 'name' => 'School / University', 'slug' => 'school_university',  'color' => '#7c3aed', 'icon' => '🎓', 'sort_order' => 8],
            ['type' => 'business_type', 'name' => 'Government Office',   'slug' => 'government_office',  'color' => '#374151', 'icon' => '🏛️', 'sort_order' => 9],
            ['type' => 'business_type', 'name' => 'Other',               'slug' => 'business_type_other','color' => '#6b7280', 'icon' => '🏢', 'sort_order' => 99],
        ];

        foreach ($seeds as $seed) {
            DB::table('categories')->insertOrIgnore(array_merge($seed, [
                'description' => null,
                'is_active'   => 1,
                'metadata'    => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]));
        }
    }

    public function down(): void
    {
        $types = [
            'ticket_issue_type', 'terminal_model', 'employee_position',
            'client_industry', 'project_type', 'visit_purpose', 'business_type',
        ];
        DB::table('categories')->whereIn('type', $types)->delete();
    }
};
