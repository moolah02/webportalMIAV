<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Information Technology',
                'Human Resources', 
                'Finance & Accounting',
                'Operations',
                'Marketing & Sales',
                'Customer Service',
                'Research & Development',
                'Quality Assurance'
            ]),
            'description' => $this->faker->sentence(10),
        ];
    }
}
