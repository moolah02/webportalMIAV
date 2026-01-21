<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssetCategory;
use App\Models\AssetCategoryField;
use Illuminate\Support\Facades\DB;

class AssetCategoryFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing fields
        DB::table('asset_category_fields')->truncate();

        // Get categories
        $vehicleCategory = AssetCategory::where('name', 'Vehicles')->first();
        $furnitureCategory = AssetCategory::where('name', 'Furniture')->first();
        $softwareCategory = AssetCategory::where('name', 'Software')->first();
        $itCategory = AssetCategory::where('name', 'IT Equipment')->first();

        // Vehicle Category Fields (simplified as per user request)
        if ($vehicleCategory) {
            $vehicleFields = [
                [
                    'field_name' => 'license_plate',
                    'field_label' => 'License Plate / Number Plate',
                    'field_type' => 'text',
                    'is_required' => true,
                    'placeholder_text' => 'e.g., KAA 123A',
                    'help_text' => 'Vehicle license plate number',
                    'display_order' => 1,
                ],
                [
                    'field_name' => 'make',
                    'field_label' => 'Make',
                    'field_type' => 'text',
                    'is_required' => true,
                    'placeholder_text' => 'e.g., Toyota, Nissan',
                    'display_order' => 2,
                ],
                [
                    'field_name' => 'model',
                    'field_label' => 'Model',
                    'field_type' => 'text',
                    'is_required' => true,
                    'placeholder_text' => 'e.g., Hilux, Patrol',
                    'display_order' => 3,
                ],
                [
                    'field_name' => 'year',
                    'field_label' => 'Year',
                    'field_type' => 'number',
                    'is_required' => true,
                    'validation_rules' => json_encode(['min' => 1900, 'max' => 2030]),
                    'placeholder_text' => 'e.g., 2023',
                    'display_order' => 4,
                ],
                [
                    'field_name' => 'fuel_type',
                    'field_label' => 'Fuel Type',
                    'field_type' => 'select',
                    'is_required' => false,
                    'options' => json_encode(['Petrol', 'Diesel', 'Electric', 'Hybrid']),
                    'display_order' => 5,
                ],
                [
                    'field_name' => 'registration_date',
                    'field_label' => 'Registration Date',
                    'field_type' => 'date',
                    'is_required' => false,
                    'display_order' => 6,
                ],
                [
                    'field_name' => 'insurance_expiry',
                    'field_label' => 'Insurance Expiry Date',
                    'field_type' => 'date',
                    'is_required' => false,
                    'help_text' => 'When insurance expires',
                    'display_order' => 7,
                ],
                [
                    'field_name' => 'insurance_provider',
                    'field_label' => 'Insurance Provider',
                    'field_type' => 'text',
                    'is_required' => false,
                    'placeholder_text' => 'e.g., Jubilee Insurance',
                    'display_order' => 8,
                ],
            ];

            foreach ($vehicleFields as $field) {
                AssetCategoryField::create(array_merge($field, [
                    'asset_category_id' => $vehicleCategory->id,
                    'is_active' => true,
                ]));
            }
        }

        // Furniture Category Fields (simplified)
        if ($furnitureCategory) {
            $furnitureFields = [
                [
                    'field_name' => 'item_type',
                    'field_label' => 'Item Type',
                    'field_type' => 'select',
                    'is_required' => true,
                    'options' => json_encode(['Desk', 'Chair', 'Cabinet', 'Table', 'Shelf', 'Other']),
                    'display_order' => 1,
                ],
                [
                    'field_name' => 'purchase_date',
                    'field_label' => 'Purchase Date',
                    'field_type' => 'date',
                    'is_required' => false,
                    'display_order' => 2,
                ],
                [
                    'field_name' => 'warranty_expiry',
                    'field_label' => 'Warranty Expiry Date',
                    'field_type' => 'date',
                    'is_required' => false,
                    'display_order' => 3,
                ],
                [
                    'field_name' => 'condition',
                    'field_label' => 'Condition',
                    'field_type' => 'select',
                    'is_required' => false,
                    'options' => json_encode(['New', 'Good', 'Fair', 'Poor']),
                    'display_order' => 4,
                ],
            ];

            foreach ($furnitureFields as $field) {
                AssetCategoryField::create(array_merge($field, [
                    'asset_category_id' => $furnitureCategory->id,
                    'is_active' => true,
                ]));
            }
        }

        // Software/License Category Fields
        if ($softwareCategory) {
            $softwareFields = [
                [
                    'field_name' => 'license_type',
                    'field_label' => 'License Type',
                    'field_type' => 'select',
                    'is_required' => true,
                    'options' => json_encode(['Insurance', 'Road License', 'Operating Permit', 'NTSA', 'Software License', 'Other']),
                    'display_order' => 1,
                ],
                [
                    'field_name' => 'license_number',
                    'field_label' => 'License Number',
                    'field_type' => 'text',
                    'is_required' => true,
                    'placeholder_text' => 'e.g., INS-2024-12345',
                    'display_order' => 2,
                ],
                [
                    'field_name' => 'issuing_authority',
                    'field_label' => 'Issuing Authority',
                    'field_type' => 'text',
                    'is_required' => false,
                    'placeholder_text' => 'e.g., Jubilee Insurance, NTSA',
                    'display_order' => 3,
                ],
                [
                    'field_name' => 'issue_date',
                    'field_label' => 'Issue Date',
                    'field_type' => 'date',
                    'is_required' => false,
                    'display_order' => 4,
                ],
                [
                    'field_name' => 'expiry_date',
                    'field_label' => 'Expiry Date',
                    'field_type' => 'date',
                    'is_required' => true,
                    'help_text' => 'When this license expires',
                    'display_order' => 5,
                ],
                [
                    'field_name' => 'renewal_date',
                    'field_label' => 'Renewal Date',
                    'field_type' => 'date',
                    'is_required' => false,
                    'help_text' => 'When to renew this license',
                    'display_order' => 6,
                ],
                [
                    'field_name' => 'license_holder',
                    'field_label' => 'License Holder',
                    'field_type' => 'text',
                    'is_required' => false,
                    'placeholder_text' => 'Company or person name',
                    'display_order' => 7,
                ],
                [
                    'field_name' => 'coverage_details',
                    'field_label' => 'Coverage Details',
                    'field_type' => 'textarea',
                    'is_required' => false,
                    'placeholder_text' => 'Details about what this license covers',
                    'display_order' => 8,
                ],
                [
                    'field_name' => 'premium_amount',
                    'field_label' => 'Premium Amount',
                    'field_type' => 'number',
                    'is_required' => false,
                    'placeholder_text' => 'Annual premium or license cost',
                    'display_order' => 9,
                ],
                [
                    'field_name' => 'payment_frequency',
                    'field_label' => 'Payment Frequency',
                    'field_type' => 'select',
                    'is_required' => false,
                    'options' => json_encode(['Annual', 'Monthly', 'Quarterly', 'One-time']),
                    'display_order' => 10,
                ],
            ];

            foreach ($softwareFields as $field) {
                AssetCategoryField::create(array_merge($field, [
                    'asset_category_id' => $softwareCategory->id,
                    'is_active' => true,
                ]));
            }
        }

        // IT Equipment Category Fields
        if ($itCategory) {
            $itFields = [
                [
                    'field_name' => 'serial_number',
                    'field_label' => 'Serial Number',
                    'field_type' => 'text',
                    'is_required' => true,
                    'placeholder_text' => 'Unique serial number',
                    'help_text' => 'Device serial number for tracking',
                    'display_order' => 1,
                ],
                [
                    'field_name' => 'processor',
                    'field_label' => 'Processor',
                    'field_type' => 'text',
                    'is_required' => false,
                    'placeholder_text' => 'e.g., Intel i5-11400H, Apple M1',
                    'display_order' => 2,
                ],
                [
                    'field_name' => 'ram',
                    'field_label' => 'RAM',
                    'field_type' => 'text',
                    'is_required' => false,
                    'placeholder_text' => 'e.g., 8GB DDR4, 16GB LPDDR5',
                    'display_order' => 3,
                ],
                [
                    'field_name' => 'storage',
                    'field_label' => 'Storage',
                    'field_type' => 'text',
                    'is_required' => false,
                    'placeholder_text' => 'e.g., 256GB SSD, 512GB NVMe',
                    'display_order' => 4,
                ],
                [
                    'field_name' => 'os',
                    'field_label' => 'Operating System',
                    'field_type' => 'text',
                    'is_required' => false,
                    'placeholder_text' => 'e.g., Windows 11 Pro, macOS 13',
                    'display_order' => 5,
                ],
                [
                    'field_name' => 'purchase_date',
                    'field_label' => 'Purchase Date',
                    'field_type' => 'date',
                    'is_required' => false,
                    'display_order' => 6,
                ],
                [
                    'field_name' => 'warranty_expiry',
                    'field_label' => 'Warranty Expiry Date',
                    'field_type' => 'date',
                    'is_required' => false,
                    'help_text' => 'When manufacturer warranty expires',
                    'display_order' => 7,
                ],
                [
                    'field_name' => 'condition',
                    'field_label' => 'Condition',
                    'field_type' => 'select',
                    'is_required' => false,
                    'options' => json_encode(['New', 'Good', 'Fair', 'Poor']),
                    'display_order' => 8,
                ],
                [
                    'field_name' => 'notes',
                    'field_label' => 'Additional Notes',
                    'field_type' => 'textarea',
                    'is_required' => false,
                    'placeholder_text' => 'Any additional information about this device',
                    'display_order' => 9,
                ],
            ];

            foreach ($itFields as $field) {
                AssetCategoryField::create(array_merge($field, [
                    'asset_category_id' => $itCategory->id,
                    'is_active' => true,
                ]));
            }
        }

        $this->command->info('Asset category fields seeded successfully!');
    }
}
