<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\SalaryRequest;
use App\Models\Payroll\Employee;
use App\Models\Payroll\Salary;
use App\Services\Payroll\Salary\SalaryCreateService;
use App\Services\Payroll\Salary\SalaryEditService;
use App\Services\Payroll\Salary\SalaryListService;
use App\Services\Payroll\Salary\SalaryUpdateService;
use App\Traits\Ledger;
use Illuminate\Http\Request;

/**
 * Class SalaryController
 */
class SalaryController extends Controller
{
    use Ledger;

    public function total_sum()
    {
        $salary = Salary::selectRaw('coalesce(sum(salaries.amount),0) as total_paid')
            ->selectRaw('coalesce(sum(salaries.current_salary),0) as total_current_salary')
            ->selectRaw('coalesce(sum(salaries.current_salary),0) - coalesce(sum(salaries.amount),0) as total_due')
            ->selectRaw('coalesce(sum(salaries.amount),0) - coalesce(sum(salaries.current_salary),0) as negative_total_due')
            ->where('company_id', request()->input('company_id'))
            ->first();

        return response()->json($salary)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(SalaryListService $salaryListService, Request $request)
    {
//        $this->authorize('viewAny', Salary::class);
        $result['salaries'] = $this->renderArrayOutput($salaryListService, $request, null);

        return response()->json($result['salaries'])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', Salary::class);

        return response()->json([
            'employees' => Employee::employeeList()->get(),
            'banks' => $this->listOfBankWithRunningBalance(),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return string
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(SalaryCreateService $salaryCreateService, SalaryRequest $request)
    {
        //        dd($request->all());
//        $this->authorize('create', Salary::class);

        return $this->renderJsonOutput($salaryCreateService, $request, null);
    }

    public function show($id)
    {
        //
    }

    /**
     * @return string
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id, SalaryEditService $salaryEditService, Request $request)
    {
//        $this->authorize('update', Salary::find($id));

        return $this->renderJsonOutput($salaryEditService, $request, ['id' => $id]);

    }

    /**
     * @return string
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(SalaryUpdateService $salaryUpdateService, SalaryRequest $request, $id)
    {
//        $this->authorize('update', Salary::find($id));

        return $this->renderJsonOutput($salaryUpdateService, $request, null);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
//        $this->authorize('delete', Salary::find($id));
        $salary = Salary::find($id);
        if ($salary->transaction_id) {
            $salary->transaction()->delete();
        }
        $salary->delete();

        return response()->json([
            'type' => 'success', 'message' => 'Salary has been delete successfully',
        ]);
    }
}
