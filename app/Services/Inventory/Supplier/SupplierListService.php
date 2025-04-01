<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/8/2018
 * Time: 1:32 PM
 */

namespace App\Services\Inventory\Supplier;


use App\Models\Inventory\Supplier;
use App\Services\ActionIntf;
use App\Services\CommonActionInfMethods;
use Illuminate\Http\Request;

class SupplierListService extends CommonActionInfMethods implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId = request()->input('company_id');
        $suppliers = Supplier::whereCompanyId($companyId);
        $query = request()->query();
//        dd($query, $query['sortBy'][0]);
        if (array_key_exists('lookup', $query)) {
            return $this->lookup($array, $request);
        }
        if ($query) {
            $keys = array_keys($query);
            foreach ($keys as $key) {
                if ($key == 'company' || $key == 'phone') {
                    $suppliers->where($key, 'like', '%' . $query[$key] . '%');
                }
            }
            if(array_key_exists('itemsPerPage', $query) && $query['itemsPerPage']){
                $itemPerPage = $query['itemsPerPage'];
            }
            if(array_key_exists('search', $query) && $query['search']){
                $suppliers
                    ->where('name', 'like', '%' . $query['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $query['search'] . '%')
                    ->orWhere('company', 'like', '%' . $query['search'] . '%')
                    ->orWhere('email', 'like', '%' . $query['search'] . '%');
            }
            if($this->isSortByAndSortOrderExists($query)){
                $suppliers->orderBy($query['sortBy'][0], $query['sortDesc'][0]=='false'?'asc':'desc');
            }
        }
        $array['suppliers']= request()->input('dropdown') ? Supplier::supplierList()->get() : $suppliers->paginate(itemPerPage());

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

    public function lookup($array, Request $request)
    {
        $lookup = $request->input('lookup');
        $array['suppliers'] = Supplier::where('name', 'like', '%' . $lookup . '%')
            ->orWhere('phone', 'like', '%' . $lookup . '%')
            ->where('company_id', $request->input('company_id'))
            ->select('id','name', 'company','phone')
            ->take(itemPerPage())
            ->orderBy('name', 'ASC')
            ->get();
        return $array;
    }

}
