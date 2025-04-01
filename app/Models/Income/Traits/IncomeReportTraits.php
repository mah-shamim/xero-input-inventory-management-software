<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/18/2019
 * Time: 11:48 AM
 */

namespace App\Models\Income\Traits;


use Illuminate\Http\Request;

trait IncomeReportTraits
{
    public function incomeReport(Request $request)
    {
        $model = $this->staticVariables(self::$incomeModel);
        return response()->json($this->reportQueryBuilder(
            $request,
            $model,
            ['warehouse','category']
        ));
    }
}