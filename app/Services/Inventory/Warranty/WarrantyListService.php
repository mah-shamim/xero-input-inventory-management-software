<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/11/2019
 * Time: 5:14 PM
 */

namespace App\Services\Inventory\Warranty;


use App\Models\Inventory\Warranty;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class WarrantyListService implements ActionIntf
{
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => ''];
        return $result;
    }

    public function execute($array, Request $request)
    {
        $companyId = request()->input('company_id');
        $warranty = Warranty::with('product', 'customer')->whereCompanyId($companyId)->select();
        $query = request()->query();

        if ($query) {
            $keys = array_keys($query);

            foreach ($keys as $key) {
                if ($key === 'product_name' && request()->input('product_name')) {
                    $warranty->whereHas('product', function ($query) {
                        $query->where('name', 'like', '%'.request()->input('product_name').'%');
                    });
                }
                if ($key == 'customer_name' && request()->input('customer_name')) {
                    $warranty->whereHas('customer', function ($query) {
                        $query->where('name', 'like', '%'.request()->input('customer_name').'%');
                    });
                }
            }
        }
        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $warranty->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }

        $warranties = $warranty->paginate(itemPerPage());
        $warranties->map(function ($d) {
            $d['customer_name'] = $d['customer']['name'];
            $d['product_name'] = $d['product']['name'];
            unset($d['product']);
            unset($d['customer']);
        });
        $array['warranty'] = $warranties;

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
     * @param array $keys
     * @param $warranty
     */
    public function searchPerKey(array $keys, $warranty): void
    {
        foreach ($keys as $key) {
            if ($key === 'product_name' && request()->input('product_name')) {
                $warranty->whereHas('product', function ($query) {
                    $query->where('name', 'like', '%' . request()->input('product_name') . '%');
                });

            }
            if ($key === 'customer_name' && request()->input('customer_name')) {
                $warranty->whereHas('customer', function ($query) {
                    $query->where('name', 'like', '%' . request()->input('customer_name') . '%');
                });
            }
        }
    }

    /**
     * @param $query
     * @param $warranty
     */
    public function searchGlobal($query, $warranty): void
    {
        if (array_key_exists('search', $query) && $query['search']) {
            $warranty->whereHas('customer', function ($customer) use ($query) {
                $customer->where('name', 'like', '%' . $query['search'] . '%');
            })->orWhereHas('product', function ($product) use ($query) {
                $product->where('name', 'like', '%' . $query['search'] . '%');
            });
        }
    }

}
