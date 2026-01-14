<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // Get departments and roles
        $itDept = Department::where('name', 'Information Technology')->first();
        $financeDept = Department::where('name', 'Finance & Accounting')->first();
        $hrDept = Department::where('name', 'Human Resources')->first();
        $opsDept = Department::where('name', 'Operations')->first();

        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $techRole = Role::where('name', 'Technician')->first();
        $empRole = Role::where('name', 'Employee')->first();

        // Create default admin user
        $admin = Employee::create([
            'employee_id' => 'EMP0001',
            'employee_id' => 'EMP0001',
            'employee_number' => 'EMP0001',
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@miav.com',
            'password' => Hash::make('password'),
            'phone' => '+263712345678',
            'department_id' => $itDept->id,
            'role_id' => $adminRole->id,
            'time_zone' => 'Africa/Harare',
            'language' => 'en',
            'status' => 'active',
            'hire_date' => '2024-01-01',
            'email_verified_at' => now(),
        ]);

        // Create department managers
        $itManager = Employee::create([
            'employee_id' => 'EMP0002',
            'employee_number' => 'EMP0002',
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@miav.com',
            'password' => Hash::make('password'),
            'phone' => '+263712345679',
            'department_id' => $itDept->id,
            'role_id' => $managerRole->id,
            'manager_id' => $admin->id,
            'time_zone' => 'Africa/Harare',
            'language' => 'en',
            'status' => 'active',
            'hire_date' => '2024-01-15',
            'email_verified_at' => now(),
        ]);

        $financeManager = Employee::create([
            'employee_id' => 'EMP0003',
            'employee_number' => 'EMP0003',
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'sarah.johnson@miav.com',
            'password' => Hash::make('password'),
            'phone' => '+263712345680',
            'department_id' => $financeDept->id,
            'role_id' => $managerRole->id,
            'manager_id' => $admin->id,
            'time_zone' => 'Africa/Harare',
            'language' => 'en',
            'status' => 'active',
            'hire_date' => '2024-01-20',
            'email_verified_at' => now(),
        ]);

        // Create technicians
        $technician1 = Employee::create([
            'employee_id' => 'EMP0004',
            'employee_number' => 'EMP0004',
            'first_name' => 'Mike',
            'last_name' => 'Thompson',
            'email' => 'mike.thompson@miav.com',
            'password' => Hash::make('password'),
            'phone' => '+263712345681',
            'department_id' => $opsDept->id,
            'role_id' => $techRole->id,
            'manager_id' => $itManager->id,
            'time_zone' => 'Africa/Harare',
            'language' => 'en',
            'status' => 'active',
            'hire_date' => '2024-02-01',
            'email_verified_at' => now(),
        ]);

        $technician2 = Employee::create([
            'employee_id' => 'EMP0005',
            'employee_number' => 'EMP0005',
            'first_name' => 'Alex',
            'last_name' => 'Wilson',
            'email' => 'alex.wilson@miav.com',
            'password' => Hash::make('password'),
            'phone' => '+263712345682',
            'department_id' => $opsDept->id,
            'role_id' => $techRole->id,
            'manager_id' => $itManager->id,
            'time_zone' => 'Africa/Harare',
            'language' => 'en',
            'status' => 'active',
            'hire_date' => '2024-02-15',
            'email_verified_at' => now(),
        ]);

        // Create regular employees
        Employee::create([
            'employee_id' => 'EMP0006',
            'employee_number' => 'EMP0006',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@miav.com',
            'password' => Hash::make('password'),
            'phone' => '+263712345683',
            'department_id' => $hrDept->id,
            'role_id' => $empRole->id,
            'manager_id' => $financeManager->id,
            'time_zone' => 'Africa/Harare',
            'language' => 'en',
            'status' => 'active',
            'hire_date' => '2024-03-01',
            'email_verified_at' => now(),
        ]);

        // Create additional employees using factory
        Employee::factory(15)
            ->state(['status' => 'active'])
            ->create()
            ->each(function ($employee) use ($itManager, $financeManager) {
                // Assign random managers
                $employee->update([
                    'manager_id' => fake()->randomElement([$itManager->id, $financeManager->id, null])
                ]);
            });
    }
}