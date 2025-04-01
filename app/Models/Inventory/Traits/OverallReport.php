<?php


namespace App\Models\Inventory\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait OverallReport
{
    use ProfitTraits;
    public function overallReport(Request $request)
    {
        $date_between = [];
        $date_between_purchase_str = '';
        $date_between_sale_str = '';
        $date_between_expense_str = '';
        $date_between_income_str = '';
        $date_between_return_str='';
        $date_between_salary_str='';

        $query = request()->query();
        $companyId = $request->input('company_id');
        $queryParams = ['companyId' => $companyId];

        if (count($query) > 0) {
            $query = json_decode($query['query']);
//            dd(isset();
            if (isset($query->date_between)) {
                $date_between = $query->date_between;
                $date_between_purchase_str = ' AND purchase_date BETWEEN :startDate AND :endDate';
                $date_between_sale_str = ' AND sales_date BETWEEN :startDate AND :endDate';
                $date_between_return_str = ' AND created_at BETWEEN :startDate AND :endDate';
                $date_between_expense_str = ' AND expense_date BETWEEN :startDate AND :endDate';
                $date_between_income_str = ' AND income_date BETWEEN :startDate AND :endDate';
                $date_between_salary_str = ' AND salary_date BETWEEN :startDate AND :endDate';
                if($date_between[1]<=> $date_between[0] ){
                    $end_date =  $date_between[1];
                    $start_date =  $date_between[0];
                }else{
                    $end_date =  $date_between[0];
                    $start_date =  $date_between[1];
                }
                $queryParams = ['companyId' => $companyId, 'startDate' => $start_date, 'endDate' => $end_date];
            }

        }

        if(count($date_between)!==2){
            return response()->json(false, 422);
        }

//        dd($queryParams);

        $purchaseTotal = DB::select("SELECT (SELECT CASE WHEN SUM(payments.paid) IS NOT NULL THEN SUM(payments.paid) ELSE 0 END FROM purchases left join payments on purchases.id = payments.paymentable_id WHERE paymentable_type = 'App\\\Inventory\\\Purchase' and payments.payment_type=1 AND company_id = :companyId" . $date_between_purchase_str . ") AS cash_total,
(SELECT CASE WHEN SUM(payments.paid) IS NOT NULL THEN SUM(payments.paid) ELSE 0 END FROM purchases left join payments on purchases.id = payments.paymentable_id WHERE paymentable_type = 'App\\\Inventory\\\Purchase' and payments.payment_type=2 AND company_id = :companyId" . $date_between_purchase_str . ") AS credit_total,
(SELECT CASE WHEN SUM(payments.paid) IS NOT NULL THEN SUM(payments.paid) ELSE 0 END FROM purchases left join payments on purchases.id = payments.paymentable_id WHERE paymentable_type = 'App\\\Inventory\\\Purchase' and payments.payment_type=3 AND company_id = :companyId" . $date_between_purchase_str . ") AS cheque_total,
CASE WHEN SUM(total) IS NOT NULL THEN SUM(total) ELSE 0 END AS total_amount,
COUNT(p.id) AS total_count
FROM purchases p
WHERE company_id = :companyId".$date_between_purchase_str, $queryParams);




        $saleTotal = DB::select("SELECT (SELECT CASE WHEN SUM(payments.paid) IS NOT NULL THEN SUM(payments.paid) ELSE 0 END FROM sales left join payments on sales.id = payments.paymentable_id WHERE paymentable_type = 'App\\\Inventory\\\Sale' and payments.payment_type = 1 AND company_id = :companyId" . $date_between_sale_str . ") AS cash_total,
(SELECT CASE WHEN SUM(payments.paid) IS NOT NULL THEN SUM(payments.paid) ELSE 0 END FROM sales left join payments on sales.id = payments.paymentable_id WHERE paymentable_type = 'App\\\Inventory\\\Sale' and payments.payment_type = 2 AND company_id = :companyId" . $date_between_sale_str . ") AS credit_total,
(SELECT CASE WHEN SUM(payments.paid) IS NOT NULL THEN SUM(payments.paid) ELSE 0 END FROM sales left join payments on sales.id = payments.paymentable_id WHERE paymentable_type = 'App\\\Inventory\\\Sale' and payments.payment_type = 3 AND company_id = :companyId" . $date_between_sale_str . ") AS cheque_total,
(SELECT CASE WHEN SUM(amount) IS NOT NULL THEN SUM(amount) ELSE 0 END FROM returns WHERE company_id = :companyId" . $date_between_return_str . ") AS return_total,
CASE WHEN SUM(total) IS NOT NULL THEN SUM(total) ELSE 0 END AS total_amount,
COUNT(p.id) AS total_count
FROM sales p
WHERE company_id = :companyId".$date_between_sale_str, $queryParams);




        $expenseTotal = DB::select('SELECT (SELECT CASE WHEN SUM(amount) IS NOT NULL THEN SUM(amount) ELSE 0 END FROM expenses WHERE  payment_method = 1 AND company_id = :companyId' . $date_between_expense_str . ') AS cash_total,
(SELECT CASE WHEN SUM(amount) IS NOT NULL THEN SUM(amount) ELSE 0 END FROM expenses WHERE  payment_method = 2 AND company_id = :companyId' . $date_between_expense_str . ') AS credit_total,
(SELECT CASE WHEN SUM(amount) IS NOT NULL THEN SUM(amount) ELSE 0 END FROM expenses WHERE  payment_method = 3 AND company_id = :companyId' . $date_between_expense_str . ') AS cheque_total,
CASE WHEN SUM(amount) IS NOT NULL THEN SUM(amount) ELSE 0 END AS total_amount,
COUNT(p.id) AS total_count
FROM expenses p
WHERE company_id = :companyId'.$date_between_expense_str, $queryParams);

        $incomeTotal = DB::select('SELECT (SELECT CASE WHEN SUM(amount) IS NOT NULL THEN SUM(amount) ELSE 0 END FROM incomes WHERE  payment_method = 1 AND company_id = :companyId' . $date_between_income_str . ') AS cash_total,
(SELECT CASE WHEN SUM(amount) IS NOT NULL THEN SUM(amount) ELSE 0 END FROM incomes WHERE  payment_method = 2 AND company_id = :companyId' . $date_between_income_str . ') AS credit_total,
(SELECT CASE WHEN SUM(amount) IS NOT NULL THEN SUM(amount) ELSE 0 END FROM incomes WHERE  payment_method = 3 AND company_id = :companyId' . $date_between_income_str . ') AS cheque_total,
CASE WHEN SUM(amount) IS NOT NULL THEN SUM(amount) ELSE 0 END AS total_amount,
COUNT(p.id) AS total_count
FROM incomes p
WHERE company_id = :companyId'.$date_between_income_str, $queryParams);


        $salaryTotal = DB::select('SELECT (SELECT CASE WHEN SUM(total) IS NOT NULL THEN SUM(total) ELSE 0 END FROM salaries WHERE  payment_method = 1 AND company_id = :companyId' . $date_between_salary_str . ') AS cash_total,
(SELECT CASE WHEN SUM(total) IS NOT NULL THEN SUM(total) ELSE 0 END FROM salaries WHERE  payment_method = 2 AND company_id = :companyId' . $date_between_salary_str . ') AS credit_total,
(SELECT CASE WHEN SUM(total) IS NOT NULL THEN SUM(total) ELSE 0 END FROM salaries WHERE  payment_method = 3 AND company_id = :companyId' . $date_between_salary_str . ') AS cheque_total,
CASE WHEN SUM(total) IS NOT NULL THEN SUM(total) ELSE 0 END AS total_amount,
COUNT(p.id) AS total_count
FROM salaries p
WHERE company_id = :companyId'.$date_between_salary_str, $queryParams);


        $actualProfit=$this->getProfit($date_between_sale_str, $queryParams);

        return response()->json([
            'purchaseTotal' => collect($purchaseTotal)->first(),
            'saleTotal' => collect($saleTotal)->first(),
            'expenseTotal' => collect($expenseTotal)->first(),
            'incomeTotal' => collect($incomeTotal)->first(),
            'salaryTotal' => collect($salaryTotal)->first(),
            'actualProfit'=>$actualProfit
        ]);
    }

}
