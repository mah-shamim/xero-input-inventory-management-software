<?php

namespace Database\Factories\Inventory\Warehouse;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory\Warehouse\Warehouse>
 */
class WarehouseFactory extends Factory
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
            'code'=>fake()->postcode,
            'phone'=>fake()->e164PhoneNumber,
            'email'=>fake()->email,
            'address'=>fake()->address,
            'is_default'=>false,
        ];
    }
}
