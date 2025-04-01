<?php

namespace Database\Seeders;

use App\Models\Inventory\Brand;
use App\Models\Inventory\Category;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::where('email', 'test@test.com')->first();
        $user2 = User::where('email', 'test_dusk@test.com')->first();
        foreach ([$user1, $user2] as $user) {
            $product = Product::factory()->create([
                'company_id'=>$user->company_id,
                'brand_id'=>Brand::factory()->create([
                    'company_id'=>$user->company_id
                ]),
                'base_unit_id'=>Unit::factory([
                    'company_id'=>$user->company_id
                ])
            ]);
            $categories = Category::where('type', 'product')->where('company_id', $user->company_id)->get();
            foreach ($categories as $category) {
                $category->products()->attach($product->id);
            }
        }
    }
}
