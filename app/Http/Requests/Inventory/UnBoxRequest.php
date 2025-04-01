<?php

namespace App\Http\Requests\Inventory;

use App\Inventory\Unboxevent;
use App\Services\Inventory\BillOfMaterial\BOMEventServices;
use Illuminate\Foundation\Http\FormRequest;

class UnBoxRequest extends FormRequest
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
            'warehouse' => 'required',
            'quantity' => 'required',
            'location'=>'required'
        ];
    }

    public function save()
    {
        $bOMEventServices = new BOMEventServices();
        $product = $bOMEventServices->getProduct();

        $item['product'] = $product;
        $item['unit_id'] = $product->base_unit_id;
        $item['quantity'] = $this->input('quantity');
        $item['unit'] = $product->base_unit_id;
        $item['location'] = $this->input('location');
        $item['warehouse_id'] = $this->input('warehouse');

        if ($bOMEventServices->stockOut($item)) {
            $this->subtractWeight($product, $item);
            foreach ($product->bill_of_materials as $bom) {
                $this->merge(['unit' => $bom->unit_id]);
                $this->merge(['quantity' => $bom->quantity * $this->input('quantity')]);
                $this->merge(['warehouse' => $bom->warehouse_id]);
                $weight = $bom->product->units->first(function ($u) use ($bom) {
                    return $u->id == $bom->unit_id;
                })->pivot->weight * $item['quantity'] * $bom->quantity;

                $bom->product->updateWeight('sum', $bom->product, $bom->warehouse_id, $weight);
                $bOMEventServices->stockIn($bom->product);
            }
        }

        Unboxevent::create(array_merge($item, [
            'company_id' => $this->input('company_id'),
            'product_id' => $product->id,
        ]));

        return response()->json([
            'type' => 'success',
            'message' => 'unbox successfully done',
        ]);
    }

    private function subtractWeight($product, $item)
    {
        $weight = $product->units->first(function ($value) use ($item) {
            return $value->id == $item['unit'];
        })->pivot->weight * $item['quantity'];

        $product->updateWeight('subtract', $product, $item['warehouse_id'], $weight);
    }
}
