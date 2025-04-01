<?php

namespace Database\Factories\Payroll;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payroll\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "nid" => fake()->uuid,
            "name" => fake()->name,
            "note" => fake()->text,
            "birth" => fake()->date(),
            "mobile" => fake()->e164PhoneNumber(),
            "salary" => fake()->numberBetween([1, 2]),
            "address" => fake()->address,
            "join_date" => fake()->date(),
            "emergency" => fake()->e164PhoneNumber(),
            "employee_id" => fake()->uuid,
            "designation" => fake()->jobTitle,
            "contract_type" => fake()->randomElement([
                'permanent',
                'temporary',
                'contractual',
                'others'
            ]),
        ];
    }
}
