<?php
/**
 * Created by PhpStorm.
 * User: Sharif
 * Date: 9/29/2019
 * Time: 7:05 PM
 */
namespace App\Services\Inventory\UnitConversion;

use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class UnitConversionIndexService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $unitConversion = UnitConversion::with('from_unit', 'to_unit')
            ->whereCompanyId(request()->input('company_id'));
        $query = request()->query();

        if ($query) {
            if(array_key_exists('search', $query) && $query['search']){
                $unitConversion->whereHas('from_unit', function($q) use ($query){
                    $q->where('key', 'like', '%' . $query['search'] . '%');
                })->orWhereHas('to_unit', function($q) use ($query){
                    $q->where('key', 'like', '%' . $query['search'] . '%');
                });
            }
            if(array_key_exists('sortBy', $query) && $query['sortBy']){
                if($query['sortBy']==='from_unit' or $query['sortBy']==='to_unit'){
                    //
                }else{
                    $unitConversion->orderBy($query['sortBy'], $query['sortDesc']==='false'?'asc':'desc');
                }
            }
        }

        $array['units'] = $unitConversion->paginate(getResultPerPage());
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