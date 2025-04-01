<?php

namespace Tests\Browser\Inventory;

use App\Models\Account;
use App\Models\Inventory\Customer;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\Warehouse\Warehouse;
use Database\Seeders\AccountSeeder;
use Database\Seeders\InitialSeeder;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExpenseDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->page = '/inventory/expenses';
        $this->user = $this->seed_and_getUser();
        $this->warehouse = Warehouse::factory()->create([
            'company_id' => $this->user->company_id,
            'is_default' => true,
        ]);
        $this->supplier = Supplier::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        (new AccountSeeder())->create_accounts([$this->user]);
    }


    /**
     * @test
     */
    public function index_expense_dusk()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Expenses');
        });
    }

    /**
     * @test
     */
    public function create_expense_dusk(): void
    {
        $buttons = [
//            '@pay_later',
            '@pay_now',
        ];
        foreach ($buttons as $button) {
            $this->browse(function (Browser $browser) use ($button) {
                $browser->loginAs($this->user)
                    ->visit($this->page)
                    ->pause(2000)
                    ->assertSee('Expenses')
                    ->click('@create')
                    ->waitFor('#create_edit_expense_dialog')
                    ->pause(2000)
                    ->pickADate('@expense_date')
                    ->pause(1000)
                    ->type('@bill_no', fake()->buildingNumber)
                    ->type('@amount', fake()->numberBetween(10, 20))
                    ->randomVSelect('#accounts')
                    ->pause(200)
                    ->randomVSelect('#warehouse')
                    ->pause(200)
                    ->vAutocompleteWithSelector('@search_user', 'userable_id', $this->supplier->name);
                $browser->press($button);

                $browser->pause(2000)
                    ->waitFor('#global_payment_crud')
                    ->pause(2000);
            });
        }

    }
}