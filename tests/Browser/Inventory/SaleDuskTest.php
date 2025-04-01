<?php

namespace Tests\Browser\Inventory;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SaleDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->page = '/inventory/sales';
        $this->user = $this->seed_and_getUser();
        $this->sale_data = $this->sale_seed_standalone($this->user);
    }

    /**
     * @test
     */
    public function index_sale_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Sales')->pause(100);
        });
    }

    /**
     * @todo need to figure out print button test later
     * @test
     */
    public function create_sale_dusk(): void
    {
        $buttons = [
            '@submit-close',
            '@submit-new',
//            '@submit-print',
            '@submit-view'
        ];

        foreach ($buttons as $buttonName) {
            $this->browse(function (Browser $browser) use ($buttonName) {
                $this->flowTheSaleTest($browser, $buttonName);
                $browser->pause(4000);
                if ($buttonName === '@submit-close') $browser->assertDontSee('Create Sale');
                if ($buttonName === '@submit-new') $browser->assertSee('Create Sale');
                if ($buttonName === '@submit-view') $browser->assertSee('Sale Ledger');
            });
        }
    }

    /**
     * @todo need to figure out print button test later
     * @test
     */
    public function edit_sale_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $buttonName = '@submit-close';
            $this->flowTheSaleTest($browser, $buttonName);

            $browser->visit($this->page)
                ->pause(1000)
                ->assertSee('Sales')
                ->click('@action')
                ->pause(100)
                ->click('@edit-0')
                ->pause(1000)
                ->assertSee('Edit Sale')
//                ->type('@bill_no', '-update')
                ->press('@update')
                ->pause(200)
                ->seeSweetAlertSuccessMessage('Sale has been updated successfully');

        });

    }


    /**
     * @todo need to figure out print button test later
     * @test
     */
    public function delete_sale_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $buttonName = '@submit-close';
            $this->flowTheSaleTest($browser, $buttonName);
            $browser->visit($this->page)
                ->pause(1000)
                ->assertSee('Sales')
                ->click('@action')
                ->pause(100)
                ->clickSwalDeleteButton()
                ->pause(400)
                ->seeSweetAlertDeleteMessage('Sale has been deleted successfully');
        });
    }

    /**
     * @test
     */
    public function pay_sale_dusk()
    {
        $this->browse(function (Browser $browser) {
            $buttonName = '@submit-close';
            $inputDeduction = 10;
            $this->flowTheSaleTest($browser, $buttonName, $inputDeduction);
            $browser->visit($this->page)
                ->pause(1000)
                ->assertSee('Sales')
                ->click('@action')
                ->pause(1000)
                ->click('@payment-0')
                ->pause(2000)
                ->assertSee('Make Payment')
                ->pause(100)
                ->type('@payment_crud_paid', 1)
                ->press('@submit')
                ->seeSweetAlertSuccessMessage('Transaction has been created successfully');
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
    function flowTheSaleTest(Browser $browser, string $value, float $inputDeduction = 0): void
    {
        $browser->loginAs($this->user)
            ->visit($this->page)
            ->pause(1000)
            ->assertSee('Sales')
            ->click('@create')
            ->waitFor('#create_sale_dialog')
            ->pause(1000)
            ->randomVSelect('#status')
            ->type('@shipping_cost', fake()->numberBetween(10, 20))
            ->type('@overall_discount', fake()->numberBetween(0, 10))
            ->vAutocompleteWithSelector(
                '@search_product',
                'search_product',
                $this->sale_data['items'][0]['pname']
            )
            ->pause(3000);
        $total = $browser->element('#total')->getText();

        if ($inputDeduction > 0) {
            $total = $total - $inputDeduction;
        }

        $browser->type('@paid', $total)
//            ->randomVSelect('#bank')
            ->pause(100)
            ->press($value)
            ->pause(200)
            ->seeSweetAlertSuccessMessage('Sale has been created successfully');
    }
}
