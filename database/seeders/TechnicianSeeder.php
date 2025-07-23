<?php

// ==============================================
// 7. TECHNICIAN SEEDER
// File: database/seeders/TechnicianSeeder.php
// ==============================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Technician;
use App\Models\Employee;

class TechnicianSeeder extends Seeder
{
    public function run(): void
    {
        // Get technician employees (we created some in EmployeeSeeder)
        $techEmployees = Employee::whereHas('role', function($query) {
            $query->where('name', 'Technician');
        })->get();

        // Create technician profiles for existing technician employees
        foreach ($techEmployees as $employee) {
            Technician::create([
                'employee_id' => $employee->id,
                'employee_code' => 'TECH' . str_pad($employee->id, 3, '0', STR_PAD_LEFT),
                'specializations' => [
                    'POS Repair',
                    'Network Setup',
                    'Software Installation'
                ],
                'regions' => ['North', 'Central'],
                'availability_status' => 'available',
                'phone' => $employee->phone,
                'email' => $employee->email,
                'hire_date' => $employee->hire_date,
            ]);
        }

        // Create additional technicians
        Technician::factory(3)->available()->create();
    }
}
