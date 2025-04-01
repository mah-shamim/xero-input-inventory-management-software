<?php

namespace Tests\Browser\Inventory;

use App\Models\Inventory\Category;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CategoryDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
        $this->page = '/inventory/categories';
    }

    /**
     * @test
     */
    public function index_category_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Category');
        });
    }

    /**
     * @test
     */
    public function create_category_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $browser
                ->loginAs($user)
                ->visit($this->page)
                ->waitFor('#create_category')
                ->type('@name', fake()->name)
                ->click('@type')
                ->whenAvailable('.menuable__content__active', function ($dropDown) {
                    $dropDown->elements('.v-list-item')[rand(0, 3)]->click();
                })
                ->type('@description', fake()->sentence)
                ->press('@submit');

            $browser->pause(1000)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($success_dialog) {
                    $success_dialog
                        ->waitFor('#swal2-content')
                        ->assertSee('Category has been created successfully');
                });
        });
    }

    /**
     * @test
     */
    public function update_category_dusk(): void
    {
//        v-treeview-node__root
        $this->browse(function (Browser $browser) {
            $category = Category::factory()->create([
                'company_id' => $this->user->company_id
            ]);
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->waitFor('#create_category')
                ->whenAvailable('.v-treeview.theme--light', function ($tree) {
                    $tree->click('.v-treeview-node__root');
                });

            $browser->waitFor('.align-right.v-btn.error')
                ->assertSee($category->name)
                ->type('@name', 'update new')
                ->press('@submit');

            $browser->pause(1000)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($success_dialog) {
                    $success_dialog
                        ->waitFor('#swal2-content')
                        ->assertSee('Category has been updated successfully');
                })
                ->assertSee('update new');
        });
    }

    /**
     * @test
     */
    public function delete_category_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $category = Category::factory()->create([
                'company_id' => $this->user->company_id
            ]);
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->waitFor('#create_category')
                ->whenAvailable('.v-treeview.theme--light', function ($tree) {
                    $tree->click('.v-treeview-node__root');
                });

            $browser->waitFor('.align-right.v-btn.error')
                ->press('@delete')
                ->pause(1000)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->assertSee('Are you sure?')
                ->waitFor('.swal2-confirm.swal2-styled')
                ->pause(1000)
                ->press('.swal2-confirm')
                ->pause(200)
                ->assertSee('Category has been deleted successfully.');
        });
    }
}
