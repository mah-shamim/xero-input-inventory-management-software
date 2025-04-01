<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 2/7/2018
 * Time: 3:55 PM
 */

namespace App\Services\Inventory\Customer;


use App\Models\Inventory\Customer;
use App\Services\ActionIntf;
use App\Services\CommonActionInfMethods;
use Illuminate\Http\Request;

class CustomerListService extends CommonActionInfMethods implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $customers = Customer::whereCompanyId($request->input('company_id'));
        $query = request()->query();
        $itemPerPage = null;
        if (array_key_exists('itemsPerPage', $query) && $query['itemsPerPage']) {
            $itemPerPage = $query['itemsPerPage'];
        }
        if (array_key_exists('lookup', $query)) {
            return $this->lookup($array, $request, $itemPerPage);
        }
        if ($query) {
            $keys = array_keys($query);
            foreach ($keys as $key) {
                if ($key == 'phone') {
                    $customers->where($key, 'like', '%' . $query[$key] . '%');
                }
            }
            if (array_key_exists('search', $query) && $query['search']) {
                $customers
                    ->where('name', 'like', '%' . $query['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $query['search'] . '%')
                    ->orWhere('email', 'like', '%' . $query['search'] . '%');
            }
            $this->implementSortingQuery($query, $customers);
        }


        if (request()->input('dropdown')) {
            $array['customers'] = Customer::customerList()->get();
        } else {
            $array['customers'] = $customers->paginate(itemPerPage());
        }

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

    /**
     * @param $array
     * @param Request $request
     * @param $itemPerPage
     * @return mixed
     */
    public function lookup($array, Request $request, $itemPerPage)
    {
        $lookup = $request->input('lookup');
        $array['customers'] = Customer::where('name', 'like', '%' . $lookup . '%')
            ->orWhere('phone', 'like', '%' . $lookup . '%')
            ->where('company_id', $request->input('company_id'))
            ->select('id', 'name', 'phone', 'is_default')
            ->take(getResultPerPage($itemPerPage))
            ->orderBy('name', 'ASC')
            ->get();
        return $array;
    }
}
