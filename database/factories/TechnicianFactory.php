<?php

// 3. TECHNICIAN FACTORY
// File: database/factories/TechnicianFactory.php
// ==============================================

namespace Database\Factories;

use App\Models\Technician;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class TechnicianFactory extends Factory
{
    protected $model = Technician::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'employee_code' => 'TECH' . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'specializations' => $this->faker->randomElements([
                'POS Repair',
                'Network Setup',
                'Software Installation',
                'Hardware Maintenance',
                'Training',
                'Troubleshooting'
            ], rand(2, 4)),
            'regions' => $this->faker->randomElements(['North', 'South', 'East', 'West', 'Central'], rand(1, 3)),
            'availability_status' => $this->faker->randomElement(['available', 'busy', 'off_duty']),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'hire_date' => $this->faker->dateTimeBetween('-3 years', '-6 months'),
        ];
    }

    public function available()
    {
        return $this->state(function (array $attributes) {
            return [
                'availability_status' => 'available',
            ];
        });
    }
}