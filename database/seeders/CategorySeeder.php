<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Asset Categories
            [
                'type' => 'asset_category',
                'name' => 'Hardware',
                'slug' => 'hardware',
                'description' => 'Computer hardware, laptops, monitors, peripherals',
                'icon' => 'bi-laptop',
                'color' => '#2196f3',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'asset_category',
                'name' => 'Software',
                'slug' => 'software',
                'description' => 'Software licenses, applications, subscriptions',
                'icon' => 'bi-gear',
                'color' => '#4caf50',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'asset_category',
                'name' => 'Office Supplies',
                'slug' => 'office-supplies',
                'description' => 'Stationery, printing supplies, office equipment',
                'icon' => 'bi-pencil',
                'color' => '#ff9800',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'asset_category',
                'name' => 'Mobile Devices',
                'slug' => 'mobile-devices',
                'description' => 'Phones, tablets, mobile accessories',
                'icon' => 'bi-phone',
                'color' => '#9c27b0',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'type' => 'asset_category',
                'name' => 'Furniture',
                'slug' => 'furniture',
                'description' => 'Office furniture, chairs, desks',
                'icon' => 'bi-house',
                'color' => '#795548',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'type' => 'asset_category',
                'name' => 'Networking',
                'slug' => 'networking',
                'description' => 'Network equipment, cables, routers',
                'icon' => 'bi-wifi',
                'color' => '#607d8b',
                'is_active' => true,
                'sort_order' => 6,
            ],

            // Asset Status
            [
                'type' => 'asset_status',
                'name' => 'Available',
                'slug' => 'available',
                'description' => 'Asset is available for request',
                'icon' => 'bi-check-circle',
                'color' => '#4caf50',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'asset_status',
                'name' => 'Assigned',
                'slug' => 'assigned',
                'description' => 'Asset is assigned to an employee',
                'icon' => 'bi-person-check',
                'color' => '#2196f3',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'asset_status',
                'name' => 'In Maintenance',
                'slug' => 'in-maintenance',
                'description' => 'Asset is under maintenance',
                'icon' => 'bi-tools',
                'color' => '#ff9800',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'asset_status',
                'name' => 'Retired',
                'slug' => 'retired',
                'description' => 'Asset is retired and no longer in use',
                'icon' => 'bi-archive',
                'color' => '#9e9e9e',
                'is_active' => true,
                'sort_order' => 4,
            ],

            // Terminal Status
            [
                'type' => 'terminal_status',
                'name' => 'Active',
                'slug' => 'terminal-active',
                'description' => 'Terminal is active and operational',
                'icon' => 'bi-check-circle-fill',
                'color' => '#4caf50',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'terminal_status',
                'name' => 'Offline',
                'slug' => 'terminal-offline',
                'description' => 'Terminal is offline',
                'icon' => 'bi-x-circle-fill',
                'color' => '#f44336',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'terminal_status',
                'name' => 'Maintenance',
                'slug' => 'terminal-maintenance',
                'description' => 'Terminal is under maintenance',
                'icon' => 'bi-tools',
                'color' => '#ff9800',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'terminal_status',
                'name' => 'Faulty',
                'slug' => 'terminal-faulty',
                'description' => 'Terminal has issues',
                'icon' => 'bi-exclamation-triangle-fill',
                'color' => '#ff5722',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'type' => 'terminal_status',
                'name' => 'Decommissioned',
                'slug' => 'terminal-decommissioned',
                'description' => 'Terminal is decommissioned',
                'icon' => 'bi-archive-fill',
                'color' => '#9e9e9e',
                'is_active' => true,
                'sort_order' => 5,
            ],

            // Service Types
            [
                'type' => 'service_type',
                'name' => 'Installation',
                'slug' => 'service-installation',
                'description' => 'New terminal installation',
                'icon' => 'bi-plus-circle',
                'color' => '#4caf50',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'service_type',
                'name' => 'Maintenance',
                'slug' => 'service-maintenance',
                'description' => 'Regular maintenance service',
                'icon' => 'bi-gear',
                'color' => '#2196f3',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'service_type',
                'name' => 'Repair',
                'slug' => 'service-repair',
                'description' => 'Repair and troubleshooting',
                'icon' => 'bi-wrench',
                'color' => '#ff9800',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'service_type',
                'name' => 'Inspection',
                'slug' => 'service-inspection',
                'description' => 'Terminal inspection',
                'icon' => 'bi-search',
                'color' => '#9c27b0',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'type' => 'service_type',
                'name' => 'Decommission',
                'slug' => 'service-decommission',
                'description' => 'Terminal decommissioning',
                'icon' => 'bi-trash',
                'color' => '#f44336',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug'], 'type' => $category['type']],
                $category
            );
        }
    }
}
