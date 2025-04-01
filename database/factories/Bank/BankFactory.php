<?php

namespace Database\Factories\Bank;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bank\Bank>
 */
class BankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>fake()->name,
            'branch'=>fake()->city,
            'type'=>'cash',
            'account_no'=>fake()->creditCardNumber,
            'address'=>fake()->address
        ];
    }
}
