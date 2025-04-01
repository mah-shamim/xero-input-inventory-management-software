<?php

use App\Models\Payroll\Department;
use App\Models\Payroll\Employee;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

uses()->group('employeeTest');

it('has payroll/employee page', function () {
    login()->getJson(route('employee.index'))
        ->assertStatus(200);
});

it('can create payroll/employee', function () {
    login()->postJson(route('employee.store'), $data = postData())
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
    $this->assertDatabaseHas('employees', [
        'nid' => $data['nid']
    ]);
});

it('can update payroll/employee', function () {
    login()->postJson(route('employee.store'), $data = postData())
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
    $employee = Employee::first();
    $data['name'] = $data['name'] . ' update';
    patchJson(route('employee.update', $employee->id), $data)
        ->assertStatus(200);
    $this->assertDatabaseHas('employees', [
        'name' => $data['name']
    ]);
});

it('can delete payroll/employee', function () {
    login();
    $employee = Employee::factory()->create([
        'company_id'=>auth()->user()->company_id,
        'department_id'=>Department::factory()->create(['company_id' => auth()->user()->company_id])->id
    ]);
    deleteJson(route('employee.destroy', $employee->id))
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);
});

it('validate employee data', function ($key) {
    login();
    $data = postData();
    $message = "The " . str_replace('_', ' ', $key) . " field is required.";
    unset($data[$key]);
    postJson(route('employee.store'), $data)
        ->assertStatus(422)
        ->assertJsonFragment([
            $key => [$message]
        ]);
})->with('requiredData');

dataset('requiredData', [
    "nid",
    "name",
    "mobile",
    "salary",
    "address",
    "emergency",
    "employee_id",
    "designation",
    "contract_type",
    "department_id",
    "birth",
    "join_date",
]);

function postData(): array
{
    return [
        "nid" => fake()->uuid,
        "name" => fake()->name,
        "note" => fake()->text,
        "birth" => fake()->date(),
        "mobile" => fake()->e164PhoneNumber(),
        "salary" => fake()->numberBetween([1, 2]),
        "address" => fake()->address,
        "join_date" => fake()->date(),
        "emergency" => fake()->e164PhoneNumber(),
        "employee_id" => fake()->uuid,
        "designation" => fake()->jobTitle,
        "contract_type" => fake()->randomElement([
            'permanent',
            'temporary',
            'contractual',
            'others'
        ]),
        'department_id' => Department::factory()->create(['company_id' => auth()->user()->company_id])->id
    ];
}