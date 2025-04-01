<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::factory()->create([
            'name' => 'test123',
            'company_id' => $company1 = Company::factory()->create(),
            'email' => 'test_dusk@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123123123'), // password
            'remember_token' => Str::random(10),
        ]);
        $user = User::factory()->create([
            'name' => 'test123',
            'company_id' => $company2 = Company::factory()->create(),
            'email' => 'test@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123123123'), // password
            'remember_token' => Str::random(10),
        ]);

        Setting::create([
            'version' => 1,
            'company_id' => $company1->id,
            'settings'=>settings_seed()
        ]);
        Setting::create([
            'version' => 1,
            'company_id' => $company2->id,
            'settings'=>settings_seed()
        ]);
    }
}
