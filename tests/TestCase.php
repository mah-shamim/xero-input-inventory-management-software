<?php

namespace Tests;

use App\Models\Inventory\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\SharedTestMethods;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, SharedTestMethods;

    public function seed_and_getUser($user = null)
    {
        $this->seed(\Database\Seeders\UsersTableSeeder::class);
        return $this->get_login_user();
    }

    public function get_login_user()
    {
        return User::where('email', 'test@test.com')->first();
    }

    public function signIn(): static
    {
        $user = $this->seed_and_getUser();
        $this->actingAs($user);
        return $this;
    }

    public function customer_seed($user = null): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|Customer
    {
        if ($user) auth()->loginUsingId($user->id);
        else {
            $this->signIn();
        }
        $user = auth()->user();
        $customer = Customer::factory()->create([
            'company_id' => $user->company_id
        ]);
        auth()->logout();
        return $customer;
    }
}
