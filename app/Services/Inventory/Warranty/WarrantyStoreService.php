<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/11/2019
 * Time: 4:21 PM
 */

namespace App\Services\Inventory\Warranty;


use App\Models\Inventory\Warranty;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class WarrantyStoreService implements ActionIntf
{
    public function executePreCondition(Request $request, $params): array
    {
        return ['type' => 'success', 'message' => ''];
    }

    public function execute($array, Request $request)
    {
        $warranty = Warranty::create($request->all());
        $array['warranty'] = $warranty;
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['type']='success';
        $array['message'] = 'Warranty has been created successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        $array['type']='error';
        return $array;
    }
}
