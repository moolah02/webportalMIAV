<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Structure / reference data first
            DepartmentSeeder::class,
            RoleSeeder::class,
            PermissionRoleSeeder::class,
            CategorySeeder::class,
            AssetCategorySeeder::class,
            AssetCategoryFieldSeeder::class,
            RegionSeeder::class,

            // Users
            EmployeeSeeder::class,

            // Optional placeholder data (comment out for a true clean slate)
            // ClientSeeder::class,
            // PosTerminalSeeder::class,
            // TechnicianSeeder::class,
            // AssetSeeder::class,

            // Documentation — always restore
            DocPageSeeder::class,
        ]);
    }
}
