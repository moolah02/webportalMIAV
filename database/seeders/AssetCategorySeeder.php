<?php

// ==============================================
// 1. ASSET CATEGORY SEEDER
// File: database/seeders/AssetCategorySeeder.php
// ==============================================

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Hardware',
                'description' => 'Computer hardware, laptops, monitors, peripherals',
                'icon' => '💻',
                'color' => '#2196f3',
                'sort_order' => 1,
                'requires_individual_entry' => false,
            ],
            [
                'name' => 'Software',
                'description' => 'Software licenses, applications, subscriptions',
                'icon' => '⚙️',
                'color' => '#4caf50',
                'sort_order' => 2,
                'requires_individual_entry' => false,
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Stationery, printing supplies, office equipment',
                'icon' => '📝',
                'color' => '#ff9800',
                'sort_order' => 3,
                'requires_individual_entry' => false,
            ],
            [
                'name' => 'Mobile Devices',
                'description' => 'Phones, tablets, mobile accessories',
                'icon' => '📱',
                'color' => '#9c27b0',
                'sort_order' => 4,
                'requires_individual_entry' => false,
            ],
            [
                'name' => 'Furniture',
                'description' => 'Office furniture, chairs, desks',
                'icon' => '🪑',
                'color' => '#795548',
                'sort_order' => 5,
                'requires_individual_entry' => false,
            ],
            [
                'name' => 'Networking',
                'description' => 'Network equipment, cables, routers',
                'icon' => '🌐',
                'color' => '#607d8b',
                'sort_order' => 6,
                'requires_individual_entry' => false,
            ],
            [
                'name' => 'Vehicles',
                'description' => 'Company vehicles, cars, trucks, motorcycles',
                'icon' => '🚗',
                'color' => '#f44336',
                'sort_order' => 7,
                'requires_individual_entry' => true,
            ],
            [
                'name' => 'IT Equipment',
                'description' => 'IT equipment with unique serial numbers',
                'icon' => '🖥️',
                'color' => '#673ab7',
                'sort_order' => 8,
                'requires_individual_entry' => true,
            ],
        ];

        foreach ($categories as $category) {
            AssetCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
