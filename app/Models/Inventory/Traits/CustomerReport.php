<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 5/12/2018
 * Time: 3:38 PM
 */

namespace App\Models\Inventory\Traits;

use App\Models\Inventory\Customer;
use App\Models\Inventory\Sale;
use Illuminate\Support\Facades\DB;

trait CustomerReport
{
    public function customerReport()
    {
        $customers = Customer::select('customers.*',
            DB::raw('round(coalesce(sale_total, 0), 4) as sale_total'),
            DB::raw('round(coalesce(sum(p.paid), 0), 4) as paid_total'),
            DB::raw('coalesce(sale_total - sum(p.paid), 0) as due')
        )
            ->leftJoinSub(function ($query) {
                $query->select(
                    'customer_id',
                    DB::raw('SUM(total) as sale_total'),
                )
                    ->from('sales')
                    ->groupBy('customer_id');
            }, 's', 's.customer_id', '=', 'customers.id')
            ->leftjoin('sales as sale', 'sale.customer_id', '=', 'customers.id')
            ->leftjoin('payments as p', function ($join) {
                $join->on('p.paymentable_id', '=', 'sale.id')
                    ->where('p.paymentable_type', get_class(new Sale()));
            })
            ->fromCompany()
            ->groupBy('customers.id');

        $pages = request()->get('itemsPerPage');

        if (request()->get('name')) {
            $customers->where('customers.name', 'like', '%'.request()->get('name').'%');
        }
        if (request()->get('phone')) {
            $customers->where('customers.phone', 'like', '%'.request()->get('phone').'%');
        }
        if (request()->get('code')) {
            $customers->where('customers.code', 'like', '%'.request()->get('code').'%');
        }

        $customers = $customers->paginate($pages);

        return response()->json($customers);
    }
}
