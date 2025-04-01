<?php

use App\Models\Inventory\Warehouse\Warehouse;

/**
 * @param $user
 * @return mixed
 */

function create_warehouse($user): mixed
{
    return Warehouse::create([
        'name' => fake()->name,
        'code' => fake()->currencyCode,
        'email' => fake()->email,
        'phone' => fake()->phoneNumber,
        'company_id' => $user->company_id
    ]);
}

it('warehouse index page is ok', function () {
    $response = $this->signIn()->get('/api/inventory/warehouses');
    $response->assertStatus(200);
});

it('create warehouse test', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/warehouses', [
            'name' => $name = fake()->name,
            'code' => fake()->languageCode,
            'address' => fake()->address,
            'email' => fake()->email,
            'phone' => fake()->phoneNumber,
        ]);

    $this->assertDatabaseHas('warehouses', [
        'name' => $name
    ]);
    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});


it('update warehouse test', function () {
    $this->signIn();
    $warehouse = create_warehouse(auth()->user());
    $name = $warehouse->name . ' update';
    $response = $this->patchJson("/api/inventory/warehouses/{$warehouse->id}", [
            'name' => $name,
            'code' => $warehouse->code,
            'address' => $warehouse->address,
            'email' => $warehouse->email,
            'phone' => $warehouse->phone,
        ]);

    $this->assertDatabaseHas('warehouses', [
        'name' => $name
    ]);

    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);

});


it('delete warehouse test', function () {
    $this->signIn();
    $warehouse = create_warehouse(auth()->user());
    $id = $warehouse->id;
    $response = $this->deleteJson("/api/inventory/warehouses/{$warehouse->id}", []);


    $this->assertDatabaseMissing('warehouses', ['id' => $id]);
    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('validation warehouse name', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/warehouses', [
            'code' => fake()->languageCode,
            'address' => fake()->address,
            'email' => fake()->email,
            'phone' => fake()->phoneNumber,
        ]);
    $response->assertStatus(422)
        ->assertJsonFragment(
            [
                'name' => ['The name field is required.']
            ]
        );
});

it('validation warehouse code', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/warehouses', [
            'name' => fake()->name,
            'address' => fake()->address,
            'email' => fake()->email,
            'phone' => fake()->phoneNumber,
        ]);
    $response->assertStatus(422)
        ->assertJsonFragment(
            [
                'code' => ['The code field is required.']
            ]
        );
});

it('validation warehouse phone', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/warehouses', [
            'name' => fake()->name,
            'code' => fake()->languageCode,
            'address' => fake()->address,
            'email' => fake()->email,
        ]);
    $response->assertStatus(422)
        ->assertJsonFragment(
            [
                'phone' => ['The phone field is required.']
            ]
        );
});

it('validation warehouse phone format', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/warehouses', [
            'name' => fake()->name,
            'code' => fake()->languageCode,
            'address' => fake()->address,
            'email' => fake()->email,
            'phone' => fake()->email,
        ]);
    $response->assertStatus(422)
        ->assertJsonFragment(
            [
                'phone' => ['The phone is not valid.']
            ]
        );
});

it('validation warehouse email', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/warehouses', [
            'name' => fake()->name,
            'code' => fake()->languageCode,
            'address' => fake()->address,
            'phone' => fake()->phoneNumber,
        ]);
    $response->assertStatus(422)
        ->assertJsonFragment(
            [
                'email' => ['The email field is required.']
            ]
        );
});

it('validation warehouse email format', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/warehouses', [
            'name' => fake()->name,
            'code' => fake()->languageCode,
            'address' => fake()->address,
            'email' => fake()->address,
            'phone' => fake()->phoneNumber,
        ]);
    $response->assertStatus(422)
        ->assertJsonFragment(
            [
                'email' => ['The email must be a valid email address.']
            ]
        );
});


