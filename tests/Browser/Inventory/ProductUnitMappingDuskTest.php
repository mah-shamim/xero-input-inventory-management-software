<?php

namespace Tests\Browser\Inventory;

use App\Models\User;
use Laravel\Dusk\Browser;
use PHPUnit\Util\Test;
use Tests\DuskTestCase;

class ProductUnitMappingDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
        $this->page = '/inventory/productunit';
        $this->data = $this->product_seed_only($this->user);
    }

    /**
     * @test
     */
    public function index_product_unit_mapping_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Product unit mapping');
        });
    }

    /**
     * @test
     */
    public function create_product_unit_mapping_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user);

            $browser->visit($this->page)
                ->assertSee('Product unit mapping')
                ->waitFor('tbody tr')
                ->click('@create')
                ->pause(100)
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) {
                    $dialog->assertSee('Product Unit Mapping');

                })
                ->click('#select_unit')
                ->whenAvailable('.menuable__content__active', function ($dropDown2) {
                    $dropDown2->elements('.v-simple-checkbox')[0]->click();
                    $dropDown2->elements('.v-simple-checkbox')[1]->click();
                })
                ->click('@select_product')
                ->whenAvailable('.menuable__content__active', function ($dropDown2) {
                    $dropDown2->elements('.v-list-item')[0]->click();
                })
                ->click('@submit')
                ->pause(100)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($success_dialog) {
                    $success_dialog
                        ->waitFor('#swal2-content')
                        ->assertSee('Product Unit Mapping has been created successfully');
                });;
        });
    }

    /**
     * @test
     */
    public function search_product_unit_mapping_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $this->create_product_unit_mapping_dusk();
            $browser->pause(100)
                ->type('@search_product', $this->data['product'][0]['name'])
                ->waitFor('tbody tr')
                ->assertSee($this->data['product'][0]['name']);
        });
    }
}
