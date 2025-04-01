<?php


use App\Models\Inventory\Brand;
use App\Models\Inventory\Category;
use App\Models\Inventory\Unit;

it('product index page is ok', function () {
    $response = $this
        ->signIn()
        ->getJson(api_product_url());
    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);

});

it('create product test', function () {
    $this->signIn();
    $data = validate_product_data();

    $response = $this->postJson(api_product_url(), $data);
    unset($data['categories']);
    unset($data['weight']);
    $this->assertDatabaseHas('products', $data);
    expect($response)->assertStatus(200)->assertJson(['type' => 'success']);
});

it('update product test', function () {
    $this->signIn();
    $data = validate_product_data();
    $response = $this->postJson(api_product_url(), $data);
    $response = $response->assertStatus(200)->assertJson(['type' => 'success']);

    $posted_data_original = $response->getOriginalContent();
    $posted_data = json_decode($posted_data_original);

    $data = collect($posted_data->product)->toArray();

    $categories = Category::factory()->create(['company_id' => auth()->user()->company_id]);
    $data['categories'] = [$categories->id];

    unset($data['created_at']);
    unset($data['updated_at']);
    unset($data['purchased_location']);
    unset($data['sale_location']);
    $this->patchJson(api_product_url($data['id']), $data)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);

    unset($data['categories']);

    $this->assertDatabaseHas('products', $data);
});

it('product delete test', function () {
    $this->signIn();
    $data = validate_product_data();
    $response = $this
        ->postJson(api_product_url(), $data)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);

    $posted_data_original = $response->getOriginalContent();
    $posted_data = json_decode($posted_data_original);

    $data = collect($posted_data->product)->toArray();

    $this->deleteJson(api_product_url()."/{$data['id']}")
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('product brand search test', function () {
    $this->signIn();
    $data1 = validate_product_data();
    $data2 = validate_product_data();
    $this->postJson(api_product_url(), $data1);
    $this->postJson(api_product_url(), $data2);

    $this->getJson(api_product_url().'?brand_id='.$data2['brand_id'])
        ->assertStatus(200)
        ->assertJsonFragment(
            [
                'name'=>$data2['name']
            ]
        );
});

it('product category search test', function () {
    $this->signIn();
    $data1 = validate_product_data();
    $data2 = validate_product_data();
    $this->postJson(api_product_url(), $data1);
    $this->postJson(api_product_url(), $data2);

    $this->getJson(api_product_url().'?categories='.$data2['categories'][0])
        ->assertStatus(200)
        ->assertJsonFragment(
            [
                'name'=>$data2['name']
            ]
        );
});

it('product name search test', function () {
    $this->signIn();
    $data1 = validate_product_data();
    $data2 = validate_product_data();
    $this->postJson(api_product_url(), $data1);
    $this->postJson(api_product_url(), $data2);

    $this->getJson(api_product_url().'?name_code_search='.$data2['name'])
        ->assertStatus(200)
        ->assertJsonFragment(
            [
                'name'=>$data2['name']
            ]
        );
});

it('product code search test', function () {
    $this->signIn();
    $data1 = validate_product_data();
    $data2 = validate_product_data();
    $this->postJson(api_product_url(), $data1);
    $this->postJson(api_product_url(), $data2);

    $this->getJson(api_product_url().'?name_code_search='.$data2['code'])
        ->assertStatus(200)
        ->assertJsonFragment(
            [
                'code'=>$data2['code']
            ]
        );
});

it('validation product name', function () {
    $response = $this->signIn()
        ->postJson(api_product_url(), validate_product_data('name'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'name' => ['The name field is required.']
        ]);
});

it('validation product brand_id', function () {
    $response = $this->signIn()
        ->postJson(api_product_url(), validate_product_data('brand_id'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'brand_id' => ['The brand name field is required.']
        ]);
});

it('validation product price', function () {
    $response = $this->signIn()
        ->postJson(api_product_url(), validate_product_data('price'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'price' => ['The price field is required.']
        ]);
});

it('validation product categories', function () {
    $response = $this->signIn()
        ->postJson(api_product_url(), validate_product_data('categories'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'categories' => ['The categories field is required.']
        ]);
});

it('validation product base_unit_id', function () {
    $response = $this->signIn()
        ->postJson(api_product_url(), validate_product_data('base_unit_id'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'base_unit_id' => ['The unit field is required.']
        ]);
});

it('validation product buying_price', function () {
    $response = $this->signIn()
        ->postJson(api_product_url(), validate_product_data('buying_price'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'buying_price' => ['The buying price field is required.']
        ]);
});

it('validation product code', function () {
    $response = $this->signIn()
        ->postJson(api_product_url(), validate_product_data('code'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'code' => ['The code field is required.']
        ]);
});

function validate_product_data(string $key = null): array
{
    $categories = Category::factory()->create(
        [
            'type' => 'PRODUCT',
            'company_id' => auth()->user()->company_id
        ]
    );
    $data = [
        'name' => fake()->name,
        'code' => fake()->currencyCode,
        'buying_price' => $buying_price = fake()->randomFloat(4, 10, 100),
        'price' => $buying_price + fake()->randomNumber(2),
        'base_unit_id' => Unit::factory()->create(['company_id' => auth()->user()->company_id])->id,
        'brand_id' => Brand::factory()->create(['company_id' => auth()->user()->company_id])->id,
        'weight' => fake()->randomNumber(2),
        'categories' => [$categories['id']]
    ];

//    dd($data);

    if ($key) unset($data[$key]);

    return $data;
}