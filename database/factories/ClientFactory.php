<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'client_code' => 'CLT' . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'company_name' => $this->faker->randomElement([
                'ABC Bank',
                'First National Bank',
                'City Bank',
                'Metro Bank',
                'United Bank',
                'People\'s Bank',
                'Commercial Bank',
                'Standard Bank'
            ]),
            'contact_person' => $this->faker->name(),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'region' => $this->faker->randomElement(['North', 'South', 'East', 'West', 'Central']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'contract_start_date' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
            'contract_end_date' => $this->faker->dateTimeBetween('+1 year', '+3 years'),
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
}