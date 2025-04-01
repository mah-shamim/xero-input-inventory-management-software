<?php

namespace App\Http\Controllers;

use App\Models\Inventory\Customer;
use App\Models\Inventory\Supplier;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
//        return 'hello';
//        $this->migrate_email_from_user_to_CustomerSupplier();

        return view('home');
    }

    public function migrate_email_from_user_to_CustomerSupplier(): void
    {
        $users = User::withTrashed()->take(400)->orderBy('id')->get();

        foreach ($users as $user) {
            $customer = Customer::where('user_id', $user->id)->first();
            $supplier = Supplier::where('user_id', $user->id)->first();
            if ($customer) {
//                dd($customer, $user->name);
                $customer->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_id' => null
                ]);
            }
            if ($supplier) {
//                dd($supplier, $user->name);
                $supplier->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_id' => null
                ]);
            }

            if ($supplier or $customer) {
                User::withTrashed()->where('id', $user->id)->forceDelete();
            }

        }

        $users = User::onlyTrashed()->get();
        foreach ($users as $user) {
            $user->forceDelete();
        }

        $customers = Customer::onlyTrashed()->get();
        $suppliers = Supplier::onlyTrashed()->get();
        foreach ($suppliers as $supplier) {
            $supplier->forceDelete();
        }
        foreach ($customers as $customer) {
            $customer->forceDelete();
        }
    }
}
