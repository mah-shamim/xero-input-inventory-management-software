<?php

namespace App\Http\Controllers\Inventory;

use App\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\PurchaseOrderRequest;
use App\Inventory\Orderpurchase;
use App\Inventory\Product;
use App\Inventory\Supplier;
use App\Inventory\Unit;
use App\Inventory\UnitConversion;
use App\Inventory\Warehouse;
use App\Services\Inventory\Order\OrderUpdateService;
use App\Services\Inventory\Order\Purchase\OrderPurchaseCreateService;
use App\Services\Inventory\UnitConversion\UnitConversionConvertService;
use App\Traits\Ledger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    use Ledger;

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index()
    {
        $this->checkAuthorization('purchase_order', 'index');

        $purchase_orders = Orderpurchase::select(
            'orderpurchases.*',
            DB::raw("CONCAT(suppliers.name, '-' ,suppliers.code) as supplier_name_id"
            )
        )
            ->join('suppliers', 'suppliers.id', '=', 'orderpurchases.supplier_id')
            ->where('orderpurchases.company_id', compid())
            ->groupBy('orderpurchases.id');

        if (request()->get('id')) {
            $purchase_orders->where('id', request()->get('id'));
        }

        if (request()->get('order_no')) {
            $purchase_orders->where('orderpurchases.order_no', request()->get('order_no'));
        }

        if (request()->get('supplier_name')) {
            $purchase_orders->where('suppliers.company', 'like', '%'.request()->get('supplier_name').'%')
                ->orWhere('suppliers.code', 'like', '%'.request()->get('supplier_name').'%');
        }

        if (request()->get('order_date')) {
            if (count(request()->input('order_date')) === 1) {
                $purchase_orders->whereDate('orderpurchases.order_date', request()->input('order_date')[0]);
            }
            if (count(request()->input('order_date')) == 2) {

                if (Carbon::parse(request()->input('order_date')[0]) < Carbon::parse(request()->input('order_date')[1])) {
                    $purchase_orders->whereBetween('orderpurchases.order_date', request()->get('order_date'));
                } else {
                    $purchase_orders->whereBetween('orderpurchases.order_date', array_reverse(request()->get('order_date')));
                }
            }
        }

        if (! empty(request()->query('sortBy')) && ! empty(request()->query('sortDesc'))) {
            $purchase_orders = $purchase_orders->orderBy(request()->query('sortBy')[0], request()->query('sortDesc')[0] === 'true' ? 'desc' : 'asc');
        }

        return response()->json($purchase_orders->latest()->paginate(itemPerPage()))->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create()
    {
        $this->checkAuthorization('purchase_order', 'create');

        $latest_order = Orderpurchase::latest()->first();

        if ($latest_order) {
            $latest_order = $latest_order->order_no;
        } else {
            $latest_order = 0;
        }

        $latest_order = is_numeric($latest_order) ? $latest_order : 0;
        $latest_order = $latest_order + 1;

        return response()->json([
            'warehouses' => Warehouse::warehouseList()->get(),
            'products' => Product::with('warehouses', 'units')
                ->has('units')->whereCompanyId(request()->company_id)
                ->productList()
                ->get(),
            'suppliers' => Supplier::supplierList()->get(),
            'units' => Unit::unitList()->get(),
            'unitconversions' => UnitConversion::with('from_unit', 'to_unit')->whereCompanyId(compid())->get(),
            'latest_po' => $latest_order,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return string|void
     */
    public function store(PurchaseOrderRequest $request, OrderPurchaseCreateService $orderPurchaseCreateService)
    {
        $this->checkAuthorization('purchase_order', 'create');

        return $this->renderJsonOutput($orderPurchaseCreateService, $request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function show($id)
    {
        $this->checkAuthorization('purchase_order', 'show');

        $order = Orderpurchase::with(['supplier', 'createdBy', 'products.warehouses', 'products.units'])
            ->where('company_id', compid())->where('id', $id)
            ->first();
        foreach ($order->products as $product) {
            if ($product->base_unit_id !== $product->pivot->unit_id) {
                $unitConversionConvertService = new UnitConversionConvertService();
                $value = $this->renderJsonOutput($unitConversionConvertService, request(), [
                    'from_unit_id' => $product->pivot->unit_id,
                    'to_unit_id' => $product->base_unit_id,
                    'quantity' => 1,
                ]
                );
                $value = json_decode($value);
                $product->conversion_factor = $value->conversion->conversion_factor;
            }
        }

        return response()->json([
            'orders' => $order,
            'company' => Company::select('id', 'address1', 'contact_phone', 'name')
                ->findOrFail(auth()->user()->company_id),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->checkAuthorization('purchase_order', 'edit');

        $order_purchase = Orderpurchase::fromCompany()->with(['createdBy', 'products.warehouses', 'products.units'])
            ->where('id', $id)
            ->first();
        if ($order_purchase->purchase_id) {
            return response()->json([
                'type' => 'warning',
                'message' => 'The quotation has been sold on '.$order_purchase->updated_at,
            ]);
        }

        return response()->json([
            'warehouses' => Warehouse::warehouseList()->get(),
            'products' => Product::with('warehouses', 'units')->productList()->get(),
            'suppliers' => Supplier::SupplierList()->get(),
            'units' => Unit::unitList()->get(),
            'order' => $order_purchase,
            'banks' => $this->listOfBankWithRunningBalance(),
            'settings' => [
                'currency' => session()->get('settings')->settings['currency'],
                'inv_settings' => session()->get('settings')->settings['inventory'],
            ],
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return string|object
     */
    public function update(
        PurchaseOrderRequest $request,
        OrderUpdateService $orderUpdateService,
        $id
    ) {
        $this->checkAuthorization('purchase_order', 'edit');

        return $this->renderJsonOutput($orderUpdateService, $request, ['id' => $id]);
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

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function toBeReceived()
    {
        $toBeReceived = Orderpurchase::fromCompany()->whereNotNull('expected_receive_date')
            ->whereNull('received_date');

        if (request()->get('order_no')) {
            $toBeReceived->where('order_no', request()->get('order_no'));
        }

        if (request()->get('expected_receive_date')) {
            if (count(request()->input('expected_receive_date')) === 1) {
                $toBeReceived->whereDate('expected_receive_date', request()->input('expected_receive_date')[0]);
            }
            if (count(request()->input('expected_receive_date')) == 2) {

                if (Carbon::parse(request()->input('expected_receive_date')[0]) < Carbon::parse(request()->input('expected_receive_date')[1])) {
                    $toBeReceived->whereBetween('expected_receive_date', request()->get('expected_receive_date'));
                } else {
                    $toBeReceived->whereBetween('expected_receive_date', array_reverse(request()->get('expected_receive_date')));
                }

            }
        }

        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $toBeReceived->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }

        return response()->json(
            $toBeReceived->paginate(itemPerPage())
        )->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function received()
    {
        $delivered = Orderpurchase::fromCompany()->whereNotNull('received_date');

        if (request()->get('order_no')) {
            $delivered->where('order_no', request()->get('order_no'));
        }

        if (request()->get('received_date')) {
            if (count(request()->input('received_date')) === 1) {
                $delivered->whereDate('received_date', request()->input('received_date')[0]);
            }
            if (count(request()->input('received_date')) == 2) {

                if (Carbon::parse(request()->input('received_date')[0]) < Carbon::parse(request()->input('received_date')[1])) {
                    $delivered->whereBetween('received_date', request()->get('received_date'));
                } else {
                    $delivered->whereBetween('received_date', array_reverse(request()->get('received_date')));
                }
            }
        }

        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $delivered->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }

        return response()->json(
            $delivered->paginate(itemPerPage())
        )->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsReceived()
    {
        request()->validate([
            'received_date' => 'required',
        ]);
        $order = Orderpurchase::fromCompany()->where('id', request()->input('id'))->first();
        $order->update([
            'received_date' => Carbon::parse(request()->input('received_date'))->format('Y-m-d H:m:s'),
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Order successfully marked as delivered',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeReceive()
    {
        $order = Orderpurchase::fromCompany()->where('id', request()->input('id'))->first();
        $order->update([
            'received_date' => null,
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'deliver status changed',
        ]);
    }
}
