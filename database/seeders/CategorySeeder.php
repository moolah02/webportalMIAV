<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Asset Categories
            [
                'type' => 'asset_category',
                'name' => 'Computer Equipment',
                'slug' => 'computer-equipment',
                'description' => 'Laptops, desktops, monitors',
                'color' => '#2196F3',
                'icon' => 'computer',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'asset_category',
                'name' => 'Office Furniture',
                'slug' => 'office-furniture',
                'description' => 'Desks, chairs, cabinets',
                'color' => '#4CAF50',
                'icon' => 'chair',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'asset_category',
                'name' => 'Mobile Devices',
                'slug' => 'mobile-devices',
                'description' => 'Phones, tablets',
                'color' => '#FF9800',
                'icon' => 'phone',
                'is_active' => true,
                'sort_order' => 3,
            ],

            // Asset Statuses
            [
                'type' => 'asset_status',
                'name' => 'Available',
                'slug' => 'available',
                'description' => 'Ready for assignment',
                'color' => '#4CAF50',
                'icon' => 'check-circle',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'asset_status',
                'name' => 'In Use',
                'slug' => 'in-use',
                'description' => 'Currently assigned',
                'color' => '#2196F3',
                'icon' => 'user',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'asset_status',
                'name' => 'Under Repair',
                'slug' => 'under-repair',
                'description' => 'Being repaired',
                'color' => '#FF9800',
                'icon' => 'wrench',
                'is_active' => true,
                'sort_order' => 3,
            ],

            // Terminal Statuses
            [
                'type' => 'terminal_status',
                'name' => 'Active',
                'slug' => 'active',
                'description' => 'Terminal is operational',
                'color' => '#4CAF50',
                'icon' => 'check',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'terminal_status',
                'name' => 'Offline',
                'slug' => 'offline',
                'description' => 'Terminal is not responding',
                'color' => '#F44336',
                'icon' => 'x-circle',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'terminal_status',
                'name' => 'Maintenance',
                'slug' => 'maintenance',
                'description' => 'Under maintenance',
                'color' => '#FF9800',
                'icon' => 'tool',
                'is_active' => true,
                'sort_order' => 3,
            ],

            // Service Types
            [
                'type' => 'service_type',
                'name' => 'Installation',
                'slug' => 'installation',
                'description' => 'New terminal installation',
                'color' => '#2196F3',
                'icon' => 'download',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'service_type',
                'name' => 'Repair',
                'slug' => 'repair',
                'description' => 'Terminal repair service',
                'color' => '#FF9800',
                'icon' => 'wrench',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'service_type',
                'name' => 'Maintenance',
                'slug' => 'maintenance',
                'description' => 'Regular maintenance',
                'color' => '#4CAF50',
                'icon' => 'check',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
