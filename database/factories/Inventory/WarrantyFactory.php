<?php

namespace Database\Factories\Inventory;

use App\Models\Company;
use App\Models\Inventory\Customer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory\Warranty>
 */
class WarrantyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = ['Receive from Customer',
            'Send to Supplier',
            'Receive from Supplier',
            'Delivered to Customer',
            'Damaged'];

        return [
            'quantity' => 1,
            'status' =>array_random($status, 1)[0],
            'warranty_date'=>Carbon::now()->format('Y-m-d')
        ];
    }
}
