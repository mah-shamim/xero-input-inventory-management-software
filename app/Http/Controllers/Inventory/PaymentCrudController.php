<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Expense\Expense;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\PaymentCrudRequest;
use App\Models\Income\Income;
use App\Models\Inventory\Payment;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Sale;

class PaymentCrudController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index()
    {
        $values = [];
        if (request()->query('model') === 'purchase' && request()->query('ref')) {
            $values = Purchase::where('purchases.company_id', compid())
                ->leftJoin('payments as p', function ($payments) {
                    $payments->on('p.paymentable_id', '=', 'purchases.id')
                        ->where('p.paymentable_type', '=', Purchase::classPath());
                })
                ->where('purchases.bill_no', 'like', '%' . request()->query('bill_no') . '%')
                ->orderBy('purchases.bill_no')
                ->select('purchases.*')
                ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
                ->groupBy('purchases.id')
                ->take(10)
                ->get();
        }
        if (request()->query('model') === 'sale' && request()->query('ref')) {
            $values = Sale::where('sales.company_id', compid())
                ->leftJoin('payments as p', function ($payments) {
                    $payments->on('p.paymentable_id', '=', 'sales.id')
                        ->where('p.paymentable_type', '=', Sale::classPath());
                })
                ->where('sales.ref', 'LIKE', '%' . trim(request()->query('ref')) . '%')
                ->orderBy('sales.ref')
                ->select('sales.*')
                ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
                ->groupBy('sales.id')
                ->take(10)
                ->get();
        }
        if (request()->query('model') === 'expense' && request()->query('ref')) {
            $values = Expense::where('expenses.company_id', compid())
                ->leftJoin('payments as p', function ($payments) {
                    $payments->on('p.paymentable_id', '=', 'expenses.id')
                        ->where('p.paymentable_type', '=', Expense::classPath());
                })
//                ->join('payments as p', 'p.paymentable_id', '=', 'expenses.id')
                ->where('p.paymentable_type', '=', Expense::classPath())
                ->where('expenses.ref', 'LIKE', '%' . trim(request()->query('ref')) . '%')
                ->orderBy('expenses.ref')
                ->select('expenses.*')
                ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
                ->groupBy('expenses.id')
                ->take(10)
                ->get();
        }

        return response()->json($values)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create()
    {
        $value = null;

        if (request()->query('model') === 'purchase' && request()->query('multiple')) {
            $value = Purchase::where('purchases.company_id', compid())
                ->leftJoin('payments as p', function ($payments) {
                    $payments->on('p.paymentable_id', '=', 'purchases.id')
                        ->where('p.paymentable_type', '=', Purchase::classPath());
                })
                ->whereIn('purchases.id', request()->query('model_ids'))
                ->select('purchases.*')
                ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
                ->groupBy('purchases.id')
                ->get();
        }

        if (request()->query('model') === 'expense' && request()->query('model_id')) {
            $value = Expense::where('expenses.company_id', compid())
                ->leftjoin('payments as p', function ($join) {
                    $join->on('p.paymentable_id', '=', 'expenses.id')
                        ->where('p.paymentable_type', '=', Expense::classPath());
                })
                ->where('expenses.id', request()->query('model_id'))
                ->select('expenses.*', 'expenses.amount as total')
                ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
                ->groupBy('expenses.id')
                ->first();
        }

        if (request()->query('model') === 'income' && request()->query('model_id')) {
            $value = Income::where('incomes.company_id', compid())
                ->leftjoin('payments as p', function ($join) {
                    $join->on('p.paymentable_id', '=', 'incomes.id')
                        ->where('p.paymentable_type', '=', Income::classPath());
                })
                ->where('incomes.id', request()->query('model_id'))
                ->select('incomes.*', 'incomes.amount as total')
                ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
                ->groupBy('incomes.id')
                ->first();
        }

        if (request()->query('model') === 'purchase' && request()->query('model_id')) {
            $value = Purchase::where('purchases.company_id', compid())
                ->leftJoin('payments as p', function ($payments) {
                    $payments->on('p.paymentable_id', '=', 'purchases.id')
                        ->where('p.paymentable_type', '=', Purchase::classPath());
                })
                ->where('purchases.id', request()->query('model_id'))
                ->select('purchases.*')
                ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
                ->groupBy('purchases.id')
                ->first();
        }

        if (request()->query('model') === 'sale' && request()->query('model_id')) {
            $value = Sale::where('sales.company_id', compid())
                ->leftJoin('payments as p', function ($payments) {
                    $payments->on('p.paymentable_id', '=', 'sales.id')
                        ->where('p.paymentable_type', '=', Sale::classPath());
                })
                ->where('sales.id', request()->query('model_id'))
                ->select('sales.*')
                ->selectRaw('coalesce(sum(p.paid),0) as paid_total')
                ->groupBy('sales.id')
                ->first();
        }

        return response()->json($value)
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PaymentCrudRequest $request)
    {
        return $request->save();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function show($id)
    {
        $payment = Payment::with(
            'paymentable.userable',
            'paymentable.payments',
            'transaction.bank',
            'activities.causer'
        )
            ->where('id', $id)
            ->first();

        abort_unless($payment->paymentable['company_id'] === compid(), 403);

        return response()->json($payment)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function edit($id)
    {
        $type = $this->getClassPath();

        $payment = Payment::with('paymentable', 'transaction')->where('payments.id', $id)
            ->where('payments.paymentable_type', '=', $type)
            ->select('payments.*')
            ->first();

        return response()->json($payment)
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PaymentCrudRequest $request, $id)
    {
        return $request->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getClassPath(): string
    {
        $classes = [
            'purchase' => Purchase::classPath(),
            'expense' => Expense::classPath(),
            'income' => Income::classPath(),
            'sale' => Sale::classPath()
        ];
        return $classes[request()->query('model')];
    }
}
