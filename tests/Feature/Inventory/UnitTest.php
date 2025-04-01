<?php


use App\Models\Inventory\Unit;

it('unit index page is ok', function () {
    $response = $this
        ->signIn()
        ->getJson('/api/inventory/units');
    $response->assertStatus(200);
});

it('create unit test', function () {
    $data = validate_unit_data();
    $response = $this->signIn()
        ->postJson('/api/inventory/units', $data);
    $this->assertDatabaseHas('units', $data);

    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('update unit test', function () {
    $this->signIn();
    $unit = Unit::factory()->create(
        ['company_id' => auth()->user()->company_id]
    );
    $data = [
        'key' => substr($unit->key . ' update', 0, 19),
        'is_primary'=>true
    ];
//    dd($unit->id);
    $this->patchJson("/api/inventory/units/{$unit->id}", $data)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);

    $this->assertDatabaseHas('units', $data);
});

it('validation unit key', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/units', validate_unit_data('key'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'key' => ['The key field is required.']
        ]);
});

it('validation unit is_primary', function () {
    $response = $this->signIn()
        ->postJson('/api/inventory/units', validate_unit_data('is_primary'));
    $response->assertStatus(422)
        ->assertJsonFragment([
            'is_primary' => ['The primary field is required.']
        ]);
});

function validate_unit_data($key = null): array
{
    $data = [
        'key' => fake()->colorName,
        'is_primary'=>true
    ];

    if ($key) unset($data[$key]);

    return $data;
}

it('delete unit test', function () {
    $this->signIn();
    $unit = Unit::factory()->create(
        ['company_id' => auth()->user()->company_id]
    );
    $id = $unit->id;
    $response = $this->deleteJson("/api/inventory/units/{$id}", []);

    if ($this->isSoftDeletableModel($unit)){
        $this->assertSoftDeleted('units', $unit);
    }else{
        $this->assertDatabaseMissing('units', ['id'=>$unit->id]);
    }

    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});