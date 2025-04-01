<?php

namespace App\Models\Inventory\Traits;

use Illuminate\Http\Request;

trait SalesReport
{
    public function salesReport(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->checkAuthorization('saleReport', 'index');
        $salesList = $this->staticVariables(self::$salesModel)::fromCompany()->with([
            'customer', 'products', 'payments',
        ]);
        $pages = $request->get('itemsPerPage');

        if ($request->get('ref')) {
            $salesList->where('ref', 'like', '%' . $request->get('ref') . '%');
        }
        if ($request->get('salesman_code')) {
            $salesList->where('salesman_code', 'like', '%' . $request->get('salesman_code') . '%');
        }
        if ($request->get('sales_date') && count($request->get('sales_date')) > 1) {
            $salesList->whereBetween('sales_date', $request->get('sales_date'));
        }
        if ($request->get('customer')) {
            $salesList->whereHas('customer', function ($sales) use ($request) {
                $sales->where('name', 'like', '%' . $request->get('customer') . '%');
            });
        }
        if ($request->get('product')) {
            $salesList->whereHas('products', function ($sales) use ($request) {
                $sales->where('name', 'like', '%' . $request->get('product') . '%');
            });
        }
        if ($request->get('sortBy') && !empty($request->get('sortBy'))) {
            $salesList->orderBy($request->get('sortBy')[0], $request->get('sortDesc')[0] ? 'desc' : 'asc');
        }

        $salesList = $salesList->paginate($pages);
        foreach ($salesList as $sale) {
            foreach ($sale->products as $product) {
                $product->quantityStr = $product->getQuantityWithConversions($product);
            }
        }

        return response()->json($salesList)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
