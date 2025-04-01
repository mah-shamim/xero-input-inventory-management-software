<?php

beforeEach(function () {
    $this->user = $this->seed_and_getUser();
    $this->postData = $this->purchase_input_data($this->user);
});


it('index purchase test', function () {
    login()->getJson(route('purchases.index'))
        ->assertStatus(200)
        ->assertJsonFragment(['current_page' => 1]);
})->group('testIndexPurchase');


it('create purchase test', function () {
    login()
        ->getJson(route('purchases.create'))
        ->assertStatus(200);

    login()->postJson(route('purchases.store'), $this->postData)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
})->group('testCreatePurchase');

it('update purchase test', function () {
    login();
    $data = login()->postJson(route('purchases.store'), $this->postData)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);

    $purchase_data = json_decode($data->getOriginalContent())->purchase;

    $data = test()->getJson(route('purchases.edit', $purchase_data->id))
        ->assertStatus(200);

    $purchase_data = json_decode($data->getOriginalContent());
    $purchase_data = collect($purchase_data->purchases)->toArray();
//    dd($purchase_data['products'][0]->pivot->pp_id);
    $purchase_data["items"] = [
        [
            "product_id" => $purchase_data['products'][0]->id,
            "pname" => $purchase_data['products'][0]->name,
            "pp_id" => $purchase_data['products'][0]->pivot->pp_id, //
            "price" => $purchase_data['products'][0]->buying_price,
            "unit_price" => $purchase_data['products'][0]->buying_price,
            "fromUnit" => $purchase_data['products'][0]->base_unit_id,
            "baseUnit" => $purchase_data['products'][0]->base_unit_id,
            "warehouse" => $purchase_data['products'][0]->pivot->warehouse_id,
            "unit" => $purchase_data['products'][0]->pivot->unit_id,
            "weight" => 0,
            "manufacture_part_number" => 0,
            "part_number" => [],
            "quantity" => 1,
            "location" => "",
            "weight_total" => 0,
        ]
    ];
//    dd($purchase_data);
    test()->patchJson(route('purchases.update', $purchase_data['id']), $purchase_data)
        ->assertStatus(200);

})->group('testUpdatePurchase');


it('delete purchase test', function () {
    login();
    $data = login()->postJson(route('purchases.store'), $this->postData)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);

    $purchase_data = json_decode($data->getOriginalContent())->purchase;
//    dd($purchase_data->id);
    test()
        ->deleteJson(route('purchases.destroy', $purchase_data->id))
        ->assertStatus(200);
})->group('testDeletePurchase');

it('validate purchase test', function ($key) {
    login();

    if(str_contains($key, '.0.')){
        $arr = explode('.', $key);
        unset($this->postData[$arr[0]][0][$arr[2]]);
        $message  ="The ". title_case(str_replace(['_', 'id'], ' ', $arr[2])) ." field is required.";
        $message = str_replace('  ', '', $message);
    }else{
        unset($this->postData[$key]);
        $message  ="The ". str_replace('_', ' ', $key) ." field is required.";
    }

    login()->postJson(route('purchases.store'), $this->postData)
        ->assertStatus(422)
        ->assertJsonFragment([
            $key => [$message]
        ]);

})->with([
    'status',
    'bank_id',
    'supplier_id',
    'purchase_date',
    'payment_status',
    'items.0.unit',
    'items.0.quantity',
    'items.0.warehouse',
    'items.0.unit_price',
    'items.0.product_id',
]);



