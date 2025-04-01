<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/8/2018
 * Time: 1:18 PM
 */

namespace App\Services\Inventory\Warehouse;


use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class WarehouseListService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId = request()->input('company_id');
        $warehouses = Warehouse::whereCompanyId($companyId)->select();
        $query = request()->query();
        $itemPerPage = null;
        if ($query) {
            $keys = array_keys($query);

            foreach ($keys as $key) {
                if ($key == 'name') {
                    $warehouses->where($key, 'like', '%' . $query[$key] . '%');

                }
                if ($key == 'code') {
                    $warehouses->where($key, 'like', '%' . $query[$key] . '%');
                }
            }

            if(array_key_exists('itemsPerPage', $query) && $query['itemsPerPage']){
                $itemPerPage = $query['itemsPerPage'];
            }

            if(array_key_exists('search', $query) && $query['search']){
                $warehouses
                    ->where('name', 'like', '%' . $query['search'] . '%')
                    ->orwhere('code', 'like', '%' . $query['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $query['search'] . '%')
                    ->orWhere('email', 'like', '%' . $query['search'] . '%');
            }
            if(array_key_exists('sortBy', $query) && $query['sortBy']){
                $warehouses->orderBy($query['sortBy'], $query['sortDesc']=='false'?'asc':'desc');
            }
        }


        $array['warehouses']= request()->input('dropdown') ? Warehouse::warehouseList()->get() : $warehouses->paginate(getResultPerPage($itemPerPage));
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