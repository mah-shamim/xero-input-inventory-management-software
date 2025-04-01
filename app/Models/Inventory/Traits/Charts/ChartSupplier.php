<?php


namespace App\Models\Inventory\Traits\Charts;


use App\Models\Inventory\Supplier;

trait ChartSupplier
{

    public function top_supplier_by_total_purchase()
    {
        $suppliers = $this->dbSupplierQueryDevelop(Supplier::class);
        $suppliers = $suppliers->orderBy('purchases_total', 'desc')->take(10)->get();
        list($name, $total) = $this->mappingData(collect($suppliers), 'name', 'purchases_total');
        $supplierChart = [
            'name' => $name,
            'total' => $total
        ];

        return response()->json($supplierChart);
    }

    public function top_supplier_by_purchase_count()
    {
        $suppliers = $this->dbSupplierQueryDevelop(Supplier::class);
        $suppliers = $suppliers->orderBy('purchases_count', 'desc')->take(10)->get();
        list($name, $total) = $this->mappingData(collect($suppliers), 'name', 'purchases_count');
        $supplierChart = [
            'name' => $name,
            'total' => $total
        ];

        return response()->json($supplierChart);
    }

}
