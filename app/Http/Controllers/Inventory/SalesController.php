<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\SalesRequest;
use App\Models\Company;
use App\Models\Inventory\Customer;
use App\Models\Inventory\Product;
use App\Models\Inventory\Returns;
use App\Models\Inventory\Sale;
use App\Models\Inventory\Salestable;
use App\Models\Inventory\Unit;
use App\Services\Inventory\Sales\SalesCreateService;
use App\Services\Inventory\Sales\SalesDeleteService;
use App\Services\Inventory\Sales\SalesEditService;
use App\Services\Inventory\Sales\SalesUpdateService;
use App\Traits\Ledger;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    use Ledger;

    /**
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
//        @todo authorization
//        $this->checkAuthorization('sales', 'index');
        $sales = Sale::fromCompany()->indexQuery();
        $query = request()->query();

        if ($query) {
            $keys = array_keys($query);
            foreach ($keys as $key) {
                if ($key == 'ref' || $key == 'biller') {
                    $sales->where($key, 'like', '%' . $query[$key] . '%');
                }
                if ($key == 'status') {
                    $sales->where($key, $query[$key]);
                }
                if ($key == 'customer_name') {
                    $sales->where('customers.name', 'like', '%' . $query[$key] . '%')
                        ->orWhere('customers.code', 'like', '%' . $query[$key] . '%');
                }
                if ($key == 'part_number') {
                    $sales->whereHas('partnumbers', function ($sales) use ($query, $key) {
                        $sales->where('part_number', 'like', $query[$key])
                            ->whereNotNull('sale_id');
                    });
                }
            }
        }
        if (!empty(request()->query('sortBy')) && !empty(request()->query('sortDesc'))) {
            $sales = $sales->orderBy(request()->query('sortBy')[0], request()->query('sortDesc')[0] === 'true' ? 'desc' : 'asc');
        }

        $sales = $sales->paginate(itemPerPage());

        return response()->json($sales)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function create(): \Illuminate\Http\JsonResponse
    {
        //        @todo authorization
//        $this->checkAuthorization('sales', 'create');

        return response()->json([
            'products' => Product::with('warehouses', 'units')
                ->has('warehouses')
                ->has('units')
                ->whereCompanyId(request()->input('company_id'))
                ->take(10)
                ->get(),
            'customers' => Customer::customerList()->get(),
            'tables' => Salestable::where('company_id', request()->input('company_id'))->get(),
            'units' => Unit::unitList()->get(),
            'banks' => $this->listOfBankWithRunningBalance(),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function store(SalesCreateService $salesCreateService, SalesRequest $salesRequest): \Illuminate\Http\Response|string
    {
        $this->checkAuthorization('sales', 'create');

        return $this->renderJsonOutput($salesCreateService, $salesRequest, null);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $this->checkAuthorization('sales', 'show');
        $sales = Sale::with([
            'customer',
            'products',
            'partnumbers',
            'payments.transaction',
        ])->fromCompany()->find($id);

        foreach ($sales->products as $product) {
            $product->quantityStr = $product->getQuantityWithConversions($product);
        }

        $returns = Returns::with(['product', 'unit', 'transaction'])
            ->whereReturnableId($id)->whereReturnableType("App\Models\Inventory\Sale")
            ->whereCompanyId(request()->input('company_id'))->get();

        return response()->json([
            'sale' => $sales,
            'company' => Company::select('id', 'address1', 'contact_phone', 'name')->findOrFail(auth()->user()->company_id),
            'returns' => $returns,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response|string
     */
    public function edit(SalesEditService $salesEditService, $id)
    {
        $this->checkAuthorization('sales', 'edit');

        return $this->renderJsonOutput($salesEditService, request(), ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $
     * @return \Illuminate\Http\Response
     */
    public function update(SalesUpdateService $salesUpdateService, SalesRequest $salesRequest, $id)
    {
        $this->checkAuthorization('sales', 'edit');

        return $this->renderJsonOutput($salesUpdateService, $salesRequest, ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy(SalesDeleteService $salesDeleteService, Request $request, $id)
    {
        $this->checkAuthorization('sales', 'delete');

        return $this->renderJsonOutput($salesDeleteService, $request, ['id' => $id]);
    }
}
