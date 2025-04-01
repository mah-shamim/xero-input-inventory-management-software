<?php

namespace App\Services\Inventory\ProductUnit;

use App\Models\Inventory\Purchase;
use App\Models\Inventory\Sale;
use App\Services\ActionIntf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductUnitDeleteServices implements ActionIntf
{

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'success', 'message' => '', 'id'=>$params];
        $purchase = Purchase::whereHas('units', function ($query) use ($params) {
            return $query->where('id', $params);
        })->first();
        $sale = Sale::whereHas('units', function ($query) use ($params) {
            return $query->where('id', $params);
        })->first();

        if ($purchase or $sale) {
            $result['type'] = 'error';
            $result['message'] = 'This unit conversion is occupied with other sale or purchase';
            throw ValidationException::withMessages($result);
        }

        return $result;


    }

    public function execute($array, Request $request)
    {
//        @todo later need to solve this
        DB::table('product_unit as pu')
            ->leftJoin('products as p', 'pu.product_id','=','p.id')
            ->leftJoin('units as u','pu.unit_id','=','u.id')
            ->where('p.company_id', compid())
            ->where('pu.pu_id', $array['id'])->delete();

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        $array['type'] = 'success';
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Product unit conversion has been deleted successfully';
        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}