<?php

use App\Models\Inventory\Unit;

it('unit mapping index page is ok', function () {
    login()
        ->getJson(api_unit_mapping_url())
        ->assertStatus(200)
        ->assertJson(['current_page' => 1]);
})->group('unitMappingIndexTest');

it('create unit mapping test', function () {
    login();
    $data = generateUnitDataForConversion();
    test()
        ->postJson(api_unit_mapping_url(), $data)
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
})->group('createUnitMappingTest');

it('search unit mapping test', function () {
    login();
    $from_unit_id = Unit::factory()->create(['key'=>'Box','company_id' => auth()->user()->company_id]);
    $from_unit_val = 1;
    $to_unit_id = Unit::factory()->create(['key'=>'Piece','company_id' => auth()->user()->company_id])->id;
    $to_unit_val = rand(10, 20);
    $data = [
        'from_unit_id' => $from_unit_id->id,
        'from_unit_val' => $from_unit_val,
        'to_unit_id' => $to_unit_id,
        'to_unit_val' => $to_unit_val
    ];
    test()->postJson(api_unit_mapping_url(), $data);
    test()->getJson(api_unit_mapping_url(), ['search' => $from_unit_id->key])
        ->assertJsonFragment([
            'key' => $from_unit_id->key
        ]);
})->group('searchUnitMappingTest');

it('validate unit mapping test', function ($key) {
    login();
    $data = generateUnitDataForConversion($key);
    test()->postJson(api_unit_mapping_url(), $data)
        ->assertStatus(422)
        ->assertJsonFragment([
            $key => ["The ". str_replace('_', ' ', $key) ." field is required."]
        ]);
})->with([
    'from_unit_id',
    'from_unit_val',
    'to_unit_id',
    'to_unit_val'
])->group('validateUnitMappingTest');

function generateUnitDataForConversion($key = null): array
{
    $from_unit_id = Unit::factory()->create(['key'=>'Box', 'company_id' => auth()->user()->company_id])->id;
    $from_unit_val = 1;
    $to_unit_id = Unit::factory()->create(['key'=>'Piece', 'company_id' => auth()->user()->company_id])->id;
    $to_unit_val = rand(10, 20);
    $data = [
        'from_unit_id' => $from_unit_id,
        'from_unit_val' => $from_unit_val,
        'to_unit_id' => $to_unit_id,
        'to_unit_val' => $to_unit_val
    ];
    if ($key) unset($data[$key]);

    return $data;
}

