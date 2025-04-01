<?php

use App\Models\User;

test('confirm password screen can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

//test('password can be confirmed', function () {
//    $user = User::factory()->create();
//
//    $response = $this->actingAs($user)->post('/forgot-password', [
//        'password' => 'password',
//    ]);
//
//    $response->assertRedirect();
//    $response->assertSessionHasNoErrors();
//});
//
//test('password is not confirmed with invalid password', function () {
//    $user = User::factory()->create();
//
//    $response = $this->actingAs($user)->post('/reset-password', [
//        'password' => 'wrong-password',
//    ]);
//
//    $response->assertSessionHasErrors();
//});
