<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 1/9/2018
 * Time: 1:14 PM
 */
namespace App\Services\Inventory\Purchase;
use App\Http\Controllers\Bank\TransactionController;
use App\Models\Inventory\Partnumber;
use App\Models\Inventory\Product;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Unit;
use App\Models\Inventory\UnitConversion;
use App\Services\ActionIntf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseUpdateService implements ActionIntf
{
    use PurchaseServicesCommonMethods;
    public function executePreCondition(Request $request, $params)
    {

        $result = ['type' => 'error', 'message' => ''];
        $id = $params['id'];
        $purchase = Purchase::with([
            'products', 'payments' => function ($p) {
                $p->with('transaction')->first();
            },
        ])->find($id);

        if (! $purchase) {
            $result['message'] = 'Selected Purchase does not exists anymore.Please refresh and try again';

            return $result;
        }
        $result['purchases'] = $purchase;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $array['type'] = 'error';
        $purchase = $array['purchases'];
        $request->merge(['total_weight' => $request->input('total_weight') + $request->input('extra_weight')]);
        $array = $this->dataInitial_and_quantity_update($request, $purchase, $array);
        if ($array['message'] != '') {
            return $array;
        }
        //        $purchaseData = $array['purchaseData'];
        $purchase = $purchase->fill(
            array_merge($request->except('purchase_date'), [
                'purchase_date' => Carbon::parse($request->input('purchase_date'))->format('Y-m-d H:m:s'),
            ])
        );
        $purchase->supplier()->associate($request->input('supplier_id'));
        //        $Purchase->products()->sync($purchaseData);

        if ($request->input('paid')) {
            $purchase->payments->first()->update($request->all());
            (new TransactionController())->update_bank_transaction($request, $purchase, 'credit');
        }

        $purchase->save();
        $array['type'] = 'success';

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        return ['type' => 'success', 'message' => 'Purchase has been updated successfully'];
    }

    public function buildFailure($array)
    {
        return $array;
    }

    /**
     * @return mixed
     */
    private function dataInitial_and_quantity_update(Request $request, $purchase, $array)
    {

        $purchaseData = [];
        $old_products = $purchase->products;
        $this->removeProduct($request, $old_products, $purchase);
        $purchase->products()->detach();
        foreach ($request->input('items') as $item) {
            $this->changeWarehouse($old_products, $item, $purchase);
            $product_exist = $this->checkProductExist($item['product_id'], $old_products);
            list($product, $purchaseQuantity, $subtotal, $location_detail)=$this->processProductData($item, $array);
            $purchaseData[$product->id] = $this->getArr($item, $purchaseQuantity, $subtotal, $location_detail);
            $old_product_for_warehouse = $old_products->first(function ($p) use ($item) {
                return $p->pivot->warehouse_id === $item['warehouse'];
            });
            $warehouse_id = $old_product_for_warehouse ? $old_product_for_warehouse->pivot->warehouse_id : null;
            if ($product_exist && ($warehouse_id === (int) $item['warehouse'])) {
                $item['pp_id'] = array_key_exists('pp_id', $item) ? (int) $item['pp_id'] : null;
                $product->editUpdateWeight($product, $purchase, $item['warehouse'], $item['weight_total'], 'sum', $item['pp_id']);
                $product->updateQuantity($product, $item['quantity'], $item['warehouse'], $purchase, null, $item['pp_id']);
            } else {
                $product->updateWeight('sum', $product, $item['warehouse'], $item['weight_total']);
                $product->updateQuantity($product, $array['quantity'], $item['warehouse']);
            }

            $product->update();
            $purchase->products()->attach($purchaseData);
            $this->updatePartNumber($product, $item, $purchase);

//            $product->updateLocationEdit($product, $item, $purchase);
            $purchaseData = [];
        }
        $array['purchaseData'] = $purchaseData;

        return $array;
    }


    public function checkProductExist($product_id, $old_products, $unit_id = null)
    {
        //        dd($product_id, $old_products->unit->id, $unit_id);
        foreach ($old_products as $old_product) {
            if (($old_product->id === $product_id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    private function removeProduct(Request $request, $old_products, $purchase)
    {
        if ($request->input('removed_ids') && count($request->input('removed_ids'))) {
            foreach ($request->input('removed_ids') as $remove_id) {
                $remove_product = $old_products->where('id', $remove_id)->first();
                if ($remove_product) {
                    $warehouse_id = $remove_product->pivot->warehouse_id;
                    $quantity = $remove_product->pivot->quantity;
                    $unit_id = $remove_product->pivot->unit_id;
                    $weight_total = $remove_product->pivot->weight_total;

                    $product = Product::with('warehouses')
                        ->where('id', $remove_id)
                        ->where('company_id', compid())
                        ->first();

                    $warehouse = $product->warehouses()->where('warehouse_id', $warehouse_id)->first();
                    $existing_weight = $warehouse->pivot->weight;
                    $new_weight = $existing_weight - $weight_total;

                    $product->warehouses()
                        ->syncWithoutDetaching([$warehouse_id => ['weight' => $new_weight]]);

                    $product->updateQuantity($product, -$quantity, $warehouse_id, $purchase);
                }
            }
        }
    }

    /**
     * @return void
     */
    private function changeWarehouse($old_products, $item, $purchase)
    {
        $old_product_for_warehouse = $old_products->where('id', $item['product_id'])->first();
        if (! $old_product_for_warehouse) {
            return null;
        }
        $warehouse_id = $old_product_for_warehouse->pivot->warehouse_id;
        if ($warehouse_id === $item['warehouse']) {
            return null;
        }
        $quantity = $old_product_for_warehouse->pivot->quantity;
        $unit_id = $old_product_for_warehouse->pivot->unit_id;
        $weight_total = $old_product_for_warehouse->pivot->weight_total;

        $product = Product::with('warehouses')
            ->where('id', $item['product_id'])
            ->where('company_id', compid())
            ->first();

        $warehouse = $product->warehouses()->where('warehouse_id', $warehouse_id)->first();
        $existing_weight = $warehouse->pivot->weight;
        $new_weight = $existing_weight - $weight_total;

        $product->warehouses()
            ->syncWithoutDetaching([$warehouse_id => ['weight' => $new_weight]]);

        $product->updateQuantity($product, -$quantity, $warehouse_id, $purchase);
    }

    private function updatePartNumber($product, $item, $purchase)
    {
        $existing_product_ids = $purchase->products->pluck('id')->toArray();
        $input_product_ids = array_column(request()->input('items'), 'product_id');

        $find_deleted_ids = array_diff($existing_product_ids, $input_product_ids);

        if (count($find_deleted_ids)) {
            foreach ($find_deleted_ids as $del_id) {
                Partnumber::where('product_id', $del_id)
                    ->where('purchase_id', $purchase->id)
                    ->delete();
            }
        }

        if ($product->manufacture_part_number) {
            foreach ($item['part_number'] as $part_number) {
                if ($part_number) {
                    if (is_array($part_number) && array_key_exists('id', $part_number)) {
                        $part_query = Partnumber::where('id', $part_number['id'])->first();
                        if ($part_number['part_number']) {
                            $part_query->update([
                                'product_id' => $product->id,
                                'warehouse_id' => $item['warehouse'],
                                'purchase_id' => $purchase->id,
                                'part_number' => $part_number['part_number'],
                            ]);
                        } else {
                            $part_query->delete();
                        }
                    } else {
                        if ($part_number) {
                            Partnumber::create([
                                'product_id' => $product->id,
                                'warehouse_id' => $item['warehouse'],
                                'purchase_id' => $purchase->id,
                                'part_number' => $part_number,
                            ]);
                        }
                    }
                }
            }
        }
    }
}