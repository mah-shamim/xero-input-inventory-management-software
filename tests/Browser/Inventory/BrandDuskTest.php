<?php

namespace Tests\Browser\Inventory;

use App\Models\Inventory\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BrandDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
        $this->page = '/inventory/brands';
    }

    /**
     * @test
     */
    public function index_brand_dusk(): void
    {
        $user = $this->user;
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit($this->page)
                ->assertSee('Brands');
        });
    }

    /**
     * @test
     */
    public function create_brand_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $name = fake()->colorName;
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->press('@open_brand_dialog')
                ->pause(100)
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($name) {
                    $dialog->assertSee('Brand')
                        ->type('@name', $name)
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
                        ->assertSee('Brand has been created successfully');
                });

            $browser->type('@search', $name)
                ->assertSee($name);
        });
    }

    /**
     * @test
     */
    public function update_brand_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $brand = Brand::factory()->create([
                'company_id' => $this->user->company_id
            ]);
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->waitFor('tbody tr .text-left')
                ->pause(1000)
                ->click('@edit-0')
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($brand) {
                    $dialog->assertSee('Brand')
                        ->pause(1000)
                        ->type('@name', ' update')
                        ->press('@submit');
                })
                ->pause(100)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($success_dialog) {
                    $success_dialog
                        ->waitFor('#swal2-content')
                        ->assertSee('Brand has been updated successfully');
                });

            $browser->waitFor('tbody tr .text-left')
                ->pause(100)
                ->assertSee($name = $brand->name . ' update');

            $browser->type('@search', $name)
                ->assertSee($name);
        });
    }

    /**
     * @test
     */
    public function delete_brand_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $brand = Brand::factory()->create([
                'company_id' => $user->company_id
            ]);
            $browser->loginAs($user)
                ->visit($this->page)
                ->waitFor('tbody tr .text-left')
                ->assertSee($brand->name)
                ->click('@delete-0')
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-actions', function ($dialog) {
                    $dialog->assertSee('Yes, delete it!')
                        ->press('.swal2-confirm.swal2-styled');
                });
            $browser
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($delete_dialog) {
                    $delete_dialog->assertSee('Brand has been deleted successfully');
                });
        });
    }


}
