<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/12/2017
 * Time: 10:46 AM
 */

namespace App\Services\Inventory\Warehouse;


use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class WarehouseCreateService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $name = $request->get('code');
        $count = Warehouse::where('code', $name)->whereCompanyId($request->input('company_id'))->count();
        if($request->input('is_default')){
            toggle_default(Warehouse::class);
        }
        if ($count > 0) {
            $result['message'] = 'Warehouse code already exits';
            return $result;
        }
        $result['type'] = 'success';
        return $result;
    }

    public function execute($array, Request $request)
    {
        Warehouse::create($request->all());
        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Warehouse created successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }

}