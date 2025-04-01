<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\WarehouseRequest;
use App\Services\Inventory\Warehouse\WarehouseCreateService;
use App\Services\Inventory\Warehouse\WarehouseDeleteService;
use App\Services\Inventory\Warehouse\WarehouseEditService;
use App\Services\Inventory\Warehouse\WarehouseListService;
use App\Services\Inventory\Warehouse\WarehouseUpdateService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{

    protected $warehouseCreateService;

    public function __construct(WarehouseCreateService $warehouseCreateService)
    {
        $this->warehouseCreateService = $warehouseCreateService;

    }

    /**
     * Display a listing of the resource.
     *
     * @param WarehouseListService $warehouseListService
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(WarehouseListService $warehouseListService, Request $request)
    {
        $result= $this->renderArrayOutput($warehouseListService, $request, null);
        return $result['warehouses'];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * @param WarehouseRequest $warehouseRequest
     * @return string
     */
    public function store(WarehouseRequest $warehouseRequest)
    {
        return $this->renderJsonOutput($this->warehouseCreateService, $warehouseRequest, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @param WarehouseEditService $warehouseEditService
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function edit($id, WarehouseEditService $warehouseEditService, Request $request)
    {

        return $this->renderJsonOutput($warehouseEditService, $request, ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WarehouseUpdateService $warehouseUpdateService
     * @param WarehouseRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(WarehouseUpdateService $warehouseUpdateService, WarehouseRequest $request, int $id)
    {
        return $this->renderJsonOutput($warehouseUpdateService, $request, ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param WarehouseDeleteService $warehouseDeleteService
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(WarehouseDeleteService $warehouseDeleteService, Request $request, $id)
    {
        return $this->renderJsonOutput($warehouseDeleteService, $request, ['id' => $id]);
    }

}
