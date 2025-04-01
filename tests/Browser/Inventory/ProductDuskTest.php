<?php

namespace Tests\Browser\Inventory;

use App\Models\Inventory\Brand;
use App\Models\Inventory\Category;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
        $this->page = '/inventory/products';
    }

    /**
     * @test
     */
    public function index_product_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Products')
                ->waitFor('tbody tr');
        });
    }

    /**
     * @test
     */
    public function create_product_dusk(): void
    {
        $this->brand_unit_category_create();

        $this->browse(function (Browser $browser) {
            $name = fake()->name;
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->click('@create_dialog')
                ->pause('100')
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($name) {
                    $dialog->assertSee('Create Product')
                        ->type('@name', $name)
                        ->type('@code', fake()->currencyCode)
                        ->type('@slug', fake()->slug)
                        ->type('buying_price', $buying_price = fake()->randomFloat(3, 1, 100))
                        ->type('@price', $buying_price + fake()->randomFloat(3, 1, 100));
                });
            $browser->waitFor('.v-treeview.v-treeview--hoverable')
                ->assertSee('Search Category')
                ->elements('.v-treeview-node__checkbox')[0]
                ->click();

            $browser->click('@brand_id')
                ->whenAvailable('.menuable__content__active', function ($dropDown) {
                    $dropDown->elements('.v-list-item')[0]->click();
                });

            $browser->click('@base_unit_id')
                ->whenAvailable('.menuable__content__active', function ($dropDown) {
                    $dropDown->elements('.v-list-item')[0]->click();
                });

            $browser->pause(100)
                ->click('@submit_close')
                ->pause(100)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($success_dialog) {
                    $success_dialog
                        ->waitFor('#swal2-content')
                        ->assertSee('Product has been created successfully');
                })
                ->waitFor('tbody tr .text-left')
                ->assertSee($name);
        });
    }

    /**
     * @test
     */
    public function update_product_dusk(): void
    {
        $this->brand_unit_category_create();
        list($brand, $unit, $category) = $this->brand_unit_category_create();

        $product = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'base_unit_id' => $unit->id,
            'brand_id' => $brand->id,
        ]);

        $product->categories()->attach([
            $category->id
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->waitFor('tbody tr .text-left')
                ->pause(1000)
                ->click('@edit-0')
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) use ($product) {
                    $dialog->assertSee('Update Product')
                        ->pause(100)
                        ->type('@name', ' update')
                        ->type('@code', 'up')
                        ->type('@slug', 'new')
                        ->type('buying_price', $buying_price = fake()->randomFloat(3, 1, 100))
                        ->type('@price', $buying_price + fake()->randomFloat(3, 1, 100));
                });

            $browser->waitFor('.v-treeview.v-treeview--hoverable')
                ->assertSee('Search Category')
                ->elements('.v-treeview-node__checkbox')[0]
                ->click();

            $browser
                ->press('@submit_close')
                ->pause(100)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($success_dialog) {
                    $success_dialog
                        ->waitFor('#swal2-content')
                        ->assertSee('Product has been updated successfully');
                })
                ->waitFor('tbody tr .text-left')
                ->assertSee($product->name . ' update');
        });

    }

    /**
     * @test
     */
    public function delete_product_dusk(): void
    {

        $this->browse(function (Browser $browser) {
            $this->brand_unit_category_create();
            list($brand, $unit, $category) = $this->brand_unit_category_create();

            $product = Product::factory()->create([
                'company_id' => $this->user->company_id,
                'base_unit_id' => $unit->id,
                'brand_id' => $brand->id,
            ]);

            $product->categories()->attach([
                $category->id
            ]);
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->waitFor('tbody tr .text-left')
                ->assertSee($product->name)
                ->click('@delete-0')
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-actions', function ($dialog) {
                    $dialog->assertSee('Yes, delete it!')
                        ->press('.swal2-confirm.swal2-styled');
                });
            $browser->pause(1000)
                ->waitFor('.swal2-popup.swal2-modal.swal2-show')
                ->whenAvailable('.swal2-content', function ($delete_dialog) {
                    $delete_dialog->assertSee('Product has been deleted successfully');
                });
        });
    }

    /**
     * @test
     */
    public function search_product_by_brand_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            list($product1, $product2, $brand, $brand2, $category, $category2) = $this->createTwoProductForSearch();
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->waitFor('tbody tr .text-left')
                ->assertSee($product1->name)
                ->assertSee($product2->name);

            $browser->click('@filter_open_close')
                ->waitFor('#filter_panel')
                ->type('@brand_search', $brand2->name)
                ->pause(100)
                ->whenAvailable('.menuable__content__active', function ($dropDown) use ($brand2) {
                    $dropDown
                        ->elements('.v-list-item')[0]
                        ->click();
                });
            $browser
                ->pause(500)
                ->assertDontSee($product1->name)
                ->assertSee($product2->name);

        });
    }


    /**
     * @test
     */
    public function search_product_by_category_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            list($product1, $product2, $brand, $brand2, $category, $category2) = $this->createTwoProductForSearch();
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->waitFor('tbody tr .text-left')
                ->assertSee($product1->name)
                ->assertSee($product2->name);

            $browser->click('@filter_open_close')
                ->waitFor('#filter_panel')
                ->type('@category_search', $category2->name)
                ->pause(100)
                ->whenAvailable('.menuable__content__active', function ($dropDown) use ($category2) {
                    $dropDown
                        ->elements('.v-list-item')[0]
                        ->click();
                });
            $browser
                ->pause(500)
                ->assertDontSee($product1->name)
                ->assertSee($product2->name);

        });
    }

    /**
     * @test
     */
    public function search_product_by_code_or_name_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            list($product1, $product2, $brand, $brand2, $category, $category2) = $this->createTwoProductForSearch();
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->waitFor('tbody tr .text-left')
                ->assertSee($product1->name)
                ->assertSee($product2->name);

            $browser->click('@filter_open_close')
                ->waitFor('#filter_panel')
                ->type('@name_code_search', $product2->name)
                ->pause(500);
            $browser
                ->assertDontSee($product1->name)
                ->assertSee($product2->name);

        });
    }
    /**
     * @return array
     */
    public function brand_unit_category_create(): array
    {
        $category = Category::factory()->create([
            'type' => 'PRODUCT',
            'company_id' => $this->user->company_id
        ]);
        $brand = Brand::factory()->create([
            'company_id' => $this->user->company_id
        ]);
        $unit = Unit::factory()->create([
            'is_primary' => true,
            'company_id' => $this->user->company_id
        ]);

        return [$brand, $unit, $category];
    }

    /**
     * @return array
     */
    function createTwoProductForSearch(): array
    {
        list($brand, $unit, $category) = $this->brand_unit_category_create();

        $product1 = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'base_unit_id' => $unit->id,
            'brand_id' => $brand->id,
        ]);

        $product1->categories()->attach([
            $category->id
        ]);

        list($brand2, $unit2, $category2) = $this->brand_unit_category_create();

        $product2 = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'base_unit_id' => $unit2->id,
            'brand_id' => $brand2->id,
        ]);

        $product2->categories()->attach([
            $category2->id
        ]);
        return array($product1, $product2, $brand, $brand2, $category, $category2);
    }
}
