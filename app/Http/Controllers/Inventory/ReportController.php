<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Expense\Traits\ExpenseReportTraits;
use App\Models\Income\Traits\IncomeReportTraits;
use App\Models\Inventory\Traits\Charts\Charts;
use App\Models\Inventory\Traits\CustomerReport;
use App\Models\Inventory\Traits\HomeReport;
use App\Models\Inventory\Traits\OverallReport;
use App\Models\Inventory\Traits\productReportTraits;
use App\Models\Inventory\Traits\PurchaseTraits;
use App\Models\Inventory\Traits\SalesReport;
use App\Models\Inventory\Traits\SupplierReport;
use App\Models\Inventory\Traits\WarehouseReport;
use Illuminate\Http\Request;

class ReportController extends Controller

{
    use PurchaseTraits, SalesReport, productReportTraits, ExpenseReportTraits, WarehouseReport, IncomeReportTraits,
        OverallReport, HomeReport, CustomerReport, SupplierReport, Charts;

    public static $purchaseModel = 'App\Models\Inventory\Purchase';
    public static $salesModel = 'App\Models\Inventory\Sale';
    public static $warehouseModel = 'App\Models\Inventory\Warehouse\Warehouse';
    public static $productModel = 'App\Models\Inventory\Product';
    public static $expenseModel = 'App\Models\Expense\Expense';
    public static $incomeModel = 'App\Models\Income\Income';
    public static $customerModel = 'App\Models\Inventory\Customer';
    public static $supplierModel = 'App\Models\Inventory\Supplier';
    private $queryData;
    private $pageNumber;


    /**
     * @param $modelName
     * @return mixed
     */
    protected function staticVariables($modelName)
    {
        $dateBetween = null;
        $model = $modelName;
        $refValue = null;
        $query = request()->query();

        $this->pageNumber = $query['itemsPerPage'];
        $this->queryData = array_key_exists('query', $query)?$query['query']:null;
        return $model;
    }

    /**
     * @param Request $request
     * @param $model
     * @param $relation
     * @return mixed
     */
    protected function reportQueryBuilder(Request $request, $model, $relation)
    {
        $query = $model::with($relation)->whereCompanyId($request->input('company_id'))->select();

        $queryDatas = json_decode(html_entity_decode($this->queryData), true);

        if ($queryDatas != null) {
            foreach ($queryDatas as $key => $value) {
                if ($key === 'rel' && count($value) > 0) {
                    $nested = $this->recursiveMethod($value, [], "");
                    $query->whereHas($nested['tableName'], function ($table) use ($nested) {
                        $table->where($nested['columnName'], 'like', '%' . $nested['columnValue'] . '%');
                    });
                }
                if (count($value) > 1) {
                    $query->whereBetween($key, $value);
                } else {
                    if ($value == null) break;
                    $key == 'rel' && count($value) > 0 ? '' : $query->where($key, 'like', '%' . $value . '%');
                    /*if (is_numeric($value)) {
                        $key == 'rel' && count($value) > 0 ? '' : $query->where($key, '=', $value);
                    } else {
                        $key == 'rel' && count($value) > 0 ? '' : $query->where($key, 'like', '%' . $value . '%');
                    }*/
                }
            }
        }

        if($request->input('sortBy')){
            $this->sortingReport($query, $model);
        }

        $data = $query->latest()->paginate($this->pageNumber);
        return $data;
    }

    public function sortingReport($query, $model)
    {
//        if($model===self::$purchaseModel && request()->input('sortBy')===''){
//
//        }else{
            $query->orderBy(request()->input('sortBy'), request()->input('sortDesc') == 'false' ? 'asc' : 'desc');
//        }

    }

    /**
     * @param $data
     * @param $values
     * @param $tableName
     * @return array
     */
    private function recursiveMethod($data, $values, $tableName)
    {
        $columnName = null;
        $columnValue = null;
        foreach ($data as $key => $val) {
            if (gettype($val) === 'array') {
                if ($tableName != '') {
                    $tableName = $tableName . "." . $key;
                } else {
                    $tableName = $key;
                }
                $values = $this->recursiveMethod($val, $values, $tableName);
            } else {
                $columnName = $key;
                $columnValue = $val;
                $values = [
                    'tableName' => $tableName,
                    'columnName' => $columnName,
                    'columnValue' => $columnValue
                ];
            }
        }

        return $values;
    }
}
