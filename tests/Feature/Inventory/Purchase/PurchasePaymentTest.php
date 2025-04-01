<?php
//@todo find a way to edit payment
beforeEach(function () {
    $this->user = $this->seed_and_getUser();
    $this->postData = $this->purchase_input_data($this->user);
});

it('create purchase payment test', function () {
    $data = login()->postJson(route('purchases.store'), $this->postData)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
    $data = json_decode($data->getOriginalContent());
    $post_data = [
        "date" => now()->format('Y-m-d'),
        "bank_id" => $this->postData['bank_id'],
        "model" => "purchase",
        "id" => null,
        "paid" => 5,
        "payment_type" => 1,
        "model_object" => $data->purchase
    ];

    login()
        ->postJson(route('pay.crud.store'), $post_data)
        ->assertStatus(200);

})->group('createPurchasePaymentTest');


//http://localhost:3000/api/payments/crud/8/edit?model=purchase