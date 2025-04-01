<?php

namespace App\Http\Controllers\Bank;

use App\Models\Bank\Bank;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bank\BankFormRequest;
use App\Models\Inventory\Traits\BankControllerTraits;
use App\Traits\Ledger;
use App\Traits\Reconcile;

class BankController extends Controller
{
    use Ledger,
        BankControllerTraits,
        Reconcile;

    public function index()
    {
        $this->authorize('viewAny', Bank::class);
        $banks = Bank::where('company_id', request()->input('company_id'))->select();
        $query = request()->query();
        $pages = array_key_exists('itemsPerPage', $query) && $query['itemsPerPage']
            ? $query['itemsPerPage']
            : getResultPerPage();
        if (request()->get('name')) {
            $banks->where('name', 'LIKE', request()->get('name').'%');
        }
        if (request()->get('account_no')) {
            $banks->where('account_no', 'LIKE', request()->get('account_no').'%');
        }
        if (request()->get('branch')) {
            $banks->where('branch', 'LIKE', request()->get('branch').'%');
        }
        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $banks->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }
        $banks = $banks->paginate($pages);
        if ($banks) {
            foreach ($banks as $bank) {
                $bank->running_balance = $this->bankTransactionTotal_debit_credit($bank->id)
                    ? $this->bankTransactionTotal_debit_credit($bank->id)->running_balance
                    : 0;

                $bank->total_debit = $this->bankTransactionTotal_debit_credit($bank->id)
                    ? $this->bankTransactionTotal_debit_credit($bank->id)->total_debits
                    : 0;

                $bank->total_credit = $this->bankTransactionTotal_debit_credit($bank->id)
                    ? $this->bankTransactionTotal_debit_credit($bank->id)->total_credits
                    : 0;
            }
        }

        return response()->json([
            'banks' => $banks,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function create()
    {

    }

    public function store(BankFormRequest $request)
    {
        $this->authorize('create', Bank::class);

        return $request->save();
    }

    public function show($id)
    {
        $data = [];
        if (request()->get('total_debit_credit')) {
            $data['bankTransactionTotal_debit_credit'] = $this->bankTransactionTotal_debit_credit($id);
        }
        if (request()->get('ledger_per_bank')) {
            $query = request()->query();
            $pages = array_key_exists('itemsPerPage', $query) && $query['itemsPerPage']
                ? $query['itemsPerPage']
                : getResultPerPage();
            $ledger_per_bank = $this->bankTransaction_ledger($id);
            $sortBy = request()->query('sortBy');
            $sortDesc = request()->query('sortDesc');
            if ($sortBy) {
                $ledger_per_bank->orderBy($sortBy[0], $sortDesc[0] === 'false' ? 'asc' : 'desc');
            }
            if (request()->get('created_at')) {
                $ledger_per_bank->whereBetween('created_at', [request()->get('created_at')[0], request()->get('created_at')[1]]);
            }
            $data['ledger_per_bank'] = $ledger_per_bank->paginate($pages);
        }

        return response()->json($data)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function edit($id)
    {
    }

    public function update(BankFormRequest $request, Bank $bank)
    {
        $this->authorize('update', $bank);

        return $request->update($bank);
    }

    public function destroy(Bank $bank)
    {
        $this->authorize('delete', $bank);
        try {
            $bank->delete();

            return response()->json([
                'type' => 'success',
                'message' => 'Bank has been deleted successfully',
            ]);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
