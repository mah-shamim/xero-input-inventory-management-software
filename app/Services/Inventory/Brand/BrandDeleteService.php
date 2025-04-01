<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/1/2018
 * Time: 12:09 PM
 */

namespace App\Services\Inventory\Brand;


use App\Models\Inventory\Brand;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class BrandDeleteService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $brand = Brand::whereId($params['id'])->first();
        $count = $brand->products()->count();
        if ($count > 0) {
            $result['message'] = $count . " products exist with this Brand. Please delete corresponding product first";
            return $result;
        }
        $result['brand'] = $brand;
        $result['type'] = "success";
        return $result;
    }

    public function execute($array, Request $request)
    {
        $brand = $array['brand'];
        $brand->delete();
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Brand deleted successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}