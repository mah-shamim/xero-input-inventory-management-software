<?php

namespace Tests\Browser\Authentication;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginDuskTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seed_and_getUser();
    }

    /**
     * @test
     */
    public function dusk_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Login')
                ->type('@email', 'test_dusk@test.com')
                ->type('@password', '123123123')
                ->press('@submit');

            $browser->waitForLocation('/home')
                ->pause(1000)
            ->assertUrlIs($this->baseUrl().'/home');
//                ->assertUrlIs($this->baseUrl() . '/inventory/settings');
        });
    }
}
