<?php

namespace Database\Factories\Inventory;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id'=>0,
            'name'=>fake()->colorName,
            'type'=>array_random(['PRODUCT', 'EXPENSE', 'CUSTOMER', 'PRICE']),
            'description'=>fake()->sentence
        ];
    }
}
