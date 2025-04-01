<?php

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;


it('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertViewIs('auth.login');
});

it('users can authenticate using the login screen', function () {


    $this->signIn();

    $this->assertDatabaseHas('users', [
        'email' => 'test@test.com',
    ]);

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {

    $this->post('/login', [
        'email' => 'test@test.com',
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
