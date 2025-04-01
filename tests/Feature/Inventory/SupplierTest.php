<?php


use App\Models\Inventory\Supplier;

function create_supplier($user): mixed
{
    return Supplier::factory()->create([
        'company_id' => $user->company_id,
    ]);
}

function validate_supplier_data($key = null): array
{
    $data = [
        'name' => fake()->name,
        'email' => fake()->email,
        'code' => fake()->currencyCode,
        'company' => fake()->company,
        'address' => fake()->address,
        'phone' => fake()->phoneNumber,
    ];

    if ($key) unset($data[$key]);

    return $data;
}

it('supplier index page is ok', function () {
    $response = $this->signIn()->get('/api/inventory/suppliers');
    $response->assertStatus(200);
});

it('create supplier test', function () {
    $data = validate_supplier_data();
    $response = $this->signIn()
        ->postJson('/api/inventory/suppliers', $data);
    $this->assertDatabaseHas('suppliers', $data);
    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('update supplier test', function () {
    $this->signIn();
    $supplier = create_supplier(auth()->user());
    $data = [
        'name' => $name = $supplier->name . ' update',
        'code' => $code = $supplier->code . 'update',
        'address' => $address = $supplier->address . 'update',
        'email' => $email = $supplier->email . 'update',
        'phone' => $phone = $supplier->phone,
        'company' => $company = $supplier->company . ' update',
    ];

    $response = $this->patchJson("/api/inventory/suppliers/{$supplier->id}", $data);
    $this->assertDatabaseHas('suppliers', [
        'name' => $name,
        'code' => $code,
        'address' => $address,
        'email' => $email,
        'phone' => $phone,
        'company' => $company
    ]);
    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});


it('delete supplier test', function () {
    $this->signIn();
    $supplier = create_supplier(auth()->user());
    $id = $supplier->id;
    $response = $this->deleteJson("/api/inventory/suppliers/{$id}", []);

    $this->assertSoftDeleted($supplier);

    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});


it('validation supplier name', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/suppliers', validate_supplier_data('name'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'name' => ['The name field is required.']
        ]);
});

it('validation supplier email', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/suppliers', validate_supplier_data('email'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'email' => ['The email field is required.']
        ]);
});

it('validation supplier phone', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/suppliers', validate_supplier_data('phone'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'phone' => ['The phone field is required.']
        ]);
});

it('validation supplier company', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/suppliers', validate_supplier_data('company'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'company' => ['The company field is required.']
        ]);
});

it('validation supplier code', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/suppliers', validate_supplier_data('code'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'code' => ['The id field is required.']
        ]);
});


