<?php

namespace App\Models\Inventory\Traits;

use App\Models\Inventory\Partnumber;

trait PartNumberReport
{
    public function partNumberReport()
    {
        $part_numbers = Partnumber::with('product', 'warehouse', 'sale', 'purchase');

        if (request()->get('warehouse_id')) {
            $part_numbers->where('warehouse_id', request()->get('warehouse_id'));
        }

        if (request()->get('product_id')) {
            $part_numbers->where('product_id', request()->get('product_id'));
        }

        if (request()->get('part_number')) {
            $part_numbers->where('part_number', 'like', request()->get('part_number'));
        }

        $part_numbers = request()->get('itemsPerPage')
            ? $part_numbers->paginate(itemPerPage())
            : $part_numbers->get();

        return response()->json($part_numbers);
    }
}
