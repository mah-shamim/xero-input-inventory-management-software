<?php

use function Pest\Laravel\getJson;

beforeEach(closure: function (){
    login();
    $this->data = test()->product_seed_only(auth()->user());
});

it('product unit mapping index page is ok', function () {
    $response = login()
        ->getJson(api_product_unit_mapping_url());
    $response->assertStatus(200)
        ->assertJson(['current_page' => 1]);
});

it('create product unit mapping test', function () {
    $response = login()->getJson('/api/inventory/productunit/create');
    $response_data = $response->getOriginalContent();

    $product = $response_data->products[0];
    $unitList = array_column($response_data->units, 'id');
    $data = [
        'product_id' => $product,
        'unitList' => $unitList,
        'unitidjoin' => implode(',', $unitList)
    ];
    $response = test()->postJson('/api/inventory/productunit', $data);
    expect($response)->assertStatus(200)->assertJsonFragment([
        'type' => 'success',
        'message' => 'Product Unit Mapping has been created successfully'
    ]);
});

it('validate product unit mapping test', function ($key) {
    $response = login()->get('/api/inventory/productunit/create');
    $response_data = $response->getOriginalContent();
    $product = $response_data->products[0];
    $unitList = array_column($response_data->units, 'id');
    $data = [
        'product_id' => $product,
        'unitList' => $unitList,
        'unitidjoin' => implode(',', $unitList)
    ];
    unset($data[$key]);

    $keyInMessage= str()->snake($key);

    $keyInMessage = str_replace('_', ' ', $keyInMessage);

    test()->postJson('/api/inventory/productunit', $data)
        ->assertStatus(422)->assertJsonFragment([
            $key => ["The " . $keyInMessage . " field is required."]
        ]);
})->with([
    'product_id', 'unitList'
]);

