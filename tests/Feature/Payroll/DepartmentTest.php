<?php

use App\Models\Payroll\Department;

it('has payroll/department page', function () {
    $response = $this->signIn()->get('/api/payroll/department');
    $response->assertStatus(200);
});


it('can store department', function () {
    $data = [
        'name' => 'hello',
    ];
    $response = $this->signIn()->postJson('/api/payroll/department', $data);
    $this->assertDatabaseHas('departments', [
        'name' => $data['name']
    ]);
    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('can update department', function () {
    $data = [
        'name' => 'hello',
    ];
    $response = $this->signIn()->postJson('/api/payroll/department', $data);
    $this->assertDatabaseHas('departments', [
        'name' => $data['name']
    ]);

    $db = Department::where('name', 'hello')->first();
    $this->getJson("/api/payroll/department/{$db->id}/edit")
        ->assertStatus(200);
    $data = [
        'id' => $db->id,
        'name' => 'helloUpdate',
    ];

    $this->patchJson("/api/payroll/department/{$db->id}", $data);
    $this->assertDatabaseHas('departments', [
        'name' => $data['name']
    ]);
    $response->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('can delete department', function () {
    $data = [
        'name' => 'hello',
    ];
    $response = $this->signIn()->postJson('/api/payroll/department', $data);
    $this->assertDatabaseHas('departments', [
        'name' => $data['name']
    ]);

    $db = Department::where('name', 'hello')->first();
    $this->deleteJson("/api/payroll/department/{$db->id}")
        ->assertStatus(200);
});
