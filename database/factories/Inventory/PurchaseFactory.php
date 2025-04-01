<?php

namespace Database\Factories\Inventory;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Traits\SharedTestMethods;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory\Purchase>
 */
class PurchaseFactory extends Factory
{
    use SharedTestMethods;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
//        $user = seed_and_getUser();
//        $data = $this->purchase_seed_standalone($user);
        return [
            //
        ];
    }

    public function seed_data()
    {


    }
}
