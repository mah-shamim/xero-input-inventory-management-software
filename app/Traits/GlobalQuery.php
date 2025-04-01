<?php

namespace App\Traits;

use App\Models\Inventory\Customer;
use App\Models\Inventory\Otheruser;
use App\Models\Inventory\Supplier;
use App\Models\Payroll\Employee;
use Illuminate\Support\Facades\DB;

trait GlobalQuery
{
    public function show_any_user($id)
    {

        if (request()->input('model') === Customer::classpath()) {
            $data = $this->customerQuery()->where('id', $id)->first();
        }
        if (request()->input('model') === Supplier::classPath()) {
            $data = $this->supplierQuery()->where('id', $id)->first();
        }
        if (request()->input('model') === Employee::classPath()) {
            $data = $this->employeeQuery()->where('id', $id)->first();
        }
        if (request()->input('model') === Otheruser::classPath()) {
            $data = $this->otheruserQuery()->where('id', $id)->first();
        }

        return response()->json($data)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function get_any_user()
    {
        $result = [];
        $customers = $this->customerQuery();

        if (request()->input('name')) {
            $customers->where('name', 'like', '%'.request()->input('name').'%');
        }
        $customers = $customers->get()->toArray();

        $suppliers = $this->supplierQuery();

        if (request()->input('name')) {
            $suppliers->where('name', 'like', '%'.request()->input('name').'%');
        }
        $suppliers = $suppliers->get()->toArray();

        $employees = $this->employeeQuery();

        if (request()->input('name')) {
            $employees->where('name', 'like', '%'.request()->input('name').'%');
        }
        $employees = $employees->get()->toArray();

        $otherusers = $this->otheruserQuery();

        if (request()->input('name')) {
            $otherusers->where('name', 'like', '%'.request()->input('name').'%');
        }
        $otherusers = $otherusers->get()->toArray();

        $result = collect(array_merge($customers, $suppliers, $employees, $otherusers));

        return response()->json($result->sortBy('name')->values()->all())->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function customerQuery(): mixed
    {
//        $customerClassPath = Customer::classPath();
//        dd($customerClassPath);
        $customerClassPath = str_replace('\\','\\\\', Customer::classPath());
        return Customer::where('company_id', compid())
            ->select('customers.id',
                'customers.name',
                'customers.email',
                'customers.phone',
                'customers.code',
                DB::raw("CONCAT('customer') AS `group`"),
                DB::raw("CONCAT('$customerClassPath') AS model"))
            ->groupBy('customers.id')->take(10);
    }

    public function supplierQuery(): mixed
    {
        $supplierClassPath = str_replace("\\", '\\' , Supplier::classPath());
//        dd($supplierClassPath);

        return Supplier::where('company_id', compid())
            ->select('suppliers.id',
                'suppliers.name',
                'suppliers.email',
                'suppliers.phone',
                'suppliers.code',
                'suppliers.company',
                DB::raw("CONCAT('supplier') AS `group`"),
                DB::raw("CONCAT('$supplierClassPath') AS model"))
            ->groupBy('suppliers.id')->take(10);
    }

    /**
     * @return mixed
     */
    public function employeeQuery()
    {
        $employeeClassPath = str_replace("\\", '\\' , Employee::classPath());

        return Employee::where('company_id', compid())
            ->select('employees.id',
                'employees.name',
                'employees.employee_id as code',
                DB::raw("CONCAT('employee') AS `group`"),
                DB::raw("CONCAT('$employeeClassPath') AS model"))
            ->groupBy('employees.id')->take(10);
    }

    /**
     * @return mixed
     */
    public function otheruserQuery()
    {
        $otherUserClassPath = str_replace("\\", '\\' ,Otheruser::classPath());
        return Otheruser::where('company_id', compid())
            ->select('otherusers.id', 'otherusers.name', DB::raw("CONCAT('other') AS `group`"), DB::raw("CONCAT('$otherUserClassPath') AS model"))
            ->groupBy('otherusers.id')->take(10);
    }

    public function get_any_user_by_dynamic_column_and_model()
    {
        //        dd(request()->input('columns'));
        if (method_exists($this, request()->input('model_type').'Query')) {
            $i = 0;
            $method = $this->{request()->input('model_type').'Query'}();
            foreach (request()->input('columns') as $value) {
                if (request()->input('gate_type') === 'and') {
                    $method->where($value, 'like', '%'.request()->input('query_string').'%');
                } else {
                    if ($i === 0) {
                        $method->where($value, 'like', '%'.request()->input('query_string').'%');
                    } else {
                        $method->orWhere($value, 'like', '%'.request()->input('query_string').'%');
                    }
                }
                $i++;
            }

            return $method->where('company_id', compid())->get()->toArray();
            //            return $method->toSql();
        }
    }
}
