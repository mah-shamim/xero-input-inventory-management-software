<?php

namespace App\Http\Requests\Inventory;

use App\Http\Controllers\Bank\TransactionController;
use App\Services\Inventory\BillOfMaterial\BOMEventServices;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BillOfMaterialBuildRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'unit'         => 'required|numeric|min:0|not_in:0',
            'quantity'     => 'required|numeric|min:0|not_in:0',
            'warehouse'    => 'required|numeric|min:0|not_in:0',
            'location'     => 'required',
            'paid'         => [ Rule::requiredIf($this->input('expense_total') > 0) ],
            'bank_id'      => [ Rule::requiredIf($this->input('payment_type') > 1) ],
            'payment_type' => [ Rule::requiredIf($this->input('expense_total') > 0) ],
        ];
    }

    public function save()
    {
        $bOMEventServices = new BOMEventServices();
        $product          = $bOMEventServices->getProduct();
        $items            = $product->bill_of_materials;
        foreach ($items as $item) {
//            dd($item, $item->product_id);
            $bom    = $item->product;
            $weight = $bom->units->first(function ($value) use ($item) {
                    return $value->id == $item->unit_id;
                })->pivot->weight * $this->input('quantity') * $item->quantity;

            $bom->updateWeight('subtract', $bom, $item->warehouse_id, $weight);

            $productWarehouse = DB::table('product_warehouse')
                ->whereProductId($item->product_id)
                ->whereWarehouseId($item->warehouse_id);

            $productWarehouseData = $productWarehouse->first();

            $productWarehouse_location         = $productWarehouseData->location;
            $productWarehouse_location_json    = json_decode($productWarehouse_location, true);
            $product_warehouse_location_weight = $productWarehouse_location_json[$item['location']]['weight'];

            $productWarehouse->update([
                'location->' . $item['location'] . '->weight' => $product_warehouse_location_weight - $weight,
            ]);

            $item->quantity = $item->quantity * $this->input('quantity');
            $bOMEventServices->stockOut($item);
        }
        $product->updateWeight('sum', $product, $this->input('warehouse'), $this->input('total_weight'));
        $bOMEventServices->stockIn($product);

        if ($this->input('expense_total') > 0) {
            (new TransactionController())->create_bank_transaction($this, 'credit');
        }

        $product->buildevents()->create(array_merge($this->all(), [
            'warehouse_id' => $this->input('warehouse'),
            'unit_id'      => $this->input('unit'),
        ]));

        return response()->json([
            'type'    => 'success',
            'message' => 'Build has been successfully done',
        ]);
    }
}
