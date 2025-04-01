<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\SaleReturnRequest;
use App\Models\Inventory\Returns;
use App\Services\Inventory\Returns\Sale\SaleReturnCreateService;
use App\Services\Inventory\Returns\Sale\ShowSaleReturnService;
use App\Traits\ReturnControllerTraits;
use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    use ReturnControllerTraits;

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index(
        ShowSaleReturnService $showSaleReturnService,
        Request $request,
        $id
    ) {
        $this->checkAuthorization('sales', 'return');

        return $this->renderJsonOutput($showSaleReturnService, $request, ['id' => $id]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function edit($sale_id, $return_id)
    {
        $return = Returns::with('returnable.products', 'returnable.products.units', 'returnable.payments')
            ->where('returnable_id', $sale_id)
            ->where('id', $return_id)
            ->where('company_id', request()->input('company_id'))
            ->first();

        return response()->json($return)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return string
     */
    public function update(Request $request, $sale_id, $return)
    {
        $request->validate([
            'price' => 'required',
            'returnable_id' => 'required',
        ]);

        $returns = $this->findReturnObject($request, "App\Models\Inventory\Sale");

        $returns->update(['amount' => $request->input('price')]);

        $this->transactionUpdate(
            $request,
            $returns,
            $this->getReturnSum($request, "App\Models\Inventory\Sale")
        );

        return response()->json([
            'type' => 'success',
            'message' => 'Amount has been updated',
        ]);
    }

    /**
     * @return string
     */
    public function store(
        SaleReturnCreateService $saleReturnCreateService,
        SaleReturnRequest $request,
        $id
    ) {
        $this->checkAuthorization('sales', 'return');

        return $this->renderJsonOutput($saleReturnCreateService, $request, ['id' => $id]);
    }
}
