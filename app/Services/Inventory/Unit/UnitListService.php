<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/8/2018
 * Time: 2:01 PM
 */

namespace App\Services\Inventory\Unit;


use App\Models\Inventory\Unit;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class UnitListService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId=request()->input('company_id');
        $units = Unit::select()->whereCompanyId($companyId);
        $query = request()->query();
        $itemPerPage=null;
        if ($query) {
            if(array_key_exists('itemsPerPage', $query) && $query['itemsPerPage']){
                $itemPerPage = $query['itemsPerPage'];
            }

            if(array_key_exists('search', $query) && $query['search']){
                $units
                    ->where('key', 'like', '%' . $query['search'] . '%')
                    ->orWhere('description', 'like', '%' . $query['search'] . '%')
                ;
            }
            if(array_key_exists('sortBy', $query) && $query['sortBy']){
                $units->orderBy($query['sortBy'], $query['sortDesc']=='false'?'asc':'desc');
            }
        }
        $array['units'] = request()->input('dropdown') ? Unit::unitList()->get() : $units->paginate(getResultPerPage($itemPerPage));

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