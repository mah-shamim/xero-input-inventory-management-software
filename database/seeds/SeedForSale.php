<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Tests\Traits\SharedTestMethods;

class SeedForSale extends Seeder
{
    use SharedTestMethods;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@test.com')->first();
        $this->sale_seed_standalone($user);
    }
}
