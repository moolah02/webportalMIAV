<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'permissions' => ['all']
            ],
            [
                'name' => 'Manager',
                'permissions' => [
                    'manage_team',
                    'view_reports',
                    'approve_requests',
                    'manage_assets',
                    'view_dashboard',
                    'manage_departments'
                ]
            ],
            [
                'name' => 'Technician',
                'permissions' => [
                    'view_jobs',
                    'manage_jobs',
                    'create_reports',
                    'view_terminals',
                    'update_terminals',
                    'view_clients'
                ]
            ],
            [
                'name' => 'Employee',
                'permissions' => [
                    'view_own_data',
                    'request_assets',
                    'view_documents',
                    'update_profile'
                ]
            ],
            [
                'name' => 'Supervisor',
                'permissions' => [
                    'manage_team',
                    'view_reports',
                    'approve_minor_requests',
                    'view_dashboard'
                ]
            ]
        ];

        foreach ($roles as $role) {
            if (!isset($role['display_name'])) {
                $role['display_name'] = $role['name'];
            }
            // No json_encode here. Role casts `permissions` to array, so
            // encoding first stored a JSON string *inside* the JSON column -
            // it then read back as a string, and the roles screen died on
            // in_array() with "must be of type array, string given".

            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
