<?php

namespace Database\Factories\Inventory;

use App\Models\Company;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $buying_price = fake()->randomFloat(4, 10, 100);
        return [
            'name'=> $name = fake()->name,
            'code'=>fake()->currencyCode,
            'slug' => str_slug($name),
            'buying_price'=>$buying_price,
            'price'=> $buying_price + fake()->randomNumber(2),
        ];
    }
}
