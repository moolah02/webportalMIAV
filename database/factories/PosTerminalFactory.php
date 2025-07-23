<?php

// ==============================================
// 2. POS TERMINAL FACTORY
// File: database/factories/PosTerminalFactory.php
// ==============================================

namespace Database\Factories;

use App\Models\PosTerminal;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class PosTerminalFactory extends Factory
{
    protected $model = PosTerminal::class;

    public function definition(): array
    {
        return [
            'terminal_id' => 'POS-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'client_id' => Client::factory(),
            'merchant_name' => $this->faker->randomElement([
                'Green Valley Supermarket',
                'City Electronics Store',
                'Mama Njeri Restaurant',
                'Uhuru Hardware',
                'Tech Plaza',
                'Fashion Hub',
                'Quick Mart',
                'Sunrise Pharmacy'
            ]),
            'merchant_contact_person' => $this->faker->name(),
            'merchant_phone' => $this->faker->phoneNumber(),
            'merchant_email' => $this->faker->email(),
            'physical_address' => $this->faker->address(),
            'region' => $this->faker->randomElement(['North', 'South', 'East', 'West', 'Central']),
            'area' => $this->faker->randomElement(['Westlands', 'Downtown', 'Kikuyu', 'Eastleigh', 'Karen']),
            'business_type' => $this->faker->randomElement(['Retail', 'Restaurant', 'Pharmacy', 'Electronics', 'Grocery']),
            'installation_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'terminal_model' => $this->faker->randomElement(['Ingenico iWL220', 'Verifone VX520', 'PAX A920', 'Ingenico Move 5000']),
            'serial_number' => 'SN' . $this->faker->unique()->numberBetween(100000, 999999),
            'contract_details' => $this->faker->sentence(10),
            'status' => $this->faker->randomElement(['active', 'offline', 'maintenance', 'faulty']),
            'last_service_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'next_service_due' => $this->faker->dateTimeBetween('now', '+3 months'),
        ];
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    public function faulty()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'faulty',
                'last_service_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
                'next_service_due' => now()->subDays(rand(1, 30)),
            ];
        });
    }
}