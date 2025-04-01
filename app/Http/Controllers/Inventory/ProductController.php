<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\ProductRequest;
use App\Models\Inventory\Product;
use App\Services\Inventory\Product\ProductCreateService;
use App\Services\Inventory\Product\ProductDeleteService;
use App\Services\Inventory\Product\ProductEditService;
use App\Services\Inventory\Product\ProductListService;
use App\Services\Inventory\Product\ProductUpdateService;
use App\Services\Inventory\Product\ShowProductService;
use Illuminate\Http\Request;

/**
 * Class ProductController
 * @package App\Http\Controllers\Inventory
 */
class ProductController extends Controller
{

    public function index(ProductListService $productListService, Request $request)
    {
//        @todo: authorization
//        $this->authorize('viewAny', Product::class);
        return $this->renderArrayOutput($productListService, $request, null);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, ShowProductService $showProductService)
    {
        //        @todo: authorization
//        $this->authorize('create', Product::class);

        return json_decode($this->renderJsonOutput($showProductService, $request, null));
    }

    public function store(ProductCreateService $productCreateService, ProductRequest $request)
    {
        //        @todo: authorization
//        $this->authorize('create', Product::class);

        return $this->renderJsonOutput($productCreateService, $request, null);
    }


    public function show(int $id)
    {
        $this->checkAuthorization('product', 'view');
        $product = Product::with('categories', 'brands', 'warehouses', 'unit', 'units')->where('company_id', compid())->where('id', $id)->first();

        return response()->json($product)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function edit(int $id, ProductEditService $productEditService, Request $request)
    {
        //        @todo: authorization
//        $this->authorize('update', Product::find($id));

        return $this->renderJsonOutput($productEditService, $request, ['id' => $id]);
    }


    public function update(ProductUpdateService $productUpdateService, ProductRequest $request, int $id)
    {
        //        @todo: authorization
//        $this->authorize('update', Product::find($id));

        return $this->renderJsonOutput($productUpdateService, $request, ['id' => $id]);
    }

    public function destroy(ProductDeleteService $productDeleteService, Request $request, int $id)
    {
        //        @todo: authorization
//        $this->authorize('delete', Product::find($id));
        return $this->renderJsonOutput($productDeleteService, $request, ['id' => $id]);
    }

    public function getProducts()
    {
        $val = request()->input('val');
        $isSale = request()->input('isSale');
        $isPurchase = request()->input('isPurchase');
        $filterWarehouse = request()->input('filterWarehouse');
        if ($isSale) {
            $products = Product::with('warehouses', 'units')->has('warehouses')->has('units')
                ->whereCompanyId(request('company_id'))
                ->where('only_module', '=', null)
                ->orWhere('only_module', '=', 'sale')
                ->productList($val)->get();

            return response()->json(
                [
                    'products' => $products,
                ]
            )->setEncodingOptions(JSON_NUMERIC_CHECK);
        } elseif ($isPurchase) {
            $products = Product::with('warehouses', 'units')
                ->has('units')
                ->whereCompanyId(request('company_id'))
                ->productList($val);

            if (request()->get('with_part_number')) {
                $products->with('partnumbers', function ($p) {
                    $p->whereNull('sale_id');
                });
            }

            $products = $products->get();

            foreach ($products as $product) {
                if ($product->bom) {
                    $items = $product->bill_of_materials()
                        ->with(
                            'unit',
                            'product.bill_of_materials',
                            'product.unit',
                            'product.units',
                            'product.warehouses'
                        )
                        ->get();
                    $product->bom = $items;
                }
            }

            return response()->json(
                [
                    'products' => $products,
                ]
            )->setEncodingOptions(JSON_NUMERIC_CHECK);
        } elseif ($filterWarehouse) {
            $warehouseId = request()->input('warehouseId');
            $products = Product::with('warehouses', 'units')
//                ->whereHas('warehouses', function ($query) use ($warehouseId) {
//                    $query->where('id', $warehouseId);
//                })
//                ->has('units')
//                ->select('id', 'name', 'code', 'price', 'base_unit_id', 'buying_price', 'manufacture_part_number')
                ->whereCompanyId(compid())
                ->orderBy('name', 'ASC')
                ->get();

            return response()->json([
                'products' => $products,
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        } else {
            return response()->json([
                'products' => Product::with('warehouses', 'units')
                    ->whereCompanyId(request()->input('company_id'))
                    ->productList($val)
                    ->get(),
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }
    }

}
