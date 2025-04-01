<?php

namespace Tests\Browser\Inventory;

use App\Models\Inventory\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UnitMappingDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
        $this->page = '/inventory/unitconversions';
    }

    /**
     * @test
     */
    public function index_unit_mapping_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Unit Conversion')
                ->waitFor('tbody tr');
        });
    }

    /**
     * @test
     */
    public function create_unit_mapping_dusk(): void
    {
        $this->browse(function (Browser $browser) {
            $from_unit_id = Unit::factory()->create(['company_id' => $this->user->company_id]);
            $from_unit_val = 1;
            $to_unit_id = Unit::factory()->create(['company_id' => $this->user->company_id]);
            $to_unit_val = rand(10, 20);
            $data = [
                'from_unit_id' => $from_unit_id,
                'from_unit_val' => $from_unit_val,
                'to_unit_id' => $to_unit_id,
                'to_unit_val' => $to_unit_val
            ];
            $browser->loginAs($this->user)
                ->visit($this->page)
                ->assertSee('Unit Conversion')
                ->waitFor('tbody tr')
                ->click('@create')
                ->pause(100)
                ->waitFor('.v-dialog.v-dialog--active')
                ->whenAvailable('.v-dialog.v-dialog--active', function ($dialog) {
                    $dialog->assertSee('Create Unit');
                });
            $browser->click('@from_unit_id')
                ->whenAvailable('.menuable__content__active', function ($dropDown) use ($data) {
                    $dropDown->elements('.v-list-item')[0]->click();
                });

            $browser->type('@from_unit_val', $data['from_unit_val']);

            $browser->click('@to_unit_id')
                ->whenAvailable('.menuable__content__active', function ($dropDown) use ($data) {
                    $dropDown->elements('.v-list-item')[1]->click();
                });

            $browser->type('@to_unit_val', $data['to_unit_val']);
            $browser->press('@submit');

            $browser->pause(100);
            $browser->seeSweetAlertSuccessMessage('Unit mapping has been created successfully');
        });
    }
}
