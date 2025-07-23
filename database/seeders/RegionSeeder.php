<?php

// 4. REGION SEEDER
// File: database/seeders/RegionSeeder.php
// ==============================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            [
                'name' => 'North',
                'description' => 'Northern region covering areas like Westlands, Kasarani, and Thika Road'
            ],
            [
                'name' => 'South',
                'description' => 'Southern region covering areas like Lang\'ata, Karen, and Rongai'
            ],
            [
                'name' => 'East',
                'description' => 'Eastern region covering areas like Eastleigh, Donholm, and Embakasi'
            ],
            [
                'name' => 'West',
                'description' => 'Western region covering areas like Ngong Road, Kawangware, and Kikuyu'
            ],
            [
                'name' => 'Central',
                'description' => 'Central region covering CBD, Upper Hill, and surrounding areas'
            ],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}