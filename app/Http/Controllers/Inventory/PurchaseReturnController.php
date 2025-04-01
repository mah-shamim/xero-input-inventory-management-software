<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\PurchaseReturenRequest;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\Inventory\Returns\Purchase\PurchaseReturnUpdateService;
use App\Traits\ReturnControllerTraits;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    use ReturnControllerTraits;

    public function show($id)
    {
        $this->checkAuthorization('purchase', 'return');
        $purchase = Purchase::with(['products.units', 'payments'])
            ->whereId($id)
            ->whereCompanyId(request()->input('company_id'))
            ->first();

        quantityStrConversion($purchase);
        $warehouses = Warehouse::warehouseList()->get();

        return response()->json([
            'purchase' => $purchase,
            'warehouses' => $warehouses,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function update(
        PurchaseReturnUpdateService $purchaseReturnUpdateService,
        PurchaseReturenRequest $request,
        $id
    ) {
        $this->checkAuthorization('purchase', 'return');

        return $this->renderJsonOutput($purchaseReturnUpdateService, $request, ['id' => $id]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'returnable_id' => 'required',
            'price' => 'required',
        ]);

        $returns = $this->findReturnObject($request, Purchase::classPath());

        $returns->update(['amount' => $request->input('price')]);

        $this->transactionUpdate(
            $request,
            $returns,
            $this->getReturnSum($request, Purchase::classPath())
        );

        return response()->json([
            'type' => 'success',
            'message' => 'Amount has been updated',
        ]);
    }
}
