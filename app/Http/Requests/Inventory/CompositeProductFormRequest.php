<?php

namespace App\Http\Requests\Inventory;

use App\Inventory\BillOfMaterial;
use App\Inventory\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompositeProductFormRequest extends FormRequest
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
            'name' => 'required',
            'code' => [
                'required',
                Rule::unique('products')
                    ->where('company_id', compid())
                    ->ignore($this->route('product_composite'), 'id'),
            ],
            'price' => 'required|numeric|min:0|not_in:0',
            'brand' => 'required',
            'categories' => 'required',
            'buying_price' => 'required|numeric|min:0|not_in:0',
            'items.*.unit' => 'required',
            'items.*.quantity' => 'required',
            'items.*.warehouse' => 'required',
            'items.*.location' => 'required',
            'items.*.unit_price' => 'required',
            'items.*.product_id' => 'required',
            'other_expenses' => 'array',
            'other_expenses.*.label' => 'required',
            'other_expenses.*.value' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'items.*.unit.required' => 'unit is required',
            'items.*.quantity.required' => 'quantity is required',
            'items.*.warehouse.required' => 'warehouse is required',
            'items.*.location.required' => 'location is required',
            'items.*.product_id.required' => 'product is required',
            'items.*.unit_price.required' => 'unit price is required',
            'other_expenses.*.label.required' => 'Label is required',
            'other_expenses.*.label.value' => 'Value is required',
        ];
    }

    public function save($id)
    {
        $this->deleteItem($id);

        $product = Product::has('bill_of_materials')
            ->with('bill_of_materials')
            ->where('company_id', $this->input('company_id'))
            ->where('id', $id)
            ->first();

        foreach ($this->items as $item) {
            if (array_key_exists('id', $item) && $item['id']) {
                $this->updateItem($product, $item);
            } else {
                $this->createItem($product, $item);
            }
        }
        $this->merge(['bom' => $this->input('other_expenses')]);
        $this->merge(['weight' => $this->input('total_weight') + $this->input('extra_weight')]);
        $this->merge(['brand_id' => $this->input('brand')]);
        $this->merge(['bom' => $this->input('other_expenses')]);
        $product->update($this->all());
        $product->categories()->sync($this->input('categories'));
        $product->units()->syncWithoutDetaching([
            $this->input('base_unit_id') => [
                'weight' => $this->input('weight'),
            ],
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Bill of material has been updated successfully',
        ]);
    }

    public function createItem($product, $item)
    {
        $productItem = Product::with('warehouses')->find($item['product_id']);
        $product->bill_of_materials()->create([
            'unit_id' => $item['unit'],
            'quantity' => $item['quantity'],
            'product_id' => $productItem->id,
            'warehouse_id' => $item['warehouse'],
            'material_quantity' => $item['quantity'],
            'location'=>$item['location']
        ]);
    }

    public function updateItem($product, $item)
    {
        $bill_of_material = $product->bill_of_materials()->where('id', $item['id'])->first();
        $bill_of_material->update([
            'unit_id' => $item['unit'],
            'quantity' => $item['quantity'],
            'warehouse_id' => $item['warehouse'],
            'material_quantity' => $item['quantity'],
            'location' => $item['location'],
        ]);
    }

    public function deleteItem($id): void
    {
        if (! empty($this->input('deletedIds'))) {
            foreach ($this->input('deletedIds') as $deletedId) {
                BillOfMaterial::where('id', $deletedId)->where('main_id', $id)->delete();
            }
        }
    }
}
