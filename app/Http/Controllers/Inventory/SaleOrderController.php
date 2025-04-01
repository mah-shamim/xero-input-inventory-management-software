<?php

namespace App\Http\Controllers\Inventory;

use App\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\OrderRequest;
use App\Inventory\Customer;
use App\Inventory\Order;
use App\Inventory\Product;
use App\Inventory\Unit;
use App\Inventory\Warehouse;
use App\Services\Inventory\Order\OrderCreateService;
use App\Services\Inventory\Order\OrderUpdateService;
use App\Services\Inventory\UnitConversion\UnitConversionConvertService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleOrderController extends Controller
{
    public function cancel_toggle()
    {
        $this->checkAuthorization('sale_order', 'cancel_toggle');
        $order = Order::where('id', request()->input('id'))->where('company_id', compid())->first();
        if (! $order->sale_id) {
            $order->update([
                'is_canceled' => ! (bool) $order->is_canceled,
            ]);
        }
        $order = Order::where('id', request()->input('id'))->where('company_id', compid())->first();

        return response()->json([
            'type' => 'success',
            'message' => $order->is_canceled ? 'Order has been canceled successfully' : 'Order has been reopen successfully',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index()
    {
        $this->checkAuthorization('sale_order', 'index');

        $sale_orders = Order::where('orders.company_id', compid())
            ->select(
                'orders.*',
                DB::raw("CONCAT(customers.name, '-' ,customers.code) as customer_name_id")
            )
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->groupBy('orders.id');

        if (request()->get('id')) {
            $sale_orders->where('orders.id', request()->get('id'));
        }

        if (request()->get('order_no')) {
            $sale_orders->where('orders.order_no', request()->get('order_no'));
        }

        if (request()->get('status')) {
            if (request()->get('status') === 'open') {
                $sale_orders->whereNull('orders.sale_id')->where('is_canceled', false);
            }
            if (request()->get('status') === 'closed') {
                $sale_orders->whereNotNull('orders.sale_id');
            }
            if (request()->get('status') === 'canceled') {
                $sale_orders->where('is_canceled', true);
            }
        }

        if (request()->get('customer_order_no')) {
            $sale_orders->where('orders.customer_order_no', 'like', '%'.request()->get('customer_order_no').'%');
        }

        if (request()->get('customer_name')) {
            $sale_orders->where('orders.name', 'like', '%'.request()->get('customer_name').'%')
                ->orWhere('orders.code', request()->get('customer_name'));
        }

        if (request()->get('order_date')) {
            if (count(request()->input('order_date')) === 1) {
                $sale_orders->whereDate('orders.order_date', request()->input('order_date')[0]);
            }
            if (count(request()->input('order_date')) == 2) {

                if (Carbon::parse(request()->input('order_date')[0]) < Carbon::parse(request()->input('order_date')[1])) {
                    $sale_orders->whereBetween('orders.order_date', request()->get('order_date'));
                } else {
                    $sale_orders->whereBetween('orders.order_date', array_reverse(request()->get('order_date')));
                }
            }
        }

        if (! empty(request()->query('sortBy')) && ! empty(request()->query('sortDesc'))) {
            $sale_orders = $sale_orders->orderBy(request()->query('sortBy')[0], request()->query('sortDesc')[0] === 'true' ? 'desc' : 'asc');
        }

        return response()->json($sale_orders->paginate(itemPerPage()))
            ->setEncodingOptions(JSON_NUMERIC_CHECK);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create()
    {
        $this->checkAuthorization('sale_order', 'create');

        $latest_order = Order::latest()->first();

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
            'latest_so' => $latest_order,
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response|string
     */
    public function store(OrderRequest $request, OrderCreateService $orderCreateService)
    {
        $this->checkAuthorization('sale_order', 'create');

        return $this->renderJsonOutput($orderCreateService, $request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function show($id)
    {
        $this->checkAuthorization('sale_order', 'show');

        $order = Order::with([
            'user',
            'customer',
            'products.units',
            'products.usedInBom',
            'products.warehouses',
            'orderpurchases.products',
            'products.usedInBom.main',
            'products.usedInBom.unit',
            'products.bill_of_materials.unit',
            'products.bill_of_materials.product.unit',
            'products.bill_of_materials.product.units',
            'products.bill_of_materials.product.warehouses',
            'products.bill_of_materials.product.bill_of_materials',
        ])->where('company_id', compid())->where('id', $id)->first();

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
        $this->checkAuthorization('sale_order', 'edit');

        $sale_order = Order::with([
            'products.partnumbers' => function ($pn) {
                $pn->whereNull('sale_id');
            },
            'user',
            'products.warehouses',
            'products.units',
            'orderpurchases',
        ])
            ->where('id', $id)
            ->where('company_id', compid())
            ->first();
        if ($sale_order->sale_id) {
            return response()->json([
                'type' => 'warning',
                'message' => 'The order has been sold on '.$sale_order->order_date,
            ]);
        }

        return response()->json([
            'warehouses' => Warehouse::warehouseList()->get(),
            'products' => Product::with(['warehouses', 'units', 'partnumbers' => function ($pn) {
                $pn->whereNull('sale_id');
            }])
                ->has('warehouses')
                ->has('units')
                ->productList()
                ->get(),
            'customers' => Customer::customerList()->get(),
            'units' => Unit::unitList()->get(),
            'order' => $sale_order,
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
    public function update(OrderRequest $request, OrderUpdateService $orderUpdateService, $id)
    {
        $this->checkAuthorization('sale_order', 'edit');

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

    public function toBeShipped()
    {
        $toToShipped = Order::whereNotNull('expected_shipping_date')
            ->whereNull('delivered_date')
            ->where('company_id', compid());

        if (request()->get('order_no')) {
            $toToShipped->where('order_no', request()->get('order_no'));
        }

        if (request()->get('expected_shipping_date')) {
            if (count(request()->input('expected_shipping_date')) === 1) {
                $toToShipped->whereDate('expected_shipping_date', request()->input('expected_shipping_date')[0]);
            }
            if (count(request()->input('expected_shipping_date')) == 2) {

                if (Carbon::parse(request()->input('expected_shipping_date')[0]) < Carbon::parse(request()->input('expected_shipping_date')[1])) {
                    $toToShipped->whereBetween('expected_shipping_date', request()->get('expected_shipping_date'));
                } else {
                    $toToShipped->whereBetween('expected_shipping_date', array_reverse(request()->get('expected_shipping_date')));
                }

            }
        }

        if (request()->get('sortBy') && ! empty(request()->get('sortBy'))) {
            $toToShipped->orderBy(request()->get('sortBy')[0], request()->get('sortDesc')[0] ? 'desc' : 'asc');
        }

        return response()->json(
            $toToShipped->paginate(itemPerPage())
        )->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function delivered()
    {
        $delivered = Order::whereNotNull('delivered_date')
            ->where('company_id', compid());

        if (request()->get('order_no')) {
            $delivered->where('order_no', request()->get('order_no'));
        }

        if (request()->get('delivered_date')) {
            if (count(request()->input('delivered_date')) === 1) {
                $delivered->whereDate('delivered_date', request()->input('delivered_date')[0]);
            }
            if (count(request()->input('delivered_date')) == 2) {
                if (Carbon::parse(request()->input('delivered_date')[0]) < Carbon::parse(request()->input('delivered_date')[1])) {
                    $delivered->whereBetween('delivered_date', request()->get('delivered_date'));
                } else {
                    $delivered->whereBetween('delivered_date', array_reverse(request()->get('delivered_date')));
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

    public function markAsDelivered()
    {
        request()->validate([
            'delivered_date' => 'required',
        ]);

        $order = Order::where('id', request()->input('id'))
            ->where('company_id', compid())
            ->first();

        $order->update([
            'delivered_date' => Carbon::parse(request()
                ->input('delivered_date'))
                ->format('Y-m-d H:m:s'),
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Order successfully marked as delivered',
        ]);
    }

    public function changeShipping()
    {
        request()->validate([
            'expected_shipping_date' => 'required',
        ]);

        $order = Order::where('id', request()->input('id'))->where('company_id', compid())->first();
        $order->update([
            'delivered_date' => null,
            'expected_shipping_date' => Carbon::parse(request()->input('expected_shipping_date'))->format('Y-m-d H:m:s'),
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'deliver status changed',
        ]);
    }
}
