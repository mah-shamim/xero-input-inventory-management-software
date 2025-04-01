<?php

namespace Tests\Browser\Payroll;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SalaryDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->page = '/payroll/salary';
        $this->user = $this->seed_and_getUser();
        $this->employee = $this->getEmployee_seed($this->user);
    }

    /**
     * @test
     */
    public function index_salary_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Salaries')
                ->pause(100);
        });
    }

    /**
     * @test
     */
    public function create_salary_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Salaries')
                ->pause(100)
                ->click('@create')
                ->pause(100)
                ->waitFor('#salary_create_edit')
                ->vAutocompleteWithSelector(
                    '@employee_id',
                    'employee_id',
                    $this->employee['name']
                )
                ->type('@amount', fake()->numberBetween(100, 200))
                ->type('@festival_bonus', fake()->numberBetween(10, 20))
                ->type('@other_bonus', fake()->numberBetween(5, 10))
                ->type('@deduction', fake()->numberBetween(1, 2))
                ->randomVSelect('#payment_method')
                ->press('@submit')
                ->pause(200)
                ->seeSweetAlertSuccessMessage('Salary has been created successfully');
        });
    }

    /**
     * @test
     */
    public function update_salary_dusk(): void
    {
        $this->create_salary_dusk();
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Salaries')
                ->pause(1000)
                ->click('@action')
                ->pause(100)
                ->click('@edit-0')
                ->pause(100)
                ->waitFor('#salary_create_edit')
                ->type('@amount', fake()->numberBetween(100, 200))
                ->press('@submit')
                ->pause(200)
                ->seeSweetAlertSuccessMessage('Salary has been updated successfully');
        });
    }

    /**
     * @test
     */
    public function delete_salary_dusk(): void
    {
        $this->create_salary_dusk();
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Salaries')
                ->pause(1000)
                ->click('@action')
                ->pause(100)
                ->clickSwalDeleteButton()
                ->pause(400)
                ->seeSweetAlertDeleteMessage('Salary has been delete successfully');
        });
    }
}