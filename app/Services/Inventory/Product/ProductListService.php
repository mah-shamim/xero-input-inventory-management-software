<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 2/7/2018
 * Time: 4:08 PM
 */

namespace App\Services\Inventory\Product;


use App\Models\Inventory\Product;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductListService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];

        return $result;
    }

    public function execute($array, Request $request)
    {
        $products = Product::whereDoesntHave('bill_of_materials')
            ->with(['brands', 'warehouses', 'categories'])
            ->whereCompanyId($request->input('company_id'));

        $query = request()->query();
        $pages = array_key_exists('itemsPerPage', $query) && $query['itemsPerPage']
            ? $query['itemsPerPage']
            : getResultPerPage();

        if ($query) {
            $keys = array_keys($query);
            foreach ($keys as $key) {
                if ($key == 'name_code_search') {
                    $products->where('name', 'like', '%' . $query[$key] . '%')
                        ->orWhere('code', 'like', '%' . $query[$key] . '%');
                }

                if ($key == 'code' || $key == 'name') {
                    $products->where($key, 'like', '%' . $query[$key] . '%');
                }
                if ($key == 'warehouse_id' && $query[$key]) {
                    $products->whereHas('warehouses', function ($q) use ($query, $key) {
                        $q->where('id', $query[$key]);
                    });
                }
                if ($key == 'brand_name' && $query[$key]) {
                    $products->whereHas('brands', function ($q) use ($query, $key) {
                        $q->where('name', 'like', '%' . $query[$key] . '%');
                    });
                }
                if ($key == 'brand_id' && $query[$key]) {
                    $products->where('brand_id', $request->input('brand_id'));
                }
                if ($key == 'categories' && $query[$key]) {
                    $products->whereHas('categories', function ($q) use ($query, $key) {
                        $categories = gettype($query[$key]) !== 'array' ? [$query[$key]] : $query[$key];
                        $q->whereIn('id', $categories);
                    });
                }
            }
        }
        if (request()->get('sortBy') && !empty(request()->get('sortBy'))) {
            $products->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }
        $array['products'] = request()->input('dropdown')
            ? Product::productList()->get()
            : $products->paginate($pages);

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}