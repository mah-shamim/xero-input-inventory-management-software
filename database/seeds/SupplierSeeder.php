<?php

namespace Database\Seeders;

use App\Models\Inventory\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::where('email', 'test@test.com')->first();
        $user2 = User::where('email', 'test_dusk@test.com')->first();
        foreach ([$user1, $user2] as $user) {
            Supplier::factory()->create([
                'company_id'=>$user->company_id,
            ]);
        }
    }
}
