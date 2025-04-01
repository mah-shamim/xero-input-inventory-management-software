<?php
use App\Models\Inventory\ProductDamage;
use App\Models\Inventory\Warehouse\Warehouse;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;

beforeEach(function () {
    $this->api_url = '/api/inventory/productdamages';
    login();
    $this->warehouse = Warehouse::factory()->create(['company_id' => auth()->user()->company_id]);
    $data_product = test()->product_seed_only(auth()->user());
    $this->product = $data_product['product'];
});

it('index product damage test', function () {
    $response = login()->getJson($this->api_url);
    $response->assertStatus(200);
});

it('create product damage test', function () {
    login();
    $this->warehouse->products()->attach([
        $this->product->id=>[
            'quantity'=>1,
            'unit_id'=>$this->product->base_unit_id
        ]
    ]);
    $getProducts = getJson(route('product.query').'?'.http_build_query([
        'filterWarehouse'=>true, 'warehouseId'=>$this->warehouse->id
        ]))->getOriginalContent()->products;

    $data = post_data();

    $product = $getProducts[0];
    $units = $product->units;
    $data['warehouse_id'] =$this->warehouse->id;
    $data['product_id'] =$product->id;
    $data['unit_id'] =$units[0]->id;

    test()->postJson($this->api_url, $data)
        ->assertStatus(200)
        ->assertJsonFragment([
            'type' => 'success'
        ]);

});

it('update product damage test', function () {
    login();
    $data = post_data();
    $product_damage = ProductDamage::create([...$data, ...['company_id' => auth()->user()->company_id]]);

    patchJson($this->api_url . '/' . $product_damage->id, $product_damage->toArray())
        ->assertStatus(200)
        ->assertJsonFragment([
            'type' => 'success'
        ]);
});

it('delete product damage test', function () {
    login();
    $data = post_data();
    $product_damage = ProductDamage::create([...$data, ...['company_id' => auth()->user()->company_id]]);
    test()->deleteJson($this->api_url . '/' . $product_damage->id)
        ->assertStatus(200)
        ->assertJsonFragment([
            'type' => 'success'
        ]);
});

it('search product damage test', function () {
    login();
    $product_name = $this->product->name;
    $data = post_data();
    ProductDamage::create([...$data, ...['company_id' => auth()->user()->company_id]]);
    $response = test()->getJson($this->api_url . '?' . http_build_query(['search' => $product_name]))
        ->assertStatus(200);
    $response_data = $response->getOriginalContent();
    expect($response_data['productDamages'][0]->product_id)->toBe($this->product->id);
});

it('validate product damage test', function ($key) {
    login();
    $data = post_data();
    unset($data[$key]);
    $message_key = keyInMessage($key);
    test()->postJson($this->api_url, $data)
        ->assertStatus(422)->assertJsonFragment([
            $key => ["The " . $message_key . " field is required."]
        ]);
    if ($key === 'warehouse_id' || $key === 'product_id' || $key === 'unit_id') {
        $data[$key] = rand(50, 70);
        test()->postJson($this->api_url, $data)
            ->assertStatus(422)->assertJsonFragment([
                $key => ["The selected " . $message_key . " is invalid."]
            ]);
    }
})->with([
    'warehouse_id',
    'product_id',
    'unit_id',
    'quantity',
    'sale_value'
]);


function post_data(): array
{
    $data = [
        'warehouse_id' => test()->warehouse->id,
        'product_id' => test()->product->id,
        'unit_id' => test()->product->base_unit_id,
        'quantity' => $quantity = rand(1, 5),
        'sale_value' => test()->product->price * $quantity
    ];
    return $data;
}


