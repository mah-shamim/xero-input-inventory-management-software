<?php

namespace Tests\Browser\Inventory;

use App\Models\Inventory\Customer;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CustomerDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
    }

    /**
     * @test
     */
    public function index_customer_dusk(): void
    {
        $user = $this->user;
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/inventory/customers')
                ->pause(400)
                ->assertSee('Customers');
        });
    }

    /**
     * @test
     */
    public function create_customer_dusk(): void
    {

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $browser
                ->loginAs($user)
                ->visit('/inventory/customers')
                ->click('@open_customer_dialog')
                ->pause(1000)
                ->waitFor('#create_customer_dialog')
                ->type('@name', fake()->name)
                ->type('@code', fake()->currencyCode)
                ->type('@email', fake()->email)
                ->type('@phone', fake()->phoneNumber)->press('@submit')
                ->pause(400);

            $browser->seeSweetAlertSuccessMessage('Customer has been created successfully');
        });
    }

    /**
     * @test
     */
    public function update_customer_dusk(): void
    {

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $customer = Customer::factory()->create([
                'company_id' => $user->company_id
            ]);
            $browser->loginAs($user)
                ->visit('/inventory/customers')
                ->waitFor('tbody tr')
                ->pause(400)
                ->click('@edit-0')
                ->pause(1000)
                ->waitFor('#create_customer_dialog')
                ->assertSee('Customer')
                ->pause(1000)
                ->type('@name', ' update')
                ->press('@submit')
                ->pause(400);

            $browser->seeSweetAlertSuccessMessage('Customer has been updated successfully');
        });

    }

    /**
     * @test
     */
    public function delete_customer_dusk(): void
    {

        $this->browse(function (Browser $browser) {
            $user = $this->user;
            $customer = Customer::factory()->create([
                'company_id' => $user->company_id
            ]);
            $browser->loginAs($user)
                ->visit('/inventory/customers')
                ->pause(1000);

            $browser->clickSwalDeleteButton();

            $browser->pause(1000)
                ->seeSweetAlertDeleteMessage('Customer has been deleted successfully');
        });
    }

}
