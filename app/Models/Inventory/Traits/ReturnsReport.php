<?php

namespace App\Models\Inventory\Traits;

use App\Models\Inventory\Returns;
use Illuminate\Support\Facades\DB;

/**
 * Trait ReturnsReport
 */
trait ReturnsReport
{
    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function purchaseReturn()
    {
        $purchaseReturn = $this->purchaseReturnQuery();
        $paid = $purchaseReturn->pluck('returns.total_return_amount')->first();
        $totalPrice = $purchaseReturn->groupBy('returns.returnable_id')->get()->sum('total_price');
        $balance = $purchaseReturn->groupBy('returns.returnable_id')->get()->sum('balance');
        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $purchaseReturn->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }

        if (request()->get('bill_no')) {
            $purchaseReturn->where('purchases.bill_no', 'LIKE', '%'.request()->get('bill_no').'%');
        }
        if (request()->get('id')) {
            $purchaseReturn->where('purchases.id', 'LIKE', '%'.request()->get('id').'%');
        }

        $purchaseReturnData = $purchaseReturn->groupBy('returns.returnable_id')->paginate(request()->get('itemsPerPage'));

        return response()->json([
            'paid' => $paid,
            'totalPrice' => $totalPrice,
            'balance' => $balance,
            'purchaseReturnData' => $purchaseReturnData,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return mixed
     */
    public function purchaseReturnQuery()
    {
        return Returns::select('returns.*', 'purchases.ref', 'purchases.bill_no', 'total_price')
            ->join('purchases', 'purchases.id', '=', 'returns.returnable_id')
            ->leftJoin(DB::raw('
            (select purchase_id,  (coalesce(sum(ret.quantity*product_purchase.price),0)) as total_price from product_purchase left join returns as ret on 
            ret.returnable_id = product_purchase.purchase_id and ret.product_id = product_purchase.product_id group by purchase_id) as pur
            '), function ($join) {
                $join->on('pur.purchase_id', '=', 'purchases.id');
            })
            ->selectRaw('coalesce(sum(returns.amount), 0) as total_return_amount')
            ->selectRaw('coalesce(sum(returns.amount)-total_price, 0) as balance')
            ->selectRaw('coalesce(total_price - sum(returns.amount), 0) as negative_balance')
            ->where('returns.company_id', request()->input('company_id'))
            ->where('returns.returnable_type', "App\Inventory\Purchase");
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function saleReturn()
    {
        $saleReturn = $this->saleReturnQuery();
        $paid = $saleReturn->pluck('returns.total_return_amount')->first();
        $totalPrice = $saleReturn->groupBy('returns.returnable_id')->get()->sum('total_price');
        $balance = $saleReturn->groupBy('returns.returnable_id')->get()->sum('balance');
        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $saleReturn->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }
        if (request()->get('ref')) {
            $saleReturn->where('sales.ref', 'LIKE', '%'.request()->get('ref').'%');
        }
        $saleReturnData = $saleReturn->groupBy('returns.returnable_id')->paginate(request()->get('itemsPerPage'));

        return response()->json([
            'paid' => $paid,
            'totalPrice' => $totalPrice,
            'balance' => $balance,
            'saleReturnData' => $saleReturnData,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return mixed
     */
    public function saleReturnQuery()
    {
        return Returns::select('returns.*', 'sales.ref', 'total_price')
            ->join('sales', 'sales.id', '=', 'returns.returnable_id')
            ->leftJoin(DB::raw('
            (select sale_id,  (coalesce(sum(ret.quantity*product_sale.price),0)) as total_price from product_sale left join returns as ret on 
            ret.returnable_id = product_sale.sale_id and ret.product_id = product_sale.product_id group by sale_id) as pur
            '), function ($join) {
                $join->on('pur.sale_id', '=', 'sales.id');
            })
            ->selectRaw('coalesce(sum(returns.amount), 0) as total_return_amount')
            ->selectRaw('coalesce(sum(returns.amount)-total_price, 0) as balance')
            ->selectRaw('total_price - coalesce(sum(returns.amount), 0) as negative_balance')
            ->where('returns.company_id', request()->input('company_id'))
            ->where('returns.returnable_type', "App\Inventory\Sale");
    }
}
