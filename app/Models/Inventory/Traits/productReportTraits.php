<?php

namespace App\Models\Inventory\Traits;

use App\Models\Inventory\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait productReportTraits
{
    public function productReport(Request $request)
    {

        $this->checkAuthorization('productReport', 'index');
        $productList = $this->productQueryBuild($request);

        return response()->json($productList);

    }

    /**
     * @return mixed
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function productQueryBuild()
    {

        $products = Product::select('products.id', 'products.name', 'products.code')
            ->selectRaw('round(COALESCE(pw.remaining_quantity, 0), 4) as remaining_quantity')
            ->selectRaw('round(COALESCE(pw.remaining_weight, 0), 4) as remaining_weight')
            ->selectRaw('round(COALESCE(pp.purchased_total, 0), 4) as purchased_total')
            ->selectRaw('round(COALESCE(ps.sold_total, 0), 4) as sold_total')
            ->leftJoinSub(function ($query) {
                $query->select(
                    'product_id',
                    DB::raw('SUM(quantity) as remaining_quantity'),
                    DB::raw('SUM(weight) as remaining_weight')
                )
                    ->from('product_warehouse')
                    ->groupBy('product_id');
            }, 'pw', 'pw.product_id', '=', 'products.id')
            ->leftJoinSub(function ($query) {
                $query->select('product_id',
                    DB::raw('SUM(subtotal) as purchased_total'),
                )
                    ->from('product_purchase')
                    ->groupBy('product_id');
            }, 'pp', 'pp.product_id', '=', 'products.id')
            ->leftJoinSub(function ($query) {
                $query->select('product_id',
                    DB::raw('SUM(subtotal) as sold_total'),
                )
                    ->from('product_sale')
                    ->groupBy('product_id');
            }, 'ps', 'ps.product_id', '=', 'products.id')
            ->groupBy('products.id')
            ->fromCompany();

        $query = request()->query();
        $pages = array_key_exists('itemsPerPage', $query) && $query['itemsPerPage']
            ? $query['itemsPerPage']
            : getResultPerPage();

        if (request()->get('name')) {
            $products->where('products.name', 'LIKE', request()->get('name').'%');
        }
        if (request()->get('code')) {
            $products->where('products.code', 'LIKE', request()->get('code').'%');
        }

        return $products->paginate($pages);
    }
}
