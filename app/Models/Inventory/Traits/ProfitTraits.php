<?php

namespace App\Models\Inventory\Traits;

use App\Models\Inventory\UnitConversion;
use Illuminate\Support\Facades\DB;

trait ProfitTraits
{
    public function getProfit($date_between_sale_str, $queryParams)
    {

        $sales = DB::select('SELECT sales.id, sales.overall_discount,sales.shipping_cost,ps.unit_id, ps.quantity,sales.sales_date,
                   ps.price,ps.subtotal,ps.product_id as productID,
                   (select base_unit_id from products where id = productID) as base_unit_id,
                   (select SUM(CASE WHEN pp.unit_id=base_unit_id THEN pp.quantity ELSE pp.quantity *
                   (select conversion_factor from unit_conversions as uc where from_unit_id=pp.unit_id and to_unit_id=base_unit_id) END)  total_quantity
                   from product_purchase as pp
                   left join purchases p2 on pp.purchase_id = p2.id
                   where pp.product_id = productID and p2.purchase_date <= sales_date group by pp.product_id) as product_purchase_quantity,
                   (select sum(pp.subtotal) from product_purchase as pp
                   left join purchases p3 on pp.purchase_id = p3.id
                   where pp.product_id = productID and p3.purchase_date <= sales_date
                   group by pp.product_id) as product_purchase_amount,
                   (select sum(CASE WHEN p.overall_discount IS NOT NULL THEN (pp.subtotal*p.overall_discount)/100 ELSE 0 END)
                   from product_purchase as pp
                   left join purchases p on pp.purchase_id = p.id
                   where pp.product_id = productID and p.purchase_date <= sales_date group by pp.product_id) as purchase_overall_discount
                   from sales
                   left join product_sale ps on sales.id = ps.sale_id
                   where company_id=:companyId'.$date_between_sale_str, $queryParams);

        $sales = collect($sales)->all();
        $salesProfit = 0;

        foreach ($sales as $sale) {

            $salesQuantity = 0;
            $salesAmount = 0;
            if ($sale->unit_id == $sale->base_unit_id) {

                $salesQuantity = $sale->quantity;
                $salesAmount = $sale->subtotal;

            } else {

                $unitConversonFactor = UnitConversion::whereFromUnitId($sale->unit_id)
                    ->whereToUnitId($sale->base_unit_id)->first();
                if ($unitConversonFactor) {
                    $salesQuantity = ($sale->quantity *
                        $unitConversonFactor->conversion_factor);
                    $salesAmount = $sale->subtotal;
                }
            }

            if ($sale->product_purchase_amount && $sale->product_purchase_quantity > 0) {

                $averagePurchasePrice = ($sale->product_purchase_amount -
                        $sale->purchase_overall_discount) / $sale->product_purchase_quantity;
            } else {
                $averagePurchasePrice = 0;
            }

            $overDiscount = $sale->overall_discount ? $salesAmount *
                (($sale->overall_discount) / 100) : 0;

            $salesProfit += ($salesAmount - $overDiscount)
                - ($salesQuantity * $averagePurchasePrice);
        }

        return $salesProfit;

    }
}
