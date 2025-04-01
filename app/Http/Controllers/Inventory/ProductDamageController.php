<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\ProductDamageRequest;
use App\Services\Inventory\ProductDamage\ProductDamageCreateService;
use App\Services\Inventory\ProductDamage\ProductDamageDeleteService;
use App\Services\Inventory\ProductDamage\ProductDamageEditService;
use App\Services\Inventory\ProductDamage\ProductDamageListService;
use App\Services\Inventory\ProductDamage\ProductDamageUpdateService;
use App\Services\Inventory\ProductDamage\ShowProductDamageService;
use Illuminate\Http\Request;

class ProductDamageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductDamageListService $productDamageListService, Request $request)
    {
        $result = $this->renderArrayOutput($productDamageListService, $request, null);
        return $result;
    }

    public function create(ShowProductDamageService $showProductDamageService, Request $request)
    {
        return $this->renderJsonOutput($showProductDamageService, $request, null);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductDamageCreateService $productDamageCreateService, ProductDamageRequest $request)
    {
        return $this->renderJsonOutput($productDamageCreateService, $request, null);
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
    public function edit(int $id, ProductDamageEditService $productDamageEditService, Request $request)
    {
        return $this->renderJsonOutput($productDamageEditService, $request, ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductDamageUpdateService $productDamageUpdateService, ProductDamageRequest $request, int $id)
    {
        return $this->renderJsonOutput($productDamageUpdateService, $request, ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductDamageDeleteService $productDamageDeleteService, Request $request, $id)
    {
        return $this->renderJsonOutput($productDamageDeleteService, $request, ['id' => $id]);
    }
}
