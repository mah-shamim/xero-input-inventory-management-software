<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/11/2017
 * Time: 2:38 PM
 */

namespace App\Services\Inventory\Brand;


use App\Models\Inventory\Brand;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class BrandStoreService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $name = $request->get('name');
        $count = Brand::where('name', $name)->where('company_id', $request->input('company_id'))->count();
        if ($count > 0) {
            $result['message'] = 'Brand name already exits';
            return $result;
        }
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        $brand = Brand::create($request->all());
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Brand has been created successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}