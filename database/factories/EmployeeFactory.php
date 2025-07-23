<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_number' => 'EMP' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Default password
            'phone' => $this->faker->phoneNumber(),
            'department_id' => Department::factory(),
            'role_id' => Role::factory(),
            'manager_id' => null, // Will be set later in seeder
            'time_zone' => $this->faker->randomElement(['UTC', 'Africa/Harare', 'Africa/Johannesburg']),
            'language' => $this->faker->randomElement(['en', 'fr', 'es']),
            'two_factor_enabled' => $this->faker->boolean(20), // 20% chance
            'status' => $this->faker->randomElement(['active', 'inactive', 'suspended']),
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'last_login_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'remember_token' => Str::random(10),
        ];
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'email' => 'admin@miav.com',
                'employee_number' => 'EMP0001',
                'status' => 'active',
                'hire_date' => '2024-01-01',
            ];
        });
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

// ==============================================
// 7. DEPARTMENT SEEDER
// File: database/seeders/DepartmentSeeder.php
// ==============================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Information Technology',
                'description' => 'Manages all technology infrastructure, software development, and technical support'
            ],
            [
                'name' => 'Finance & Accounting',
                'description' => 'Handles financial planning, accounting, budgeting, and financial reporting'
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Manages employee relations, recruitment, training, and HR policies'
            ],
            [
                'name' => 'Operations',
                'description' => 'Oversees daily business operations, process improvement, and operational efficiency'
            ],
            [
                'name' => 'Marketing & Sales',
                'description' => 'Responsible for marketing strategies, sales operations, and customer acquisition'
            ],
            [
                'name' => 'Customer Service',
                'description' => 'Provides customer support, handles inquiries, and maintains customer satisfaction'
            ],
            [
                'name' => 'Quality Assurance',
                'description' => 'Ensures quality standards, conducts testing, and maintains compliance'
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}