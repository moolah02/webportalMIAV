<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetEmployeePasswords extends Command
{
    protected $signature = 'employees:reset-passwords
                            {--password=Revival@67 : The password to set for all employees}
                            {--emails=* : Specific employee emails to reset}';

    protected $description = 'Reset passwords for specific employees or all employees';

    public function handle()
    {
        $password = $this->option('password');
        $emails = $this->option('emails');

        $query = Employee::query();

        if (!empty($emails)) {
            $query->whereIn('email', $emails);
            $this->info('Resetting passwords for specified employees...');
        } else {
            $this->info('Resetting passwords for ALL employees...');
            if (!$this->confirm('This will reset passwords for ALL employees. Continue?')) {
                $this->error('Operation cancelled.');
                return 1;
            }
        }

        $employees = $query->get();

        if ($employees->isEmpty()) {
            $this->error('No employees found matching the criteria.');
            return 1;
        }

        $hashedPassword = Hash::make($password);
        $count = 0;

        foreach ($employees as $employee) {
            $employee->password = $hashedPassword;
            $employee->save();
            $this->line("✓ Reset password for: {$employee->email}");
            $count++;
        }

        $this->info("\nSuccessfully reset passwords for {$count} employee(s).");
        $this->info("New password: {$password}");

        return 0;
    }
}
