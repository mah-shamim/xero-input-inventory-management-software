<?php

use App\Models\Inventory\Customer;
use App\Models\Inventory\Warranty;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = seed_and_getUser();
    $this->data_product = $this->product_seed_only($this->user);
    $this->data_customer = Customer::factory()->create(['company_id'=>$this->user->company_id]);
    $this->api_url = '/api/inventory/warranty';
});

it('warranty index', function () {
    login()
        ->getJson($this->api_url)
        ->assertStatus(200)
        ->assertJsonFragment([
            'current_page' => 1
        ]);
});

it('create warranty test', function () {
    $request_data = [
        'warranty_date' => Carbon::now()->format('Y-m-d'),
        'product_id' => $this->data_product['product']->id,
        'customer_id' => $this->data_customer->id,
        'status' => 'Receive from Customer'
    ];
    login()->postJson($this->api_url, $request_data)
        ->assertStatus(200)
        ->assertJsonFragment(['type' => 'success']);
});

it('update warranty test', function () {
    $request_data = [
        'warranty_date' => Carbon::now()->format('Y-m-d'),
        'product_id' => $this->data_product['product']->id,
        'customer_id' => $this->data_customer->id,
        'status' => 'Receive from Customer'
    ];
    login()->postJson($this->api_url, $request_data)
        ->assertStatus(200);
    $warranty = Warranty::first();
    login()->patchJson($this->api_url . '/' . $warranty->id, $request_data)
        ->assertStatus(200)
        ->assertJsonFragment(['type' => 'success']);
});

it('delete warranty test', function () {
    $request_data = [
        'warranty_date' => Carbon::now()->format('Y-m-d'),
        'product_id' => $this->data_product['product']->id,
        'customer_id' => $this->data_customer->id,
        'status' => 'Receive from Customer'
    ];
    login()->postJson($this->api_url, $request_data)
        ->assertStatus(200);
    $warranty = Warranty::first();
    login()->deleteJson($this->api_url . '/' . $warranty->id)
        ->assertStatus(200)
        ->assertJsonFragment(['type' => 'success']);
});

it('validate warranty test', function ($key) {
    $request_data = [
        'warranty_date' => Carbon::now()->format('Y-m-d'),
        'product_id' => $this->data_product['product']->id,
        'customer_id' => $this->data_customer->id,
        'status' => 'Receive from Customer'
    ];
    unset($request_data[$key]);
    $keyInMessage = str()->snake($key);
    $keyInMessage = str_replace('_', ' ', $keyInMessage);
    login()->postJson($this->api_url, $request_data)
        ->assertStatus(422)->assertJsonFragment([
            $key => ["The " . $keyInMessage . " field is required."]
        ]);
})->with([
    'warranty_date',
    'product_id',
    'customer_id',
    'status'
]);
