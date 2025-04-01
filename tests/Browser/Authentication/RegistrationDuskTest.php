<?php

namespace Tests\Browser\Authentication;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegistrationDuskTest extends DuskTestCase
{
    /**
     * @test
     */
    public function dusk_registration(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Register')
                ->value('@title', 'Mr')
                ->type('@name', fake()->name)
                ->type('@company', fake()->company)
                ->type('@email', fake()->email)
                ->type('@contact_phone', fake()->phoneNumber)
                ->type('@address1', fake()->address)
                ->type('@password', $password = fake()->password(10))
                ->type('@password_confirmation', $password);

            $browser->press('@submit')->waitForRoute('home');

        });
    }
}
