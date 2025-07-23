<?php

// 6. POS TERMINAL SEEDER
// File: database/seeders/PosTerminalSeeder.php
// ==============================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PosTerminal;
use App\Models\Client;

class PosTerminalSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();

        // Sample terminals for ABC Bank
        $abcBank = $clients->where('client_code', 'CLT001')->first();
        if ($abcBank) {
            $terminals = [
                [
                    'terminal_id' => 'POS-001',
                    'client_id' => $abcBank->id,
                    'merchant_name' => 'Green Valley Supermarket',
                    'merchant_contact_person' => 'John Merchant',
                    'merchant_phone' => '+254712345678',
                    'merchant_email' => 'john@greenvalley.com',
                    'physical_address' => 'Westlands Shopping Centre, Nairobi',
                    'region' => 'North',
                    'area' => 'Westlands',
                    'business_type' => 'Retail/Grocery',
                    'installation_date' => '2024-01-15',
                    'terminal_model' => 'Ingenico iWL220',
                    'serial_number' => 'SN123456',
                    'status' => 'active',
                    'last_service_date' => '2024-07-15',
                    'next_service_due' => '2024-10-15',
                ],
                [
                    'terminal_id' => 'POS-002',
                    'client_id' => $abcBank->id,
                    'merchant_name' => 'City Electronics Store',
                    'merchant_contact_person' => 'Jane Store Owner',
                    'merchant_phone' => '+254723456789',
                    'merchant_email' => 'jane@cityelectronics.com',
                    'physical_address' => 'Tom Mboya Street, Nairobi CBD',
                    'region' => 'Central',
                    'area' => 'CBD',
                    'business_type' => 'Electronics',
                    'installation_date' => '2024-02-01',
                    'terminal_model' => 'Verifone VX520',
                    'serial_number' => 'SN234567',
                    'status' => 'offline',
                    'last_service_date' => '2024-07-10',
                    'next_service_due' => '2024-08-10',
                ],
            ];

            foreach ($terminals as $terminal) {
                PosTerminal::create($terminal);
            }
        }

        // Sample terminals for First National Bank
        $fnb = $clients->where('client_code', 'CLT002')->first();
        if ($fnb) {
            PosTerminal::create([
                'terminal_id' => 'POS-003',
                'client_id' => $fnb->id,
                'merchant_name' => 'Mama Njeri Restaurant',
                'merchant_contact_person' => 'Mary Njeri',
                'merchant_phone' => '+254734567890',
                'merchant_email' => 'mary@mamanjeri.com',
                'physical_address' => 'Kikuyu Town, Kiambu County',
                'region' => 'West',
                'area' => 'Kikuyu',
                'business_type' => 'Restaurant',
                'installation_date' => '2024-03-01',
                'terminal_model' => 'PAX A920',
                'serial_number' => 'SN345678',
                'status' => 'active',
                'last_service_date' => '2024-07-18',
                'next_service_due' => '2024-10-18',
            ]);
        }

        // Create random terminals for all clients
        foreach ($clients as $client) {
            $terminalCount = rand(2, 8); // Each client gets 2-8 terminals
            PosTerminal::factory($terminalCount)->create([
                'client_id' => $client->id,
            ]);
        }

        // Create some specifically faulty terminals
        PosTerminal::factory(5)->faulty()->create();
    }
}
