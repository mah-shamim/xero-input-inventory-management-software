<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 4/3/2018
 * Time: 5:12 PM
 */

namespace App\Services\Inventory\ProductDamage;

use App\Models\Inventory\ProductDamage;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductDamageListService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];

        return $result;
    }

    public function execute($array, Request $request)
    {
        $productDamages = ProductDamage::join('products', 'products.id', '=', 'product_damages.product_id')
            ->join('units', 'units.id', '=', 'product_damages.unit_id')
            ->select('product_damages.*', 'products.name as product_name', 'units.key as unit_name')
            ->where('product_damages.company_id', compid());

        if ($request->get('name')) {
            $productDamages->where('products.name', 'like', '%'.$request->get('name').'%');
        }
        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $productDamages->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }

        $array['productDamages'] = $productDamages->paginate(getResultPerPage());

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
