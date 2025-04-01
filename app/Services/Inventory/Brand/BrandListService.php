<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 2/7/2018
 * Time: 1:57 PM
 */

namespace App\Services\Inventory\Brand;


use App\Models\Inventory\Brand;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class BrandListService implements ActionIntf
{

    public function executePreCondition(Request $request, $params): array
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId = request()->input('company_id');
        $brands = Brand::select()->whereCompanyId($companyId);
        $query = request()->query();
        $itemPerPage = null;

        if ($query) {
            $keys = array_keys($query);
            if(array_key_exists('itemsPerPage', $query) && $query['itemsPerPage']){
                $itemPerPage = $query['itemsPerPage'];
            }

            if(array_key_exists('search', $query) && $query['search']){
                $brands
                    ->where('name', 'like', '%' . $query['search'] . '%');
            }
            if(array_key_exists('sortBy', $query) && $query['sortBy']){
                $brands->orderBy($query['sortBy'], $query['sortDesc']=='false'?'asc':'desc');
            }
        }
        $array['brands'] = request()->input('dropdown') ? Brand::brandList()->get() : $brands->paginate(getResultPerPage($itemPerPage));
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