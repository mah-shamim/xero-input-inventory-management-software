<?php


namespace App\Models\Inventory\Traits\Charts;


use App\Models\Inventory\Customer;

trait ChartCustomer
{

    public function top_customer_by_total_purchase()
    {
        $customers = $this->dbCustomerQueryDevelop(Customer::class);
        $customers = $customers->orderBy('sales_total', 'desc')->take(10)->get();
        list($name, $total) = $this->mappingData(collect($customers), 'name', 'sales_total');
        $customerChart = [
            'name' => $name,
            'total' => $total
        ];

        return response()->json($customerChart);
    }

    public function top_customer_by_sale_count()
    {
        $customers = $this->dbCustomerQueryDevelop(Customer::class);
        $customers = $customers->orderBy('sales_count', 'desc')->take(10)->get();
        list($name, $total) = $this->mappingData(collect($customers), 'name', 'sales_count');
        $customerChart = [
            'name' => $name,
            'total' => $total
        ];

        return response()->json($customerChart);
    }

}
