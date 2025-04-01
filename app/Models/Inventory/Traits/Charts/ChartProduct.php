<?php


namespace App\Models\Inventory\Traits\Charts;


trait ChartProduct
{
    public function top_product_by_stock_profit()
    {
        $products = $this->dbProductQueryDevelop(self::$productModel);
        $products = $products->orderBy('total_stock_profit', 'desc')->take(10)->get();

        list($name, $total) = $this->mappingData(collect($products), 'name', 'total_stock_profit');
        $productChart = [
            'name' => $name,
            'total' => $total
        ];

        return response()->json($productChart);
    }

    public function top_product_by_sale_count_profit()
    {
        $products = $this->dbProductQueryDevelop(self::$productModel);
        $products = $products->orderBy('sale_count_profit', 'desc')->take(10)->get();

        list($name, $total) = $this->mappingData(collect($products), 'name', 'sale_count_profit');
        $productChart = [
            'name' => $name,
            'total' => $total
        ];

        return response()->json($productChart);
    }

}
