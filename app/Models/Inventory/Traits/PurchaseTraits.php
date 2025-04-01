<?php
namespace App\Models\Inventory\Traits;

use App\Models\Inventory\Unit;
use Illuminate\Http\Request;

trait PurchaseTraits
{
    public function purchasesReport(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->checkAuthorization('purchaseReport', 'index');
        $purchaseList = $this->staticVariables(self::$purchaseModel)::fromCompany()->with(['supplier', 'products', 'products.units', 'payments', 'products.unit']);
        $pages = $request->get('itemsPerPage');
        if ($request->get('bill_no')) {
            $purchaseList->where('bill_no', 'like', '%'.$request->get('bill_no').'%');
        }
        if ($request->get('purchase_date') && count($request->get('purchase_date')) > 1) {
            $purchaseList->whereBetween('purchase_date', $request->get('purchase_date'));
        }
        if ($request->get('company')) {
            $purchaseList->whereHas('supplier', function ($purchases) use ($request) {
                $purchases->where('company', 'like', '%'.$request->get('company').'%');
            });
        }
        if ($request->get('product')) {
            $purchaseList->whereHas('products', function ($purchases) use ($request) {
                $purchases->where('name', 'like', '%'.$request->get('product').'%');
            });
        }
        if ($request->get('sortBy') && ! empty($request->get('sortBy'))) {
            $purchaseList->orderBy($request->get('sortBy')[0], $request->get('sortDesc')[0] ? 'desc' : 'asc');
        }
        if ($request->get('warehouse_id')) {
            $purchaseList->whereHas('products', function ($q) {
                $q->where('warehouse_id', request()->input('warehouse_id'));
            });
        }
        if ($request->get('part_number')) {
            $purchaseList->whereHas('partnumbers', function ($q) {
                $q->where('part_number', 'like', request()->get('part_number'));
            });
        }

        $purchaseList = $purchaseList->paginate($pages);

        foreach ($purchaseList as $purchase) {
            foreach ($purchase->products as $product) {
                $product->quantityStr = $product->getQuantityWithConversions($product);
                $product->quantityBaseUnit = $product->getBaseQuantity($product, $product->pivot->quantity, $product->pivot->unit_id);
            }
        }

        return response()->json($purchaseList)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function actualPurchasesReport(Request $request): \Illuminate\Http\JsonResponse
    {
        $model = $this->staticVariables(self::$purchaseModel);
        $actualPurchaseList = $this->reportQueryBuilder(
            $request,
            $model,
            ['supplier', 'products.unit', 'payments']);
        $unitList = Unit::all();
        foreach ($actualPurchaseList as $purchase) {
            foreach ($purchase->products as $product) {
                $product->unit_name = $unitList->first(function ($item) use ($product) {
                    return $item->id == $product->pivot->unit_id;
                })->key;
                $product->quantityBaseUnit = $product->convertToPurchaseQuantity($product, $product->getBaseQuantity($product, $product->pivot->quantity, $product->pivot->unit_id));
            }
        }

        return response()->json($actualPurchaseList);
    }
}
