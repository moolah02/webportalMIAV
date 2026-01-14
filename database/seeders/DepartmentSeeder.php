<?php

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
                'description' => 'IT and Technical Support',
            ],
            [
                'name' => 'Finance & Accounting',
                'description' => 'Financial Operations and Accounting',
            ],
            [
                'name' => 'Human Resources',
                'description' => 'HR and Employee Management',
            ],
            [
                'name' => 'Operations',
                'description' => 'Day-to-day Operations',
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
