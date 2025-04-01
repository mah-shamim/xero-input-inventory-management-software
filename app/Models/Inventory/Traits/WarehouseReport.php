<?php

/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 1/22/2018
 * Time: 12:13 PM
 */

namespace App\Models\Inventory\Traits;

use App\Models\Inventory\Product;
use App\Models\Inventory\Warehouse\Warehouse;
use Illuminate\Http\Request;

trait WarehouseReport
{
    public function warehouseReport(Request $request)
    {
        $this->checkAuthorization('warehouseReport', 'index');

        if($request->get('product_location')==true){
            $product = Product::fromCompany()
                ->where('id', request()->input('product_id'))
                ->select('id', 'name', 'code')->first();
            $query = Warehouse::fromCompany()
                ->getSingleProductLocationByWarehouse(request()->input('product_id'))
                ->where('id', request()->input('warehouse_id'))
                ->first();
            if($request->get('product_location_value')){
                $query->whereNotNull('pw.location->'.$request->get('product_location_value'));
            }
            return response()->json([
                'product'=>$product,
                'warehouse'=>$query
            ]);
        }
        else{
            $query = Product::fromCompany()->with(['warehouses', 'unit', 'units'])
                ->leftJoin('product_warehouse AS pw', 'products.id', '=', 'pw.product_id')
                ->where('pw.warehouse_id', request()->input('warehouse_id'))->select('products.*', 'pw.weight as weight');
        }

        if (request()->get('name')) {
            $query->where('products.name', 'like', '%'.request()->get('name').'%');
        }
        if (request()->get('code')) {
            $query->where('products.name', 'like', '%'.request()->get('code').'%');
        }

        if (! empty(request()->query('sortBy')) && ! empty(request()->query('sortDesc'))) {
            $query->orderBy(request()->query('sortBy')[0], request()->query('sortDesc')[0] === 'true' ? 'desc' : 'asc');
        }
        // execute database query
        $productStockList = $query->paginate(itemPerPage());
//        dd($productStockList);
        //add quantity in string
        foreach ($productStockList as $product) {
            $product->quantityStr = $product->getQuantityWithConversions($product, true, request()->input('warehouse_id'));
        }
        // return response
        return response()->json($productStockList);
    }

    public function showWarehouseReport(Request $request)
    {
        return response()->json([
            'warehouseList' => Warehouse::warehouseList()->get(),
        ]);
    }
}
