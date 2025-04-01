<?php

namespace App\Http\Controllers\Inventory;

use App\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\SalequotationRequest;
use App\Inventory\Customer;
use App\Inventory\Product;
use App\Models\Inventory\Salequotation;
use App\Inventory\Unit;
use App\Inventory\Warehouse;
use App\Services\Inventory\Quotation\QuotationStoreService;
use App\Services\Inventory\Quotation\QuotationUpdateService;
use App\Traits\Ledger;

class SalequotationController extends Controller
{
    use Ledger;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $this->checkAuthorization('saleQuotation', 'index');

        $saleQuotations = Salequotation::with('createdBy')->select();
        if (request()->get('id')) {
            $saleQuotations->where('id', 'like', '%'.request()->get('id').'%');
        }
        if (request()->get('quotation_no')) {
            $saleQuotations->where('quotation_no', 'like', '%'.request()->get('quotation_no').'%');
        }

        if (request()->get('customer_name')) {
            $saleQuotations->whereHas('customer', function ($sq) {
                $sq->where('name', 'like', '%'.request()->get('customer_name').'%');
            });
        }
        if (request()->get('created_by')) {
            $saleQuotations->whereHas('createdBy', function ($sq) {
                $sq->where('name', 'like', '%'.request()->get('created_by').'%');
            });
        }
        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $saleQuotations = $saleQuotations->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');

            return response()->json($saleQuotations->paginate(request()->get('itemsPerPage')))
                ->setEncodingOptions(JSON_NUMERIC_CHECK);
        }

        return response()->json($saleQuotations->paginate(request()->get('itemsPerPage')))
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        $this->checkAuthorization('saleQuotation', 'create');

        $sale_query = Salequotation::latest()->first();
        $latest_quotation_no = (bool) $sale_query ? $sale_query->quotation_no : 0;
        $latest_quotation_no = $latest_quotation_no + 1;

        return response()->json([
            'products' => Product::with('warehouses', 'units')
                ->has('warehouses')
                ->has('units')
                ->whereCompanyId(request()->input('company_id'))
                ->take(10)
                ->get(),
            'customers' => Customer::customerList()->get(),
            'units' => Unit::unitList()->get(),
            'settings' => [
                'currency' => session()->get('settings')->settings['currency'],
                'inv_settings' => session()->get('settings')->settings['inventory'],
            ],
            'latest_sq' => $latest_quotation_no,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return string
     */
    public function store(
        QuotationStoreService $quotationStoreService,
        SalequotationRequest $request
    ) {
        $this->checkAuthorization('saleQuotation', 'create');

        return $this->renderJsonOutput($quotationStoreService, $request, null);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($sale_quotation)
    {
        $this->checkAuthorization('saleQuotation', 'show');

        return response()->json([
            'quotations' => Salequotation::with(['createdBy', 'products.warehouses', 'products.units'])->where('id', $sale_quotation)->first(),
            'company' => Company::select('id', 'address1', 'contact_phone', 'name')->findOrFail(auth()->user()->company_id),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($sale_quotation)
    {
        $this->checkAuthorization('saleQuotation', 'edit');

        $sale_quotation = Salequotation::with([
            'products.partnumbers' => function ($pn) {
                $pn->whereNull('sale_id');
            },
            'createdBy',
            'products.warehouses',
            'products.units',
        ])
            ->where('id', $sale_quotation)
            ->first();
        if ($sale_quotation->sale_id) {
            return response()->json([
                'type' => 'warning',
                'message' => 'The quotation has been sold on '.$sale_quotation->quotation_date,
            ]);
        }

        return response()->json([
            'products' => Product::with('warehouses', 'units', 'partnumbers')->has('warehouses')->has('units')
                ->productList()
                ->get(),
            'customers' => Customer::customerList()->get(),
            'units' => Unit::unitList()->get(),
            'banks' => $this->listOfBankWithRunningBalance(),
            'quotation' => $sale_quotation,
            'settings' => [
                'currency' => session()->get('settings')->settings['currency'],
                'inv_settings' => session()->get('settings')->settings['inventory'],
            ],
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return string
     */
    public function update(
        QuotationUpdateService $quotationUpdateService,
        SalequotationRequest $request,
        $sale_quotation
    ) {
        $this->checkAuthorization('saleQuotation', 'edit');

        return $this->renderJsonOutput($quotationUpdateService, $request, ['id' => $sale_quotation]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function destroy(Salequotation $sale_quotation)
    {
        $this->checkAuthorization('saleQuotation', 'delete');

        $sale_quotation->delete();

        return response()->json([
            'type' => 'success',
            'message' => 'Quotation has been deleted successfully',
        ]);
    }
}
