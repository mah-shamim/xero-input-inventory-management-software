<?php

namespace Tests\Browser\Inventory;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PurchaseDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->page = '/inventory/purchases';
        $this->user = $this->seed_and_getUser();
        $this->purchase_data = $this->purchase_seed_standalone($this->user);
    }

    /**
     * @test
     */
    public function index_purchase_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Purchases');
        });
    }

    /**
     * @todo need to figure out print button test later
     * @test
     */
    public function create_purchase_dusk(): void
    {
        $buttons = [
            '@submit-close',
            '@submit-new',
//            '@submit-print',
            '@submit-view'
        ];

        foreach ($buttons as $buttonName) {
            $this->browse(function (Browser $browser) use ($buttonName) {
                $this->flowThePurchaseTest($browser, $buttonName);
                $browser->pause(4000);
                if ($buttonName === '@submit-close') $browser->assertDontSee('Create Purchase');
                if ($buttonName === '@submit-new') $browser->assertSee('Create Purchase');
                if ($buttonName === '@submit-view') $browser->assertSee('Purchase Ledger');
            });
        }
    }

    /**
     * @test
     */
    public function edit_purchase_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $buttonName = '@submit-close';
            $this->flowThePurchaseTest($browser, $buttonName);
            $browser->visit($this->page)
                ->pause(1000)
                ->assertSee('Purchases')
                ->click('@action')
                ->pause(100)
                ->click('@edit-0')
                ->pause(1000)
                ->assertSee('Edit Purchase')
                ->type('@bill_no', '-update')
                ->press('@update')
                ->pause(200)
                ->seeSweetAlertSuccessMessage('Purchase has been updated successfully');
        });
    }


    /**
     * @test
     */
    public function delete_purchase_dusk()
    {
        $this->browse(function (Browser $browser) {
            $buttonName = '@submit-close';
            $this->flowThePurchaseTest($browser, $buttonName);
            $browser->visit($this->page)
                ->pause(1000)
                ->assertSee('Purchases')
                ->click('@action')
                ->pause(100)
                ->clickSwalDeleteButton()
                ->pause(400)
                ->seeSweetAlertDeleteMessage('Purchase has been deleted successfully');
        });
    }

    /**
     * @test
     */
    public function pay_purchase_dusk()
    {
        $this->browse(function (Browser $browser) {
            $buttonName = '@submit-close';
            $inputDeduction = 10;
            $this->flowThePurchaseTest($browser, $buttonName, $inputDeduction);
            $browser->visit($this->page)
                ->pause(1000)
                ->assertSee('Purchases')
                ->click('@action')
                ->pause(1000)
                ->click('@payment-0')
                ->pause(2000)
                ->assertSee('Pay Bill')
                ->pause(100)
                ->press('@submit')
                ->seeSweetAlertSuccessMessage('Transaction has been created successfully');
        });
    }

    /**
     * @test
     */
    public function return_purchase_dusk()
    {
        $this->browse(function (Browser $browser) {
            $buttonName = '@submit-close';
            $this->flowThePurchaseTest($browser, $buttonName);
            $browser->visit($this->page)
                ->pause(1000)
                ->assertSee('Purchases')
                ->click('@action')
                ->pause(1000)
                ->click('@return-0')
                ->pause(1000);

            $total = $browser->element('#return_total')->getText();

            $browser->type('@total', $total)
                ->type('@unit-0', 1)
                ->press('@submit');

        });
    }

    /**
     * @param \Laravel\Dusk\Browser $browser
     * @param string $value
     * @return void
     * @throws \Facebook\WebDriver\Exception\ElementClickInterceptedException
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    function flowThePurchaseTest(Browser $browser, string $value, float $inputDeduction = 0): void
    {
        $browser->loginAs($this->user)
            ->visit($this->page)
            ->pause(2000)
            ->assertSee('Purchases')
            ->click('@create')
            ->waitFor('#create_purchase_dialog')
            ->pause(2000)
            ->pickADate('@purchase_date')
            ->type('@bill_no', fake()->buildingNumber)
            ->vAutocompleteWithSelector('@supplier_id', 'supplier_id', $this->purchase_data['supplier']['company'])
            ->randomVSelect('#status')
            ->type('@shipping_cost', fake()->numberBetween(10, 20))
            ->type('@overall_discount', fake()->numberBetween(0, 10))
            ->vAutocompleteWithSelector('@search_product', 'search_product', $this->purchase_data['product_set_1']['product']['name'])
            ->pause(1000);
        $total = $browser->element('#total')->getText();

        if ($inputDeduction > 0) {
            $total = $total - $inputDeduction;
        }
        $browser->type('@paid', $total)
            ->randomVSelect('#bank')
            ->pause(100)
            ->press($value)
            ->pause(200)
            ->seeSweetAlertSuccessMessage('Purchase has been created successfully');
    }
}
