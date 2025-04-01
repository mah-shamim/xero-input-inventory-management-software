<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\ProductUnitRequest;
use App\Models\Inventory\Product;
use App\Models\Inventory\Unit;
use App\Services\Inventory\ProductUnit\ProductUnitCreateService;
use App\Services\Inventory\ProductUnit\ProductUnitDeleteServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
//        @todo
//        $this->checkAuthorization('productUnit', 'index');
        return response()->json([
            'products' => Product::productList()->whereCompanyId($request->input('company_id'))->get(),
            'units' => Unit::select('id', 'key')
                ->with('conversions')->whereCompanyId($request->input('company_id'))
                ->orderBy('key', 'ASC')->get()
                ->map(function ($unit) {
                    $data['id'] = $unit->id;
                    $data['key'] = $unit->key;
                    $data['weight'] = null;
                    $data['conversions'] = $unit->conversions;

                    return $data;
                }),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }


    public function store(ProductUnitCreateService $productUnitCreateService, ProductUnitRequest $request)
    {
        return $this->renderJsonOutput($productUnitCreateService, $request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * @param int $id
     * @param \App\Services\Inventory\ProductUnit\ProductUnitDeleteServices $productUnitDeleteServices
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $id, ProductUnitDeleteServices $productUnitDeleteServices)
    {
        return $this->renderJsonOutput($productUnitDeleteServices, request(), $id);
    }

    public function getProductUnits(){
        $data = DB::table('product_unit as pu')
            ->leftJoin('products as p', 'pu.product_id','=','p.id')
            ->leftJoin('units as u','pu.unit_id','=','u.id')
            ->select('p.id','p.name', (DB::raw("GROUP_CONCAT(u.key SEPARATOR ' | ') AS unit_name")))
            ->where('p.company_id',compid())
            ->orderBy('p.id',"DESC")
            ->groupBy('p.id');

        if (request()->query('search')) {
            $data->where('name', 'like', '%' . request()->query('search') . '%');
        }

        return $data->paginate(getResultPerPage());
    }
}
