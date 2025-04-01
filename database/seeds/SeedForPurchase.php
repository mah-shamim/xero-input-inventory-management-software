<?php

namespace Database\Seeders;

use App\Models\Bank\Bank;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Models\User;

use Illuminate\Database\Seeder;
use Tests\Traits\SharedTestMethods;

class SeedForPurchase extends Seeder
{
    use SharedTestMethods;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@test.com')->first();
        $this->purchase_seed_standalone($user);
    }
}
