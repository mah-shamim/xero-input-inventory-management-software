<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\WarrantyRequest;
use App\Models\Inventory\Customer;
use App\Models\Inventory\Product;
use App\Models\Inventory\Warranty;
use App\Services\Inventory\Warranty\WarrantyEditService;
use App\Services\Inventory\Warranty\WarrantyListService;
use App\Services\Inventory\Warranty\WarrantyStoreService;
use App\Services\Inventory\Warranty\WarrantyUpdateService;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{


    public function index(WarrantyListService $warrantyListService, Request $request)
    {
//        @todo
//        $this->authorize('viewAny', Warranty::class);

        $result = $this->renderArrayOutput($warrantyListService, $request, null);

        return $result['warranty'];
    }

    public function create()
    {
        //        @todo
//        $this->authorize('create', Warranty::class);

        return response()->json([
            'products' => Product::productList()->get(),
            'customers' => Customer::customerList()->get(),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function store(WarrantyStoreService $warrantyStoreService, WarrantyRequest $request)
    {
        //        @todo
//        $this->authorize('create', Warranty::class);

        return $this->renderJsonOutput($warrantyStoreService, $request, null);
    }

    public function show($id)
    {
    }

    public function edit($id, WarrantyEditService $warrantyEditService, Request $request)
    {
        //        @todo
//        $this->authorize('update', Warranty::find($id));

        return $this->renderJsonOutput($warrantyEditService, $request, ['id' => $id]);
    }

    public function update(WarrantyUpdateService $warrantyUpdateService, WarrantyRequest $request, $id)
    {
        //        @todo
//        $this->authorize('update', Warranty::find($id));

        return $this->renderJsonOutput($warrantyUpdateService, $request, ['id' => $id]);
    }

    public function destroy($id)
    {
        //        @todo
//        $this->authorize('delete', Warranty::find($id));
        $warranty = Warranty::find($id);
        $warranty->delete();

        return response()->json([
            'message' => 'Warranty has been deleted Successfully',
            'type' => 'success',
        ]);
    }
}
