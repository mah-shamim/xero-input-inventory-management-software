<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 5/12/2018
 * Time: 3:42 PM
 */

namespace App\Models\Inventory\Traits;

use App\Models\Inventory\Purchase;
use App\Models\Inventory\Supplier;
use Illuminate\Support\Facades\DB;

trait SupplierReport
{
    public function supplierReport()
    {
        $suppliers = Supplier::select('suppliers.*',
            DB::raw('round(coalesce(purchased_total, 0), 4) as purchased_total'),
            DB::raw('round(coalesce(sum(p.paid), 0), 4) as paid_total'),
            DB::raw('coalesce(purchased_total - sum(p.paid), 0) as due')
        )
            ->leftJoinSub(function ($query) {
                $query->select(
                    'supplier_id',
                    DB::raw('SUM(total) as purchased_total'),
                )
                    ->from('purchases')
                    ->groupBy('supplier_id');
            }, 'pu', 'pu.supplier_id', '=', 'suppliers.id')
            ->leftjoin('purchases as pur', 'pur.supplier_id', '=', 'suppliers.id')
            ->leftjoin('payments as p', function ($join) {
                $join->on('p.paymentable_id', '=', 'pur.id')
                    ->where('p.paymentable_type', get_class(new Purchase()));
            })
            ->fromCompany()
            ->groupBy('suppliers.id');

        $pages = request()->get('itemsPerPage');

        if (request()->get('name')) {
            $suppliers->where('suppliers.name', 'like', '%'.request()->get('name').'%');
        }
        if (request()->get('company')) {
            $suppliers->where('suppliers.company', 'like', '%'.request()->get('company').'%');
        }
        if (request()->get('phone')) {
            $suppliers->where('suppliers.phone', 'like', '%'.request()->get('phone').'%');
        }
        if (request()->get('code')) {
            $suppliers->where('suppliers.code', 'like', '%'.request()->get('code').'%');
        }

        $suppliers = $suppliers->paginate($pages);

        return response()->json($suppliers);
    }
}
