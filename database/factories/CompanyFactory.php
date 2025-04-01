<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'code'=>fake()->unique()->postcode,
            'address1'=>fake()->address,
            'address2'=>fake()->address,
            'contact_name'=>fake()->name,
            'contact_phone'=>[fake()->phoneNumber()],
            'is_active'=>true,
            'trial_end_at'=>now()->addDays(30)
        ];
    }
}
