<?php

use App\Models\Payroll\Department;
use App\Models\Payroll\Employee;
use App\Models\Payroll\Salary;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

uses()->group('salaryTest');

it('has payroll/salary page', function () {
    login()->getJson(route('salary.index'))
        ->assertStatus(200);
});


it('can create payroll/salary', function () {

    login()
        ->postJson(route('salary.store'), $data = postSalary())
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);

    $this->assertDatabaseHas('salaries', [
        'employee_id' => $data['employee_id']
    ]);
});

it('can update payroll/salary', function () {
    login()->postJson(route('salary.store'), $data = postSalary())
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);

    $this->assertDatabaseHas('salaries', [
        'employee_id' => $data['employee_id']
    ]);

    $salary = Salary::where('company_id', auth()->user()->company_id)->first();
    $data['amount'] = 1000;
    patchJson(route('salary.update', $salary->id), $data)
        ->assertStatus(200);
    $this->assertDatabaseHas('salaries', [
        'amount' => $salary['amount']
    ]);
});

it('can delete payroll/salary', function () {
    login()->postJson(route('salary.store'), $data = postSalary())
        ->assertStatus(200)
        ->assertJson(['type' => 'success']);

    $this->assertDatabaseHas('salaries', [
        'employee_id' => $data['employee_id']
    ]);

    $salary = Salary::where('company_id', auth()->user()->company_id)->first();

    \Pest\Laravel\deleteJson(route('salary.destroy', $salary->id))->assertStatus(200);
});

it('can validate payroll/salary', function ($key) {
    login();
    $data = postSalary();
    $message = "The " . str_replace('_', ' ', $key) . " field is required.";
    unset($data[$key]);
    postJson(route('salary.store'), $data)
        ->assertStatus(422)
        ->assertJsonFragment([
            $key => [$message]
        ]);

})->with('requiredDataSalary');

dataset('requiredDataSalary', [
    'employee_id',
    'amount',
    'salary_date',
    'salary_month',
    'payment_method'
]);

function postSalary(): array
{
    return [
        'employee_id' => Employee::factory()->create([
            'company_id' => auth()->user()->company_id,
            'department_id' => Department::factory()->create([
                'company_id' => auth()->user()->company_id
            ])->id
        ])->id,
        'amount' => fake()->numberBetween(4, 5),
        'salary_date' => fake()->date('Y-m-d'),
        'salary_month' => fake()->date('Y-m'),
        'payment_method' => 1
    ];
}