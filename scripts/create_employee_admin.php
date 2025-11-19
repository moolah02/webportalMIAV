<?php
/**
 * Script to create an admin Employee for auth (the app uses Employee as the auth model).
 * Run: php scripts/create_employee_admin.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\Role;
use App\Models\Permission;

$password = 'Rv@Adm1n2025!#';
$email = 'admin@miav.com';

try {
    if (Employee::where('email', $email)->exists()) {
        echo "EMPLOYEE_EXISTS\n";
        exit(0);
    }

    // Ensure role exists (use the project's Role model)
    // Create role with only columns that exist in `roles` table
    $role = Role::firstOrCreate(
        ['name' => 'super_admin'],
        ['display_name' => 'Super Admin', 'description' => 'Full access role']
    );

    // Create a unique employee id/number
    $employeeId = 'EMP_ADMIN_' . time();
    $employeeNumber = 'EMPADM' . rand(1000, 9999);

    // Create using individual attribute assignment because `employee_id` is not mass-assignable
    $employee = new Employee();
    $employee->employee_id = $employeeId;
    $employee->employee_number = $employeeNumber;
    $employee->first_name = 'Admin';
    $employee->last_name = 'User';
    $employee->email = $email;
    $employee->password = bcrypt($password);
    $employee->department = 'IT';
    $employee->position = 'Administrator';
    $employee->role_id = $role->id;
    $employee->status = 'active';
    $employee->time_zone = 'UTC';
    $employee->language = 'en';
    $employee->save();

    echo "CREATED\n";
    echo "EMAIL={$email}\n";
    echo "PASSWORD={$password}\n";
    echo "EMPLOYEE_ID={$employee->employee_id}\n";
    echo "EMPLOYEE_NUMBER={$employee->employee_number}\n";
    exit(0);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
