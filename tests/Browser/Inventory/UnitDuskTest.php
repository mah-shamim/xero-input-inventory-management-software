<?php

namespace Tests\Browser\Inventory;

use App\Models\Inventory\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UnitDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
        $this->page = '/inventory/units';
    }

    /**
     * @test
     */
    public function index_unit_dusk(): void
    {
        $user = $this->user;
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit($this->page)
                ->assertSee('units');
        });
    }

    /**
     * @test
     */
    public function create_unit_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $name = fake()->colorName;
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->press('@open_unit_dialog')
                ->pause(100)
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($name) {
                    $dialog->assertSee('Unit')
                        ->type('@key', $name)
                        ->assertSee('Primary')
                        ->type('@description', fake()->sentence)
                        ->press('@submit');
                });

            $browser
                ->waitFor('tbody tr .text-left')
                ->assertSee($name)
                ->pause(100)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($success_dialog) {
                    $success_dialog
                        ->waitFor('#swal2-content')
                        ->assertSee('Unit has been created successfully');
                });
        });
    }


    /**
     * @test
     */
    public function update_unit_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $unit = Unit::factory()->create([
                'company_id'=>$this->user->company_id
            ]);
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->waitFor('tbody tr .text-left')
                ->pause(1000)
                ->click('@edit-0')
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($unit) {
                    $dialog->assertSee('Unit')
                        ->pause(1000)
                        ->type('@key', ' update')
                        ->assertSee('Primary')
                        ->press('@submit');
                })
                ->pause(100)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($success_dialog) {
                    $success_dialog
                        ->waitFor('#swal2-content')
                        ->assertSee('Unit has been updated successfully');
                });

                $browser->waitFor('tbody tr .text-left')
                ->pause(1000)
                ->assertSee($unit->key . ' update');
        });
    }

    /**
     * @test
     */
    public function delete_unit_dusk(): void
    {

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $unit = Unit::factory()->create([
                'company_id' => $user->company_id
            ]);
            $browser->loginAs($user)
                ->visit($this->page)
                ->waitFor('tbody tr .text-left')
                ->assertSee($unit->key)
                ->click('@delete-0')
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-actions', function ($dialog) {
                    $dialog->assertSee('Yes, delete it!')
                        ->press('.swal2-confirm.swal2-styled');
                });
            $browser->pause(1000)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($delete_dialog) {
                    $delete_dialog->assertSee('Unit has been deleted successfully');
                });
        });
    }
}
