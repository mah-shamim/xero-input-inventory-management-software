<?php

namespace Tests\Browser\Inventory;

use App\Models\Inventory\Warehouse\Warehouse;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WarehouseDuskTest extends DuskTestCase
{

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();

    }


    /**
     * @test
     */
    public function index_warehouse_dusk(): void
    {
        $user = $this->user;
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/inventory/warehouses')
                ->assertSee('Warehouse');
        });
    }

    /**
     * @test
     */
    public function create_warehouse_dusk(): void
    {
        $user = $this->user;
        $this->browse(function (Browser $browser) use ($user) {
            $name = fake()->name;
            $browser->loginAs($user)
                ->visit('/inventory/warehouses')
                ->click('@open_warehouse_dialog')
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($name) {
                    $dialog->assertSee('Warehouse');
                    $dialog->type('@name', $name)
                        ->type('@code', fake()->currencyCode)
                        ->type('@email', fake()->email)
                        ->type('@phone', fake()->phoneNumber(14))
                        ->press('@submit');
                });
            $browser->waitFor('tbody tr .text-left')
                ->assertSee($name);
        });
    }

    /**
     * @test
     */
    public function update_warehouse_dusk()
    {

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $warehouse = Warehouse::create([
                'name' => fake()->name,
                'code' => fake()->currencyCode,
                'email' => fake()->email,
                'phone' => fake()->phoneNumber(14),
                'company_id' => $user->company_id
            ]);
            $browser->loginAs($user)
                ->visit('/inventory/warehouses')
                ->waitFor('tbody tr .text-left')
                ->click('@edit-0')
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($warehouse) {
                    $dialog->assertSee('Warehouse')
                        ->pause(1000)
                        ->type('@name', $warehouse->name . '-update')
                        ->press('@submit');
                })
                ->waitFor('tbody tr .text-left')
                ->pause(2000)
                ->assertSee($warehouse->name . '-update');
        });

    }

    /**
     * @test
     */
    public function delete_warehouse_dusk(): void
    {

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $warehouse = Warehouse::create([
                'name' => fake()->name,
                'code' => fake()->currencyCode,
                'email' => fake()->email,
                'phone' => fake()->phoneNumber(14),
                'company_id' => $user->company_id

            ]);
            $browser->loginAs($user)
                ->visit('/inventory/warehouses')
                ->waitFor('tbody tr .text-left')
                ->assertSee($warehouse->name)
                ->click('@delete-0')
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-actions', function ($dialog) {
                    $dialog->assertSee('Yes, delete it!')
                        ->press('.swal2-confirm.swal2-styled');
                });
            $browser->pause(1000)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($delete_dialog) {
                    $delete_dialog->assertSee('Warehouse has been deleted.');
                });
            $browser->assertDontSee($warehouse->name);
        });
    }

}
