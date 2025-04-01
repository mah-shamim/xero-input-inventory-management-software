<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\PaymentRequest;
use App\Models\Inventory\Payment;
use App\Models\Inventory\Purchase;
use App\Services\Inventory\Payment\PaymentCreateService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function income_payment_received()
    {

        $payments = Payment::join('incomes', 'incomes.id', '=', 'payments.paymentable_id')
            ->where('incomes.company_id', compid())
            ->where('payments.paymentable_type', "App\Income\Income")
            ->leftJoin('suppliers', function ($supplier) {
                $supplier->on('suppliers.id', '=', 'incomes.userable_id')
                    ->where('incomes.userable_model', "App\Inventory\Supplier");
            })
            ->leftJoin('customers', function ($customer) {
                $customer->on('customers.id', '=', 'incomes.userable_id')
                    ->where('incomes.userable_model', "App\Inventory\Customer");
            })
            ->leftJoin('employees', function ($employee) {
                $employee->on('employees.id', '=', 'incomes.userable_id')
                    ->where('incomes.userable_model', "App\Payroll\Employee");
            })
            ->leftJoin('otherusers', function ($otheruser) {
                $otheruser->on('otherusers.id', '=', 'incomes.userable_id')
                    ->where('incomes.userable_model', "App\Inventory\Otheruser");
            })
            ->select(
                'payments.*',
                'incomes.id as income_id',
                'incomes.ref as income_ref',
                'suppliers.name as supplier_name',
                'customers.name as customer_name',
                'employees.name as employee_name',
                'otherusers.name as otheruser_name'
            );

        if (request()->get('ref')) {
            $payments->where('incomes.ref', 'like', '%'.request()->get('ref').'%');
        }

        if (request()->get('date')) {
            if (count(request()->input('date')) === 1) {
                $payments->whereDate('payments.date', request()->input('date')[0]);
            }
            if (count(request()->input('date')) == 2) {

                if (Carbon::parse(request()->input('date')[0]) < Carbon::parse(request()->input('date')[1])) {
                    $payments->whereBetween('payments.date', request()->get('date'));
                } else {
                    $payments->whereBetween('payments.date', array_reverse(request()->get('date')));
                }

            }
        }
        if (request()->get('id')) {
            $payments->where('incomes.id', request()->get('id'));
        }

        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $payments->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }

        if (request()->get('model_id')) {
            $payments->where('incomes.id', request()->get('model_id'));
        }

        $this->orderByQuery($payments);

        $payments = request()->get('itemsPerPage')
            ? $payments->paginate(itemPerPage())
            : $payments->get();

        return response()->json($payments)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function expense_bill_paid()
    {
        $payments = Payment::join('expenses', 'expenses.id', '=', 'payments.paymentable_id')
            ->where('payments.paymentable_type', "App\Models\Expense\Expense")
            ->where('expenses.company_id', compid())
            ->leftJoin('suppliers', function ($supplier) {
                $supplier->on('suppliers.id', '=', 'expenses.userable_id')
                    ->where('expenses.userable_type', "App\Inventory\Supplier");
            })
            ->leftJoin('customers', function ($customer) {
                $customer->on('customers.id', '=', 'expenses.userable_id')
                    ->where('expenses.userable_type', "App\Inventory\Customer");
            })
            ->leftJoin('employees', function ($employee) {
                $employee->on('employees.id', '=', 'expenses.userable_id')
                    ->where('expenses.userable_type', "App\Payroll\Employee");
            })
            ->leftJoin('otherusers', function ($otheruser) {
                $otheruser->on('otherusers.id', '=', 'expenses.userable_id')
                    ->where('expenses.userable_type', "App\Inventory\Otheruser");
            })
            ->select(
                'payments.*',
                'expenses.id as expense_id',
                'expenses.ref as expense_ref',
                'suppliers.name as supplier_name',
                'customers.name as customer_name',
                'employees.name as employee_name',
                'otherusers.name as otheruser_name'
            );

        if (request()->get('model_id')) {
            $payments->where('expenses.id', request()->get('model_id'));
        }
        if (request()->get('ref')) {
            $payments->where('expenses.ref', 'like', '%'.request()->get('ref').'%');
        }
        if (request()->get('user_name')) {
            $payments
                ->where('suppliers.name', 'like', '%'.request()->get('user_name').'%')
                ->orWhere('customers.name', 'like', '%'.request()->get('user_name').'%')
                ->orWhere('employees.name', 'like', '%'.request()->get('user_name').'%')
                ->orWhere('otherusers.name', 'like', '%'.request()->get('user_name').'%');
        }
        if (request()->get('date')) {
            if (count(request()->input('date')) === 1) {
                $payments->whereDate('payments.date', request()->input('date')[0]);
            }
            if (count(request()->input('date')) == 2) {

                if (Carbon::parse(request()->input('date')[0]) < Carbon::parse(request()->input('date')[1])) {
                    $payments->whereBetween('payments.date', request()->get('date'));
                } else {
                    $payments->whereBetween('payments.date', array_reverse(request()->get('date')));
                }

            }
        }

        $this->orderByQuery($payments);

        $payments = request()->get('itemsPerPage')
            ? $payments->paginate(itemPerPage())
            : $payments->get();

        return response()->json($payments)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function received_show($id)
    {
        $this->checkAuthorization('payment_receive', 'show');

        $payment = Payment::with('paymentable.customer', 'paymentable.payments', 'transaction.bank', 'activities.causer')
            ->join('sales', 'sales.id', '=', 'payments.paymentable_id')
            ->where('sales.company_id', compid())
            ->where('payments.paymentable_type', 'App\Models\Inventory\Sale')
            ->where('payments.id', $id)
            ->select('payments.*')
            ->first();

        return response()->json($payment)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function bill_paid_show($id)
    {
        $this->checkAuthorization('bill_paid', 'show');
        $payment = Payment::with('paymentable.supplier', 'paymentable.payments', 'transaction.bank', 'activities.causer')
            ->join('purchases', 'purchases.id', '=', 'payments.paymentable_id')
            ->where('purchases.company_id', compid())
            ->where('payments.paymentable_type', Purchase::classPath())
            ->where('payments.id', $id)
            ->select('payments.*')
            ->first();

        return response()->json($payment)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function bill_paid()
    {
        $this->checkAuthorization('bill_paid', 'index');
        $payments = Payment::join('purchases', 'purchases.id', '=', 'payments.paymentable_id')
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->where('purchases.company_id', compid())
            ->where('payments.paymentable_type', Purchase::classPath())
            ->select('payments.*', 'purchases.id as purchase_id', 'purchases.ref as purchase_ref', 'purchases.bill_no as purchase_bill_no', 'suppliers.name as supplier_name');

        if (request()->get('supplier_name')) {
            $payments
                ->where('suppliers.name', 'like', '%'.trim(request()->get('supplier_name')).'%')
                ->where('suppliers.company_id', compid())
                ->orWhere('suppliers.code', 'like', '%'.trim(request()->get('supplier_name')).'%')
                ->orWhere('suppliers.email', 'like', '%'.trim(request()->get('supplier_name')).'%')
                ->orWhere('suppliers.phone', 'like', '%'.trim(request()->get('supplier_name')).'%');
        }

        if (request()->get('purchase_bill_no')) {
            $payments->where('purchases.bill_no', 'like', '%'.request()->get('purchase_bill_no').'%');
        }

        if (request()->get('id')) {
            $payments->where('purchases.id', request()->get('id'));
        }

        if (request()->get('date')) {
            if (count(request()->input('date')) === 1) {
                $payments->whereDate('payments.date', request()->input('date')[0]);
            }
            if (count(request()->input('date')) == 2) {

                if (Carbon::parse(request()->input('date')[0]) < Carbon::parse(request()->input('date')[1])) {
                    $payments->whereBetween('payments.date', request()->get('date'));
                } else {
                    $payments->whereBetween('payments.date', array_reverse(request()->get('date')));
                }

            }
        }
        $this->orderByQuery($payments);

        $payments = request()->get('itemsPerPage')
            ? $payments->paginate(itemPerPage())
            : $payments->get();

        return response()->json($payments)
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function received()
    {
        $this->checkAuthorization('payment_receive', 'index');
        $payments = Payment::join('sales', 'sales.id', '=', 'payments.paymentable_id')
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.company_id', compid())
            ->where('payments.paymentable_type', 'App\Models\Inventory\Sale')
            ->select('payments.*', 'sales.id as sale_id', 'sales.ref as sale_ref', 'customers.name as customer_name');

        if (request()->get('ref')) {
            $payments->where('sales.ref', 'like', '%'.request()->get('ref').'%');
        }

        if (request()->get('customer_name')) {
            $payments
                ->where('customers.name', 'like', '%'.trim(request()->get('customer_name')).'%')
                ->where('customers.company_id', compid())
                ->orWhere('customers.code', 'like', '%'.trim(request()->get('customer_name')).'%')
                ->orWhere('customers.email', 'like', '%'.trim(request()->get('customer_name')).'%')
                ->orWhere('customers.phone', 'like', '%'.trim(request()->get('customer_name')).'%');
        }

        if (request()->get('date')) {
            if (count(request()->input('date')) === 1) {
                $payments->whereDate('payments.date', request()->input('date')[0]);
            }
            if (count(request()->input('date')) == 2) {

                if (Carbon::parse(request()->input('date')[0]) < Carbon::parse(request()->input('date')[1])) {
                    $payments->whereBetween('payments.date', request()->get('date'));
                } else {
                    $payments->whereBetween('payments.date', array_reverse(request()->get('date')));
                }

            }
        }
        if (request()->get('id')) {
            $payments->where('sales.id', request()->get('id'));
        }

        $this->orderByQuery($payments);

        $payments = request()->get('itemsPerPage')
            ? $payments->paginate(itemPerPage())
            : $payments->get();

        return response()->json($payments)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index()
    {
        //
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
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response|string
     */
    public function store(
        PaymentCreateService $paymentCreateService,
        PaymentRequest $request,
        $modal,
        $paymenttableId
    ) {
        return $this->renderJsonOutput($paymentCreateService, $request, ['model' => $modal, 'paymentableId' => $paymenttableId]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
