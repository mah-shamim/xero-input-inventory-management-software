<?php


namespace App\Models\Inventory\Traits\Charts;


use Illuminate\Support\Facades\DB;

trait ChartSale
{
    public function single_sale_chart_by_date()
    {
        $saleByProductChart = DB::select('SELECT date(sales_date) AS sales_date, SUM(TOTAL) AS total FROM sales
WHERE sales_date BETWEEN CURDATE() - INTERVAL 3600 DAY AND CURDATE() + INTERVAL 1 DAY AND company_id = :companyId
GROUP BY date(sales_date) ORDER BY MAX(total) DESC LIMIT 10;', ['companyId' => request()->company_id]);

        list($sale_date, $saleTotal) = $this->mappingData(collect($saleByProductChart), 'sales_date');
        $saleByProductChart = [
            'sale_date' => $sale_date,
            'sale_totals' => $saleTotal
        ];

        return response()->json($saleByProductChart);
    }

    public function best_top_product_sales()
    {
        $best_products = DB::select('SELECT SUM(ps.subtotal) AS total, p.name AS  product_name FROM product_sale ps 
LEFT JOIN products p ON p.id = ps.product_id WHERE p.company_id = :companyId
GROUP BY ps.product_id LIMIT 10;', ['companyId' => request()->company_id]);
        list($product_name, $saleTotal) = $this->mappingData(collect($best_products), 'product_name');
        $saleByProductChart = [
            'product_name' => $product_name,
            'sale_totals' => $saleTotal
        ];
        return response()->json($saleByProductChart);
    }
}
