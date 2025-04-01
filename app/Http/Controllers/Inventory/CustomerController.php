<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\CustomerRequest;
use App\Services\Inventory\Customer\CustomerCreateService;
use App\Services\Inventory\Customer\CustomerDeleteService;
use App\Services\Inventory\Customer\CustomerDueService;
use App\Services\Inventory\Customer\CustomerEditService;
use App\Services\Inventory\Customer\CustomerListService;
use App\Services\Inventory\Customer\CustomerUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function index(CustomerListService $customerListService, Request $request)
    {

//        $customersU = Customer::get();
//
//        foreach ($customersU as $cust){
//            $user = User::whereId($cust->user_id)->first();
//
//            $cust->update([
//                'email'=>$user->email,
//                'loggable'=>$user->loggable,
//                'name'=>$user->name?$user->name:'walkin',
//                'user_id'=>null
//            ]);
//
//            $user->forceDelete();
//        }
//        dd('done');
        $result = $this->renderArrayOutput($customerListService, $request, null);
        return response()->json($result['customers']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerCreateService $customerCreateService, CustomerRequest $request)
    {
        return $this->renderJsonOutput($customerCreateService, $request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, CustomerEditService $customerEditService, Request $request)
    {

        return $this->renderJsonOutput($customerEditService, $request, ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerUpdateService $customerUpdateService, Request $request, $id)
    {

        return $this->renderJsonOutput($customerUpdateService, $request, ['id'=>$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerDeleteService $customerDeleteService, Request $request, $id)
    {
        return $this->renderJsonOutput($customerDeleteService, $request, ['id' => $id]);
    }

    /**
     * @param \App\Services\Inventory\Customer\CustomerDueService $customerDueService
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Exception
     */
    public function getCustomerDue(CustomerDueService $customerDueService, Request $request, $id)
    {
        return $this->renderJsonOutput($customerDueService, $request, ['id' => $id]);
    }
}
