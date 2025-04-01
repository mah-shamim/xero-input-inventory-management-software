<?php

use App\Models\Inventory\Purchase;

beforeEach(function () {
    $this->user = $this->seed_and_getUser();
    $this->postData = $this->purchase_input_data($this->user);
});

it('create purchase return', function (){
    $data = login()->postJson(route('purchases.store'), $this->postData)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
    $data = json_decode($data->getOriginalContent());

    $purchase = Purchase::with('products')
        ->where('id', $data->purchase->id)
        ->first();

    $post_data = [
        'returnable_id'=>$purchase->id,
        "returnable_type"=>"App\\Models\\Inventory\\Purchase",
        "product_id"=>$purchase->products[0]->id,
        "unit_id"=>$purchase->products[0]->pivot->unit_id,
        "quantity"=>1,
        "warehouse_id"=>$purchase->products[0]->pivot->warehouse_id,
        "amount"=>0
    ];

    login()
        ->patchJson(route('purchase-return.update', $purchase->id),
            ['return'=>[$post_data]]
        )
    ->assertStatus(200);
});
