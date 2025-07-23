<?php

// ==============================================
// 8. UPDATE DATABASE SEEDER
// File: database/seeders/DatabaseSeeder.php
// ==============================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            RoleSeeder::class,
            EmployeeSeeder::class,
            RegionSeeder::class,
            ClientSeeder::class,
            PosTerminalSeeder::class,
            TechnicianSeeder::class,
        ]);
    }
}
