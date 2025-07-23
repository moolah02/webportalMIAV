<?php

// 5. CLIENT SEEDER
// File: database/seeders/ClientSeeder.php
// ==============================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'client_code' => 'CLT001',
                'company_name' => 'ABC Bank',
                'contact_person' => 'John Manager',
                'email' => 'john.manager@abcbank.com',
                'phone' => '+254712345678',
                'address' => 'ABC Bank Tower, Westlands',
                'city' => 'Nairobi',
                'region' => 'Central',
                'status' => 'active',
                'contract_start_date' => '2023-01-01',
                'contract_end_date' => '2026-12-31',
            ],
            [
                'client_code' => 'CLT002',
                'company_name' => 'First National Bank',
                'contact_person' => 'Sarah Wilson',
                'email' => 'sarah.wilson@fnb.com',
                'phone' => '+254723456789',
                'address' => 'FNB Plaza, Upper Hill',
                'city' => 'Nairobi',
                'region' => 'Central',
                'status' => 'active',
                'contract_start_date' => '2023-06-01',
                'contract_end_date' => '2025-05-31',
            ],
            [
                'client_code' => 'CLT003',
                'company_name' => 'City Bank',
                'contact_person' => 'Michael Brown',
                'email' => 'michael.brown@citybank.co.ke',
                'phone' => '+254734567890',
                'address' => 'City Bank Centre, CBD',
                'city' => 'Nairobi',
                'region' => 'Central',
                'status' => 'active',
                'contract_start_date' => '2024-01-01',
                'contract_end_date' => '2027-12-31',
            ],
            [
                'client_code' => 'CLT004',
                'company_name' => 'Metro Bank',
                'contact_person' => 'Grace Wanjiku',
                'email' => 'grace.wanjiku@metrobank.ke',
                'phone' => '+254745678901',
                'address' => 'Metro Towers, Kilimani',
                'city' => 'Nairobi',
                'region' => 'West',
                'status' => 'active',
                'contract_start_date' => '2023-03-15',
                'contract_end_date' => '2026-03-14',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }

        // Create additional random clients
        Client::factory(6)->active()->create();
    }
}
