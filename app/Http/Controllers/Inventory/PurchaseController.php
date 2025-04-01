<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\PurchaseRequest;
use App\Models\Company;
use App\Models\Inventory\Product;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\Unit;
use App\Models\Inventory\UnitConversion;
use App\Models\Inventory\Warehouse\Warehouse;
use App\Services\Inventory\Purchase\PurchaseCreateService;
use App\Services\Inventory\Purchase\PurchaseDeleteService;
use App\Services\Inventory\Purchase\PurchaseEditService;
use App\Services\Inventory\Purchase\PurchaseUpdateService;
use App\Traits\Ledger;
use Illuminate\Http\Request;
use DB;
use ReflectionClass;


class PurchaseController extends Controller
{

    use Ledger;

    public function index()
    {
//        @todo
//        $this->authorize('viewAny', Purchase::class);

        $purchaseList = Purchase::select('purchases.*', DB::raw("CONCAT(suppliers.company, '-' ,suppliers.code) as supplier_company_id"))
            ->where('purchases.company_id', request()->input('company_id'))
            ->leftJoin('payments as p', function ($payment) {
                $payment->on('p.paymentable_id', '=', 'purchases.id')
                    ->where('p.paymentable_type', '=', Purchase::classPath());
            })
            ->withCount('products')
            ->withCount('payments')
            ->withCount('returns')
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->selectRaw('coalesce(sum(p.paid),0) as total_paid')
            ->selectRaw('coalesce(purchases.total - coalesce(sum(p.paid),0)) as due')
            ->groupBy('purchases.id');

        //        dd($purchaseList->get());

        $query = request()->query();
        if ($query) {
            $keys = array_keys($query);
            foreach ($keys as $key) {
                if ($key == 'bill_no') {
                    $purchaseList->where($key, 'like', '%' . $query[$key] . '%');
                } elseif ($key == 'company') {
                    $purchaseList->where('suppliers.company', 'like', '%' . $query[$key] . '%')
                        ->orWhere('code', 'like', '%' . $query[$key] . '%');
                }
            }
        }
        if (request()->get('supplier_id')) {
            $purchaseList->where('purchases.supplier_id', request()->get('supplier_id'));
        }

        if (request()->get('purchase_date') && count(request()->get('purchase_date')) > 1) {
            $purchaseList->whereBetween('purchases.purchase_date', request()->get('purchase_date'));
        }

        if (request()->get('sortBy') && !empty(request()->get('sortBy'))) {
            $purchaseList->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }

        if (request()->get('part_number')) {
            $purchaseList->whereHas('partnumbers', function ($query) {
                $query->where('part_number', 'like', request()->get('part_number'));
            });
        }

        return response()->json($purchaseList->paginate(itemPerPage()))
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
    //    public function index(PurchaseListService $purchaseListService, Request $request)
    //    {
    //        $result = $this->renderArrayOutput($purchaseListService, $request, null);
    //
    //        return $result['purchases'];
    //    }

    public function create()
    {
//        @todo
//        $this->authorize('create', Purchase::class);

        return response()->json([
            'warehouses' => Warehouse::warehouseList()->get(),
            'products' => Product::with('warehouses', 'units')
                ->has('units')
                ->whereCompanyId(request()->company_id)
                ->productList()
                ->get(),
            'suppliers' => Supplier::supplierList()->get(),
            'units' => Unit::unitList()->get(),
            'unitconversions' => UnitConversion::with('from_unit', 'to_unit')->whereCompanyId(request()->company_id)->get(),
            'banks' => $this->listOfBankWithRunningBalance(),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function store(PurchaseCreateService $purchaseCreateService, PurchaseRequest $request)
    {
//        @todo
//        $this->authorize('create', Purchase::class);

        return $this->renderJsonOutput($purchaseCreateService, $request, null);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
//        @todo
//        $this->authorize('view', Purchase::find($id));

        $purchase = Purchase::with([
            'supplier',
            'partnumbers',
            'returns.unit',
            'products.units',
            'returns.product',
            'returns.transaction',
            'payments.transaction',
        ])->find($id);

        foreach ($purchase->products as $product) {
            $product->quantityStr = $product->getQuantityWithConversions($product);
        }

        return response()->json([
            'purchase' => $purchase,
            'company' => Company::select('id', 'address1', 'contact_phone', 'name')
                ->findOrFail(auth()->user()->company_id),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function edit(PurchaseEditService $purchaseEditService, Request $request, $id)
    {
//        @todo
//        $this->authorize('update', Purchase::find($id));

        return $this->renderJsonOutput($purchaseEditService, $request, ['id' => $id]);
    }

    public function update(PurchaseUpdateService $purchaseUpdateService, PurchaseRequest $request, $id)
    {
//        @todo
//        $this->authorize('update', Purchase::find($id));

        return $this->renderJsonOutput($purchaseUpdateService, $request, ['id' => $id]);
    }

    public function destroy(PurchaseDeleteService $purchaseDeleteService, Request $request, $id
    )
    {
//        @todo
//        $this->authorize('delete', Purchase::find($id));

        return $this->renderJsonOutput($purchaseDeleteService, $request, ['id' => $id]);
    }
}
