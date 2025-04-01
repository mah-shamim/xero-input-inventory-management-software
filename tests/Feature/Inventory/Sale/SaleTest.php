<?php

beforeEach(function () {
    $this->user = $this->seed_and_getUser();
    $this->postData = $this->sale_input_data($this->user);
});

it('index sale test', function () {
    login()->getJson(route('sales.index'))
        ->assertStatus(200)
        ->assertJsonFragment(['current_page' => 1]);
})->group('testIndexSale');

it('create sale test', function () {
    login()
        ->getJson(route('sales.create'))
        ->assertStatus(200)->getOriginalContent();

    login()->postJson(route('sales.store'), $this->postData)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
})->group('testCreateSale');


it('update sale test', function () {
    login();
    $data = login()->postJson(route('sales.store'), $this->postData)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);

    $sale_data = json_decode($data->getOriginalContent())->sale;

    $data = test()->getJson(route('sales.edit', $sale_data->id))
        ->assertStatus(200);

    $sale_data = json_decode($data->getOriginalContent());
    $sale_data = collect($sale_data->sales)->toArray();
    $sale_data['payment_type'] = 1;
//    dd($sale_data['products'][0]->pivot->ps_id);
    $sale_data["items"] = [
        [
            "product_id" => $sale_data['products'][0]->id,
            "pname" => $sale_data['products'][0]->name,
            "ps_id" => $sale_data['products'][0]->pivot->ps_id, //
            "price" => $sale_data['products'][0]->buying_price,
            "unit_price" => $sale_data['products'][0]->buying_price,
            "fromUnit" => $sale_data['products'][0]->base_unit_id,
            "baseUnit" => $sale_data['products'][0]->base_unit_id,
            "warehouse" => $sale_data['products'][0]->pivot->warehouse_id,
            "unit" => $sale_data['products'][0]->pivot->unit_id,
            "weight" => 0,
            "manufacture_part_number" => 0,
            "part_number" => [],
            "quantity" => 1,
            "location" => "",
            "weight_total" => 0,
            "payment_type" => 1
        ]
    ];
//    dd($sale_data);
    test()->patchJson(route('sales.update', $sale_data['id']), $sale_data)
        ->assertStatus(200);
})->group('testUpdatePurchase');

it('delete sale test', function () {
    $create_sale = login()->postJson(route('sales.store'), $this->postData)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
    $sale_data = json_decode($create_sale->getOriginalContent())->sale;
    test()->deleteJson(route('sales.destroy', $sale_data->id))
        ->assertStatus(200);
})->group('testDeleteSale');

it('validate sale test', function ($key) {
    login();
    if (str_contains($key, '.0.')) {
        $arr = explode('.', $key);
        unset($this->postData[$arr[0]][0][$arr[2]]);
        $message = "The " . title_case(str_replace(['_', 'id'], ' ', $arr[2])) . " field is required.";
        $message = str_replace('  ', '', $message);
    } else {
        unset($this->postData[$key]);
        $message = "The " . str_replace('_', ' ', $key) . " field is required.";
    }
    login()->postJson(route('sales.store'), $this->postData)
        ->assertStatus(422)
        ->assertJsonFragment([
            $key => [$message]
        ]);
})->with([
    'status',
    'customer_id',
    'sales_date',
    'payment_status',
    'items.0.unit',
    'items.0.quantity',
    'items.0.warehouse',
    'items.0.unit_price',
    'items.0.product_id',
]);