<?php

use App\Models\Inventory\Category;

it("category index page is ok", function () {
    $this->signIn();
    Category::factory()->create(
        ['company_id' => auth()->user()->company_id]
    );
    $this->get('/api/inventory/categories')
        ->assertStatus(200)
        ->assertJsonStructure([
            [
                'id',
                'company_id',
                'parent_id',
                'name',
                'type'
            ]
        ]);
});

it('create category test', function () {
    $data = validate_category_data();
    $this->signIn()
        ->postJson('/api/inventory/categories', $data)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);

    $this->assertDatabaseHas('categories', $data);
});

it('update category test', function () {
    $this->signIn();
    $category = Category::factory()->create(
        ['company_id' => auth()->user()->company_id]
    );
    $data = [
        'name' => $category->name . ' update',
        'type' => $category->type,
        'description' => $category->sentence
    ];
    $this->patchJson("/api/inventory/categories/{$category->id}", $data)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
    $this->assertDatabaseHas('categories', $data);
});

it('validation category name', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/categories', validate_category_data('name'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'name' => ['The name field is required.']
        ]);
});

it('validation category type', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/categories', validate_category_data('type'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'type' => ['The type field is required.']
        ]);
});

function validate_category_data($key = null): array
{
    $data = [
        'name' => fake()->name,
        'type' => array_random(['PRODUCT', 'EXPENSE', 'CUSTOMER', 'PRICE']),
        'description' => fake()->sentence
    ];
    if ($key) unset($data[$key]);

    return $data;
}



it('delete category test', function () {
    $this->signIn();
    $category = Category::factory()->create(
        ['company_id' => auth()->user()->company_id]
    );
    $id = $category->id;
    $response = $this->deleteJson("/api/inventory/categories/{$id}", []);

    if ($this->isSoftDeletableModel($category)){
        $this->assertSoftDeleted('categories', $category);
    }else{
        $this->assertDatabaseMissing('categories', ['id'=>$category->id]);
    }

    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});
