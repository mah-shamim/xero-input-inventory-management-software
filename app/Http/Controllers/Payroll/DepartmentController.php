<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\DepartmentRequest;
use App\Models\Payroll\Department;

class DepartmentController extends Controller
{
    public function index(): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        $departments = Department::select('departments.*')
            ->where('departments.company_id', compid())
            ->withCount('employees')
            ->leftjoin('employees as e', function ($employees) {
                $employees->on('e.department_id', '=', 'departments.id')
                    ->join('salaries', 'salaries.employee_id', '=', 'e.id');
            })
            ->selectRaw('SUM(salaries.total) as sum_salaries')
            ->groupBy('departments.id');

        if (request()->get('name')) {
            $departments->where('name', 'like', '%'.request()->get('name').'%');
        }

        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $departments = $departments->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }

        $departments = ! request()->input('no-paginate')
            ? $departments->paginate(getResultPerPage())
            : $departments->get();

        return response()->json([
            'departments' => $departments,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DepartmentRequest $request): \Illuminate\Http\JsonResponse
    {
        return $request->save();
    }


    public function show($id)
    {
        //
    }

    public function edit(int $id): \Illuminate\Http\JsonResponse
    {
        $department = Department::where('id', $id)->where('company_id', compid())->first();

        return response()->json(['department' => $department]);
    }

    public function update(DepartmentRequest $request): \Illuminate\Http\JsonResponse
    {
        return $request->update();
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        $department = Department::where('id', $id)->where('company_id', compid())->first();

        $employee_count = $department->employees()->count();

        if (request()->input('confirmed')) {
            $department->delete();

            return response()->json([
                'type' => 'success',
                'message' => 'department has been deleted successfully',
            ]);
        }

        if ($employee_count > 0) {
            return response()->json([
                'type' => 'warning',
                'employee_count' => $employee_count,
            ]);
        } else {
            $department->delete();

            return response()->json([
                'type' => 'success',
                'message' => 'Department has been deleted successfully',
            ]);
        }

    }
}
