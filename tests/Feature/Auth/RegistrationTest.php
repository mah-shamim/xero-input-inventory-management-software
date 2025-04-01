<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;

it('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

it('register new company', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'code' => 'code',
        'title' => 'mr',
        'address1' => 'address',
        'address2' => 'address',
        'contact_name' => 'test',
        'contact_surname' => 'test',
        'contact_phone' => fake()->phoneNumber,
        'is_active' => true,
        'trial_end_at' => now()->addDays(365)
    ]);
//    $this->assertDatabaseHas('users', ['email'=>'test@example.com']);
    $this->get('/home');

});
