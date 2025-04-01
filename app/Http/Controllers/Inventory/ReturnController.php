<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Sale;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index($model, $model_id)
    {
        if ($model == 'sale') {
            return $this->forSale($model_id);
        }
        if ($model == 'Purchase') {
            return $this->forPurchase($model_id);
        }
    }

    public function store(Request $request, $model, $model_id)
    {
        return [$request->all(), $model, $model_id];
    }

    private function forSale($model_id)
    {
        $sale = Sale::with(['products' => function ($p) {
            $p->with('units');
        }, 'payments'])->whereId($model_id)->first();

        quantityStrConversion($sale);

        return $sale;

    }

    private function forPurchase($model_id)
    {
    }
}
