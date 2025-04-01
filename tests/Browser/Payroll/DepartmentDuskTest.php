<?php

namespace Tests\Browser\Payroll;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DepartmentDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
        $this->page = '/payroll/department';
    }

    /**
     * @test
     */
    public function index_department_dusk(): void
    {
        $user = $this->user;
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit($this->page)
                ->assertSee('Departments');
        });
    }

    /**
     * @test
     */
    public function create_department_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->click('@create')
                ->pause(100)
                ->waitFor('#create_department_dialog')
                ->type('@name', fake()->name)
                ->press('@submit')
                ->pause(200)
                ->seeSweetAlertSuccessMessage('Department has been created successfully');
        });
    }

    /**
     * @test
     */
    public function update_department_dusk(): void
    {
        $this->create_department_dusk();
        $this->browse(function (Browser $browser) {
            $browser
                ->pause(3000)
                ->click('@action')
                ->pause(100)
                ->click('@edit-0')
                ->pause(200)
                ->waitFor('#create_department_dialog')
                ->pause(100)
                ->type('@name', 'update')
                ->press('@submit')
                ->pause(200)
                ->seeSweetAlertSuccessMessage('Department has been updated successfully');
        });
    }

    /**
     * @test
     */
    public function delete_department_dusk() :void
    {
        $this->create_department_dusk();
        $this->browse(function (Browser $browser) {
            $browser
                ->pause(3000)
                ->click('@action')
                ->pause(100)
                ->clickSwalDeleteButton()
                ->pause(1000)
                ->seeSweetAlertDeleteMessage('Department has been deleted successfully');
        });
    }

}
