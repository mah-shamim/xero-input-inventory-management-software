<?php

namespace Tests\Browser\Payroll;

use App\Models\Payroll\Department;
use App\Models\Payroll\Employee;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use function Laravel\Prompts\pause;


class EmployeeDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
        $this->department = Department::factory()->create(['company_id' => $this->user->company_id]);
        $this->page = '/payroll/employee';
    }

    /**
     * @test
     */
    public function index_employee_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $browser->loginAs($user)
                ->visit($this->page);
        });
    }

    /**
     * @test
     */
    public function create_employee_dusk(): void
    {

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $browser->loginAs($user)
                ->visit($this->page)
                ->click('@create')
                ->pause(1000)
                ->waitFor('#create_edit_employee')
                ->assertSee('Employee Create')
                ->type('@employee_id', fake()->numberBetween(1, 2))
                ->type('@name', fake()->name)
                ->randomVSelect('#department')
                ->type('@designation', fake()->jobTitle)
                ->randomVSelect('#contract_type')
                ->type('@salary', fake()->numberBetween(1, 2))
                ->type('@mobile', fake()->e164PhoneNumber)
                ->pickADate('@join_date')
                ->pause(1000)
                ->type('@nid', fake()->uuid)
                ->type('@emergency', fake()->e164PhoneNumber)
                ->type('@address', fake()->streetAddress)
                ->press('@submit')
                ->pause(400)
                ->seeSweetAlertSuccessMessage('Employee has been created successfully');
        });
    }

    /**
     * @test
     */
    public function edit_employee_dusk(): void
    {
        $employee = Employee::factory()
            ->create([
                'company_id' => $this->user->company_id,
                'department_id' => $this->department->id
            ]);

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $browser->loginAs($user)
                ->visit($this->page)
                ->pause(1000)
                ->click('@action')
                ->pause(100)
                ->click('@edit-0')
                ->waitFor('#create_edit_employee')
                ->assertSee('Employee Edit')
                ->pause(500)
                ->type('@name', ' update')
                ->press('@submit')
                ->pause(400)
                ->seeSweetAlertSuccessMessage('Employee has been updated successfully');
        });
    }

    /**
     * @test
     */
    public function delete_employee_dusk(): void
    {
        $employee = Employee::factory()
            ->create([
                'company_id' => $this->user->company_id,
                'department_id' => $this->department->id
            ]);

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $browser->loginAs($user)
                ->visit($this->page)
                ->pause(1000)
                ->click('@action')
                ->pause(100)
                ->clickSwalDeleteButton()
                ->pause(400)
                ->seeSweetAlertDeleteMessage('Employee has been deleted successfully');
            ;
        });

    }
}