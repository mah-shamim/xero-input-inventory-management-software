<?php
namespace Database\Seeders;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SpecifiedSeedsForApplication
{
    public static function specific_user_data(): void
    {
        User::factory()->create([
            'name' => 'test123',
            'company_id'=>Company::factory()->create(),
            'email' => 'test@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123123123'), // password
            'remember_token' => Str::random(10),
        ]);
    }

}