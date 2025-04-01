<?php

use App\Models\Inventory\Customer;

function create_customer($user): mixed
{
    return Customer::factory()->create([
        'company_id' => $user->company_id,
    ]);
}

function validate_customer_data($key = null): array
{
    $data = [
        'name' => fake()->name,
        'code' => fake()->currencyCode,
        'email' => fake()->email,
        'address' => fake()->address,
        'phone' => fake()->phoneNumber(14),
    ];

    if ($key) unset($data[$key]);

    return $data;
}

it('customer index page is ok', function () {
    $response = $this->signIn()->getJson('/api/inventory/customers');
    $response->assertStatus(200);
});

it('create customer test', function () {
    $data = validate_customer_data();
    $response = $this->signIn()
        ->postJson('/api/inventory/customers', $data);
    $this->assertDatabaseHas('customers', $data);

    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('update customer test', function () {
    $this->signIn();
    $customer = create_customer(auth()->user());
    $data = [
        'name' => $name = $customer->name . ' update',
        'code' => $code = $customer->code . 'update',
        'address' => $address = $customer->address . 'update',
        'email' => $email = $customer->email . 'update',
        'phone' => $phone = $customer->phone,
    ];

    $response = $this->patchJson("/api/inventory/customers/{$customer->id}", $data);
    $this->assertDatabaseHas('customers', [
        'name' => $name,
        'code' => $code,
        'address' => $address,
        'email' => $email,
        'phone' => $phone,
    ]);
    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('delete customer test', function () {
    $this->signIn();
    $customer = create_customer(auth()->user());
    $id = $customer->id;
    $response = $this->deleteJson("/api/inventory/customers/{$id}", []);

    $this->assertSoftDeleted($customer);

    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('validation customer name', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/customers', validate_customer_data('name'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'name' => ['The name field is required.']
        ]);
});

it('validation customer phone', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/customers', validate_customer_data('phone'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'phone' => ['The phone field is required.']
        ]);
});
