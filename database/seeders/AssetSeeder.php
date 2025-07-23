<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            // Hardware
            [
                'name' => 'Dell Latitude 5420 Laptop',
                'description' => 'Business laptop with Intel i5, 8GB RAM, 256GB SSD',
                'category' => 'Hardware',
                'brand' => 'Dell',
                'model' => 'Latitude 5420',
                'unit_price' => 899.00,
                'currency' => 'USD',
                'stock_quantity' => 15,
                'min_stock_level' => 5,
                'sku' => 'DELL-LAT-5420',
                'specifications' => [
                    'processor' => 'Intel Core i5-1135G7',
                    'memory' => '8GB DDR4',
                    'storage' => '256GB SSD',
                    'display' => '14-inch FHD',
                    'os' => 'Windows 11 Pro'
                ],
                'is_requestable' => true,
                'requires_approval' => true,
            ],
            [
                'name' => 'HP 24" Monitor',
                'description' => 'Full HD IPS monitor with USB-C connectivity',
                'category' => 'Hardware',
                'brand' => 'HP',
                'model' => 'E24 G5',
                'unit_price' => 189.00,
                'currency' => 'USD',
                'stock_quantity' => 25,
                'min_stock_level' => 8,
                'sku' => 'HP-MON-E24G5',
                'specifications' => [
                    'size' => '24 inches',
                    'resolution' => '1920x1080',
                    'panel_type' => 'IPS',
                    'connectivity' => 'USB-C, HDMI, DisplayPort'
                ],
                'is_requestable' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Logitech MX Master 3 Mouse',
                'description' => 'Wireless productivity mouse with advanced scroll wheel',
                'category' => 'Hardware',
                'brand' => 'Logitech',
                'model' => 'MX Master 3',
                'unit_price' => 99.99,
                'currency' => 'USD',
                'stock_quantity' => 30,
                'min_stock_level' => 10,
                'sku' => 'LOG-MX-MASTER3',
                'specifications' => [
                    'connectivity' => 'Bluetooth, USB Receiver',
                    'battery' => '70 days',
                    'dpi' => '4000 DPI'
                ],
                'is_requestable' => true,
                'requires_approval' => false,
            ],

            // Software
            [
                'name' => 'Microsoft Office 365 Business',
                'description' => 'Complete productivity suite with cloud storage',
                'category' => 'Software',
                'brand' => 'Microsoft',
                'model' => 'Office 365 Business',
                'unit_price' => 12.50,
                'currency' => 'USD',
                'stock_quantity' => 100,
                'min_stock_level' => 20,
                'sku' => 'MS-O365-BUS',
                'specifications' => [
                    'license_type' => 'Subscription',
                    'duration' => '1 month',
                    'users' => '1 user',
                    'includes' => 'Word, Excel, PowerPoint, Outlook, OneDrive'
                ],
                'is_requestable' => true,
                'requires_approval' => true,
            ],
            [
                'name' => 'Adobe Creative Cloud',
                'description' => 'Complete creative software suite',
                'category' => 'Software',
                'brand' => 'Adobe',
                'model' => 'Creative Cloud All Apps',
                'unit_price' => 52.99,
                'currency' => 'USD',
                'stock_quantity' => 10,
                'min_stock_level' => 2,
                'sku' => 'ADOBE-CC-ALL',
                'specifications' => [
                    'license_type' => 'Subscription',
                    'duration' => '1 month',
                    'includes' => 'Photoshop, Illustrator, InDesign, Premiere Pro, After Effects'
                ],
                'is_requestable' => true,
                'requires_approval' => true,
            ],

            // Mobile Devices
            [
                'name' => 'iPhone 15',
                'description' => 'Latest iPhone with advanced camera system',
                'category' => 'Mobile Devices',
                'brand' => 'Apple',
                'model' => 'iPhone 15',
                'unit_price' => 799.00,
                'currency' => 'USD',
                'stock_quantity' => 8,
                'min_stock_level' => 2,
                'sku' => 'APPLE-IP15-128',
                'specifications' => [
                    'storage' => '128GB',
                    'display' => '6.1-inch Super Retina XDR',
                    'camera' => '48MP Main camera',
                    'connectivity' => '5G, Wi-Fi 6, Bluetooth 5.3'
                ],
                'is_requestable' => true,
                'requires_approval' => true,
            ],

            // Office Supplies
            [
                'name' => 'A4 Printer Paper (500 sheets)',
                'description' => 'High-quality white printer paper',
                'category' => 'Office Supplies',
                'brand' => 'Generic',
                'model' => 'A4-80GSM',
                'unit_price' => 4.99,
                'currency' => 'USD',
                'stock_quantity' => 200,
                'min_stock_level' => 50,
                'sku' => 'PAPER-A4-500',
                'specifications' => [
                    'size' => 'A4',
                    'weight' => '80gsm',
                    'sheets' => '500',
                    'color' => 'White'
                ],
                'is_requestable' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Pilot G2 Pens (Pack of 12)',
                'description' => 'Smooth writing gel pens - black ink',
                'category' => 'Office Supplies',
                'brand' => 'Pilot',
                'model' => 'G2',
                'unit_price' => 18.99,
                'currency' => 'USD',
                'stock_quantity' => 75,
                'min_stock_level' => 20,
                'sku' => 'PILOT-G2-12PK',
                'specifications' => [
                    'ink_color' => 'Black',
                    'tip_size' => '0.7mm',
                    'quantity' => '12 pens'
                ],
                'is_requestable' => true,
                'requires_approval' => false,
            ],

            // Furniture
            [
                'name' => 'Ergonomic Office Chair',
                'description' => 'Adjustable office chair with lumbar support',
                'category' => 'Furniture',
                'brand' => 'Herman Miller',
                'model' => 'Aeron',
                'unit_price' => 1395.00,
                'currency' => 'USD',
                'stock_quantity' => 5,
                'min_stock_level' => 1,
                'sku' => 'HM-AERON-MED',
                'specifications' => [
                    'size' => 'Medium',
                    'material' => 'Mesh',
                    'adjustments' => 'Height, Tilt, Armrests',
                    'warranty' => '12 years'
                ],
                'is_requestable' => true,
                'requires_approval' => true,
            ],

            // Networking
            [
                'name' => 'Ethernet Cable (Cat6 - 10ft)',
                'description' => 'High-speed ethernet cable for network connections',
                'category' => 'Networking',
                'brand' => 'Cable Matters',
                'model' => 'Cat6-10ft',
                'unit_price' => 8.99,
                'currency' => 'USD',
                'stock_quantity' => 50,
                'min_stock_level' => 15,
                'sku' => 'CAT6-10FT',
                'specifications' => [
                    'category' => 'Cat6',
                    'length' => '10 feet',
                    'speed' => '1000 Mbps',
                    'connector' => 'RJ45'
                ],
                'is_requestable' => true,
                'requires_approval' => false,
            ],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }
    }
}