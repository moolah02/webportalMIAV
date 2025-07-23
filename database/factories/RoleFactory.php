<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $roles = [
            'Admin' => ['all'],
            'Manager' => ['manage_team', 'view_reports', 'approve_requests', 'manage_assets'],
            'Technician' => ['view_jobs', 'update_jobs', 'create_reports', 'view_terminals'],
            'Employee' => ['view_own_data', 'request_assets', 'view_documents'],
            'Supervisor' => ['manage_team', 'view_reports', 'approve_minor_requests'],
        ];

        $roleName = $this->faker->randomElement(array_keys($roles));
        
        return [
            'name' => $roleName,
            'permissions' => $roles[$roleName],
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Admin',
                'permissions' => ['all'],
            ];
        });
    }

    public function manager()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Manager',
                'permissions' => ['manage_team', 'view_reports', 'approve_requests', 'manage_assets'],
            ];
        });
    }

    public function technician()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Technician',
                'permissions' => ['view_jobs', 'update_jobs', 'create_reports', 'view_terminals'],
            ];
        });
    }

    public function employee()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Employee',
                'permissions' => ['view_own_data', 'request_assets', 'view_documents'],
            ];
        });
    }
}