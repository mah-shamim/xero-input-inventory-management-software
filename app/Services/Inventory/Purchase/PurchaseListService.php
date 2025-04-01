<?php
/**
 * Created by PhpStorm.
 * User: mdit-2
 * Date: 2/8/2018
 * Time: 2:10 PM
 */

namespace App\Services\Inventory\Purchase;


use App\Models\Inventory\Purchase;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class PurchaseListService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {

        $companyId = request()->input('company_id');
        $purchases = Purchase::with(['supplier' => function ($s) {
            $s->select('id', 'name', 'company');
        }])->whereCompanyId($companyId);
        $query = request()->query();
        $itemPerPage = null;
        if ($query) {
            if (array_key_exists('itemsPerPage', $query) && $query['itemsPerPage']) {
                $itemPerPage = $query['itemsPerPage'];
            }

            if (array_key_exists('search', $query) && $query['search']) {
                $purchases
                    ->where('ref', 'like', '%' . $query['search'] . '%')
                    ->orWhereHas('supplier', function ($supplier) use ($query) {
                        $supplier->where('name', 'like', '%' . $query['search'] . '%')
                            ->orWhere('company', 'like', '%' . $query['search'] . '%');
                    });
            }
            if (array_key_exists('sortBy', $query) && $query['sortBy']) {
                if($query['sortBy']==='supplier.company'){
//                    $purchases->sortBy($query['sortBy'], $query['sortDesc'] == 'false' ? true : false);
                }else{
                    $purchases->orderBy($query['sortBy'], $query['sortDesc'] == 'false' ? 'asc' : 'desc');
                }
            }

        }
        $purchases = $purchases->orderBy('created_at', 'DESC')->paginate(getResultPerPage($itemPerPage));



        $array['purchases'] = $purchases;


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
