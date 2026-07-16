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
            ['name' => 'Harare',              'description' => 'Harare Metropolitan Province — capital city and surrounding areas'],
            ['name' => 'Bulawayo',            'description' => 'Bulawayo Metropolitan Province — second largest city'],
            ['name' => 'Manicaland',          'description' => 'Eastern province including Mutare, Chipinge, and Nyanga'],
            ['name' => 'Mashonaland Central', 'description' => 'North-central province including Bindura and Shamva'],
            ['name' => 'Mashonaland East',    'description' => 'East of Harare including Marondera, Mutoko, and Goromonzi'],
            ['name' => 'Mashonaland West',    'description' => 'Northwest province including Chinhoyi, Karoi, and Kadoma'],
            ['name' => 'Masvingo',            'description' => 'Southern province including Masvingo city, Great Zimbabwe, and Chiredzi'],
            ['name' => 'Matabeleland North',  'description' => 'Northwest province including Victoria Falls, Hwange, and Lupane'],
            ['name' => 'Matabeleland South',  'description' => 'Southwest province including Gwanda, Beitbridge, and Plumtree'],
            ['name' => 'Midlands',            'description' => 'Central province including Gweru, Kwekwe, Shurugwi, and Zvishavane'],
        ];

        foreach ($regions as $region) {
            Region::firstOrCreate(['name' => $region['name']], $region);
        }
    }
}