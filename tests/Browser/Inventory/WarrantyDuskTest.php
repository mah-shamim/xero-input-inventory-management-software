<?php

namespace Tests\Browser\Inventory;

use App\Models\Inventory\Warranty;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WarrantyDuskTest extends DuskTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
        $this->page = '/inventory/warranty/index';
        $this->data = $this->product_seed_only($this->user);
        $this->product = $this->data['product'][0];
        $this->customer = $this->customer_seed($this->user);
    }

    /**
     * @test
     */
    public function index_warranty_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Warranty');
        });
    }

    /**
     * @test
     */
    public function create_warranty_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Warranty')
                ->waitFor('tbody tr')
                ->click('@create')
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) {
                    $dialog->assertSee('Create Warranty');
                });
            $browser->vAutocompleteWithSelector('@search_product', 'product', $this->product->name);
            $browser->pickADate('@calender');

            $browser->type('@quantity', 1)
                ->click('@status')
                ->within('.v-list.v-select-list', function (Browser $select) {
                    $select->elements('.v-list-item')[rand(0, 4)]->click();
                });

            $browser->vAutocompleteWithSelector('@search_customer', 'customer', $this->customer->name);
            $browser->press('@submit')
                ->pause(100);
            $browser->seeSweetAlertSuccessMessage('Warranty has been created successfully');
        });
    }

    /**
     * @test
     */
    public function update_warranty_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user);
            $this->warrantySeed();
            $browser->visit($this->page)
                ->assertSee('Warranty')
                ->waitFor('tbody tr')
                ->waitFor('tbody tr .text-left')
                ->click('@edit-0')
                ->pause(1000)
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) {
                    $dialog->assertSee('Update Warranty');
                });
            $browser->clear('@quantity')
                ->type('@quantity', 2)
                ->click('@status')
                ->within('.v-list.v-select-list', function (Browser $select) {
                    $select->elements('.v-list-item')[rand(0, 4)]->click();
                })
                ->press('@submit')
                ->pause(400)
                ->seeSweetAlertSuccessMessage('Warranty has been updated successfully');
        });
    }

    /**
     * @test
     */
    public function delete_warranty_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user);
            $this->warrantySeed();
            $browser->visit($this->page)
                ->assertSee('Warranty')
                ->waitFor('tbody tr')
                ->waitFor('tbody tr .text-left')
                ->clickSwalDeleteButton();
            $browser->pause(1000)
                ->seeSweetAlertDeleteMessage('Warranty has been deleted successfully');
        });
    }

    /**
     * @test
     */
    public function search_warranty_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user);
            $this->warrantySeed();
            $browser->visit($this->page)
                ->assertSee('Warranty')
                ->waitFor('tbody tr')
                ->waitFor('tbody tr .text-left')
                ->type('@search_product_name', $this->product->name)
                ->waitFor('tbody tr')
                ->assertSee($this->product->name);

            $browser->clear('@search_product_name');
            $browser->type('@search_customer_name', $this->customer->name)
                ->waitFor('tbody tr')
                ->assertSee($this->customer->name);
        });
    }

    /**
     * @return void
     */
    function warrantySeed(): void
    {
        Warranty::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $this->customer->id,
            'product_id' => $this->product->id
        ]);
    }
}
