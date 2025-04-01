<?php

namespace Tests\Browser\Inventory;

use App\Models\Inventory\Supplier;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SupplierDuskTest extends DuskTestCase
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
    public function index_supplier_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/inventory/suppliers')
                ->assertSee('Suppliers');
        });
    }

    /**
     * @test
     */
    public function create_supplier_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $name = fake()->name;
            $browser
                ->loginAs($this->user)
                ->visit('/inventory/suppliers')
                ->click('@open_supplier_dialog')
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($name) {
                    $dialog->assertSee('Supplier');
                    $dialog->type('@name', $name)
                        ->type('@code', fake()->currencyCode)
                        ->type('@email', fake()->email)
                        ->type('@company', fake()->company)
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
    public function update_supplier_dusk(): void
    {

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $supplier = Supplier::factory()->create([
                'company_id'=>$user->company_id
            ]);
            $browser->loginAs($user)
                ->visit('/inventory/suppliers')
                ->waitFor('tbody tr .text-left')
                ->pause(1000)
                ->click('@edit-0')
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($supplier) {
                    $dialog->assertSee('Supplier')
                        ->pause(1000)
                        ->type('@name', ' update')
                        ->press('@submit');
                })
                ->waitFor('tbody tr .text-left')
                ->pause(2000)
                ->assertSee($supplier->name . ' update');
        });

    }

    /**
     * @test
     */
    public function delete_supplier_dusk(): void
    {

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $supplier = Supplier::factory()->create([
                'company_id' => $user->company_id
            ]);
            $browser->loginAs($user)
                ->visit('/inventory/suppliers')
                ->waitFor('tbody tr .text-left')
                ->assertSee($supplier->name)
                ->click('@delete-0')
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-actions', function ($dialog) {
                    $dialog->assertSee('Yes, delete it!')
                        ->press('.swal2-confirm.swal2-styled');
                });
            $browser->pause(1000)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($delete_dialog) {
                    $delete_dialog->assertSee('Suppliers has been deleted successfully');
                });
            $browser->assertDontSee($supplier->name);
        });
    }
}