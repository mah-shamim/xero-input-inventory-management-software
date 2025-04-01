<?php

use App\Models\Inventory\Brand;


it('brand index page is ok', function () {
    $response = $this->signIn()->getJson('/api/inventory/brands');
    $response->assertStatus(200);
});

it('create brand test', function () {
    $data = validate_brand_data();
    $response = $this->signIn()
        ->postJson('/api/inventory/brands', $data);
    $this->assertDatabaseHas('brands', $data);

    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('validation brand name', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/brands', validate_brand_data('name'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'name' => ['The name field is required.']
        ]);
});

function validate_brand_data($key = null): array
{
    $data = [
        'name' => fake()->name,
        'description'=>fake()->sentence
    ];

    if ($key) unset($data[$key]);

    return $data;
}

it('update brand test', function () {
    $this->signIn();
    $brand = Brand::factory()->create(
        ['company_id' => auth()->user()->company_id]
    );
    $data = [
        'name' => $brand->name . ' update',
    ];

    $this->patchJson("/api/inventory/brands/{$brand->id}", $data)
        ->assertStatus(200)
        ->assertJson(['type' => 'success'])
    ;

    $this->assertDatabaseHas('brands', $data);
});

it('delete brand test', function () {
    $this->signIn();
    $brand = Brand::factory()->create(
        ['company_id' => auth()->user()->company_id]
    );
    $id = $brand->id;
    $response = $this->deleteJson("/api/inventory/brands/{$id}", []);

    if ($this->isSoftDeletableModel($brand)){
        $this->assertSoftDeleted('brands', $brand);
    }else{
        $this->assertDatabaseMissing('brands', ['id'=>$brand->id]);
    }

    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

