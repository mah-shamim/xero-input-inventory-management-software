<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\SupplierRequest;
use App\Models\Inventory\Supplier;
use App\Services\Inventory\Supplier\SupplierCreateService;
use App\Services\Inventory\Supplier\SupplierDeleteService;
use App\Services\Inventory\Supplier\SupplierEditService;
use App\Services\Inventory\Supplier\SupplierListService;
use App\Services\Inventory\Supplier\SupplierUpdateService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{

    public function get_supplier()
    {
        $supplier = Supplier::select('id', 'phone', 'name','company')
            ->where('name', 'like', '%' . request()->get('val') . '%')
            ->orWhere('company', 'like', '%' . request()->get('val') . '%')
            ->orWhere('phone', 'like', '%' . request()->get('val') . '%')
            ->where('company_id', request()->input('company_id'))
            ->take(10)
            ->get();
        return response()->json($supplier)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SupplierListService $supplierListService, Request $request)
    {
//        $customersU = Supplier::get();
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

        $result = $this->renderArrayOutput($supplierListService, $request, null);
        return $result['suppliers'];

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
     * @param SupplierRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierCreateService $supplierCreateService, SupplierRequest $request)
    {
//        return $request->all();
        return $this->renderJsonOutput($supplierCreateService, $request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, SupplierEditService $supplierEditService, Request $request)
    {
        return $this->renderJsonOutput($supplierEditService, $request, [ 'id' => $id ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierUpdateService $supplierUpdateService, Request $request, int $id)
    {

        return $this->renderJsonOutput($supplierUpdateService, $request, ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierDeleteService $supplierDeleteService, Request $request, int $id)
    {
        return $this->renderJsonOutput($supplierDeleteService, $request, [ 'id' => $id ]);

    }
}
