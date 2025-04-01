<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 1/24/2018
 * Time: 3:46 PM
 */

namespace App\Services\Inventory\Product;


use App\Models\Inventory\Product;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class ProductUpdateService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];

        $product = Product::with('units')
            ->whereId($params['id'])
            ->whereCompanyId($request->input('company_id'))
            ->first();

        if (! $product) {
            $result['message'] = 'Selected Product does not exists anymore. Please refresh and try again';

            return $result;
        }

        $result['product'] = $product;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $product = $array['product'];
        $product->update($request->except(['categories', 'weight']));

        $product->units()->syncWithoutDetaching([
            $request->input('base_unit_id') => [
                'weight' => $request->input('weight'),
            ],
        ]);
        $product->categories()->sync($request->input('categories'));
        // if ($product->units->count() === 1) {
        //     $product->units()->sync([$request->input('base_unit_id') => [
        //         'weight' => $request->input('weight')
        //     ]]);
        // }
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Product has been updated successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}
