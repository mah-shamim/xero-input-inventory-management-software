<?php

namespace Tests\Traits;

use App\Http\Controllers\Inventory\PurchaseController;
use App\Models\Bank\Bank;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Category;
use App\Models\Inventory\Customer;
use App\Models\Inventory\Product;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Models\User;
use App\Services\Inventory\ProductUnit\ProductUnitCreateService;
use App\Services\Inventory\Purchase\PurchaseCreateService;
use App\Services\Inventory\UnitConversion\UnitConversionCreateService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\postJson;

trait SharedTestMethods
{
    public function sale_seed_standalone($user): array
    {
        $data = $this->purchase_input_data($user);
        Model::unguard();
        $purchase = DB::table('purchases')->insertGetId([
            'supplier_id' => $data['supplier_id'],
            'company_id' => $user->company_id,
            'shipping_cost' => $data['shipping_cost'],
            'overall_discount' => $data['overall_discount'],
            'total' => $data['total'],
            'total_weight' => $data['total_weight'],
            'bill_no' => $data['bill_no'],
            'ref' => getPurchaseRef($data['status'], $user->id),
            'status' => $data['status'],
            'payment_status' => $data['payment_type'],
            'note' => '',
            'purchase_date' => $data['purchase_date'],
        ]);

        Customer::factory()->create([
            'company_id'=>$user->company_id,
            'is_default'=>true
        ]);

        DB::table('product_purchase')->insert([
            'product_id' => $data['items'][0]['product_id'],
            'purchase_id' => $purchase,
            'warehouse_id' => $data['items'][0]['warehouse'],
            'unit_id' => $data['items'][0]['unit'],
            'delivery_stamps' => 1,
            'purchase_quantity' => $data['items'][0]['quantity'],
            'quantity' => $data['items'][0]['quantity'],
            'price' => $data['items'][0]['price'],
            'discount' => 0,
            'subtotal' => $data['items'][0]['price'],
            'actual_subtotal' => $data['items'][0]['price'],
            'actual_quantity' => $data['items'][0]['quantity'],
            'weight' => $data['items'][0]['weight'],
            'weight_total' => $data['items'][0]['weight_total'],
            'adjustment' => 0
        ]);

        DB::table('product_warehouse')
            ->insert([
                'product_id' => $data['items'][0]['product_id'],
                'warehouse_id' => $data['items'][0]['warehouse'],
                'unit_id' => $data['items'][0]['unit'],
                'quantity' => $data['items'][0]['quantity'],
                'weight' => $data['items'][0]['weight'],
                'location' => json_encode([])
            ]);

        Model::reguard();

        return $data;

    }

    public function sale_input_data($user): array
    {
        $data = $this->purchase_input_data($user);
        $data['customer_id'] = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'is_default' => true])->id;

        $data['sales_date'] = now()->format('Y-m-d');
        $data['purchase_data'] = $data;
        return $data;

    }

    public function purchase_input_data($user): array
    {
        $data = $this->purchase_seed_standalone($user);
        return [
            "bank_id" => $data['bank']['id'],
            "bill_no" => fake()->streetName,
            "cheque_number" => fake()->creditCardNumber,
            "id" => null,
            "note" => "",
            "overall_discount" => 0,
            "paid" => $data['product_set_1']['product']['price'],
            "payment_type" => 1,
            "purchase_date" => now()->format('Y-m-d'),
            "shipping_cost" => 0,
            "status" => 1,
            "sum_fields" => [],
            "supplier_id" => Supplier::factory()->create(['company_id' => $user->company_id])->id,
            "total_weight" => 0,
            "transaction_number" => "",
            "removed_ids" => [],
            "total" => "85.620",
            "payment_status" => 2,
            "purchase_date_formatted" => now()->format('Y-m-d'),
            "extra_weight" => 0,
            "items" => [
                [
                    "product_id" => $data['product_set_1']['product']['id'],
                    "pname" => $data['product_set_1']['product']['name'],
                    "unit_price" => $data['product_set_1']['product']['buying_price'],
                    "price" => $data['product_set_1']['product']['price'],
                    "fromUnit" => $data['product_set_1']['units'][0]['id'],
                    "baseUnit" => $data['product_set_1']['units'][0]['id'],
                    "warehouse" => $data['warehouse'][0]['id'],
                    "unit" => $data['product_set_1']['units'][0]['id'],
                    "weight" => 0,
                    "manufacture_part_number" => 0,
                    "part_number" => [],
                    "quantity" => 1,
                    "location" => "",
                    "weight_total" => 0,
                ],
            ],
        ];
    }

    public function purchase_seed_standalone($user = null): array
    {
        if (!$user) {
            $user = seed_and_getUser();
        }
        return [
            "supplier" => Supplier::factory()->create(['company_id' => $user->company_id]),
            "product_set_1" => $this->product_seed_only($user),
            "product_set_2" => $this->product_seed_only($user),
            "bank" => Bank::factory()->create(['company_id' => $user->company_id]),
            "warehouse" => Warehouse::factory()->count(2)
                ->sequence(
                    ['is_default' => true],
                    ['is_default' => false],
                )
                ->create([
                    'company_id' => $user->company_id,
                ])
        ];
    }

    public function
    product_seed_only($user = null): array
    {
        if ($user) {
            auth()->loginUsingId($user->id);
        } else {
            $user = auth()->user();
            $this->signIn();
        }
        $brand = Brand::factory()->create([
            'company_id' => $user->company_id
        ]);
        $unit = $this->unitSeed($user);
        $unit2 = $this->unitSeed($user);
        list($product, $categories) = $this->product_seed_with_category_unit_mapping($user, $brand, $unit, $unit2);

        auth()->logout();
        return [
            'product' => $product,
            'units' => [$unit, $unit2],
            'brand' => $brand,
            'category' => $categories
        ];
    }

    public function unitSeed(User $user): Unit
    {
        return Unit::factory()->create([
            'company_id' => $user->company_id
        ]);
    }

    private function product_seed_with_category_unit_mapping(User $user, Brand $brand, Unit $unit, Unit $unit2): array
    {
        $product = Product::factory()->create([
            'company_id' => $user->company_id,
            'brand_id' => $brand->id,
            'base_unit_id' => $unit->id
        ]);
        $categories = Category::factory()->create([
            'type' => 'PRODUCT',
            'company_id' => $user->company_id
        ]);

        $product->categories()->attach([$categories->id]);

        request()->merge([
            'from_unit_id' => $unit->id,
            'from_unit_val' => 1,
            'to_unit_id' => $unit2->id,
            'to_unit_val' => 12,
            'company_id' => $user->company_id
        ]);

        (new UnitConversionCreateService())->execute(['type' => 'success', 'message' => ''], request());

        request()->merge([
            'product_id' => $product->id,
            'unitList' => $unit_array = array_column([$unit, $unit2], 'id'),
            'unitidjoin' => implode(',', $unit_array),
            'company_id' => $user->company_id
        ]);

        (new ProductUnitCreateService())->execute(['type' => 'success', 'message' => ''], request());
        return array($product, $categories);
    }
}