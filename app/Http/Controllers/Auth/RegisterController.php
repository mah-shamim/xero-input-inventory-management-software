<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Setting;
use App\Models\User;
use App\Rules\PhoneNumber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
//    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $validation = Validator::make($data, [
            'title' => ['required', Rule::in(['Mr', 'Mrs'])],
            'name' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_phone' => ['required', new PhoneNumber],
            'address1' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        return $validation;
    }

    protected function create(array $data): \App\Models\User
    {
//        dd('hello another');
        $company = $this->companyCreate($data);
        $settings = Setting::factory()->create([
            'company_id'=>$company->id,
            'settings->default_email'=>$data['email'],
            'settings->site_name'=>$data['name'],
        ]);

        return $company->user()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'loggable' => true,

        ]);
    }

    protected function companyCreate(array $data): mixed
    {
//        dd('hello company');
        $company =  Company::create([
            'name' => $data['name'],
            'code' => $data['name'] . '-' . str_random(5),
            'title' => $data['title'],
            'address1' => $data['address1'],
            'address2' => $data['address1'],
            'contact_name' => $data['name'],
            'contact_surname' => $data['name'],
            'contact_phone' => [$data['contact_phone']],
            'is_active' => true,
            'email_verified_at'=>now(),
            'trial_end_at' => now()->addDays(365)
        ]);
        return $company;
    }

//    protected function registered(Request $request, $user): void
//    {
//        $user->sendEmailVerificationNotification();
//    }
}
