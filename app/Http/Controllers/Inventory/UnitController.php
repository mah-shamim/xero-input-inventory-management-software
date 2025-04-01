<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\UnitRequest;
use App\Models\Inventory\Unit;
use App\Services\Inventory\Unit\UnitCreateService;
use App\Services\Inventory\Unit\UnitDeleteService;
use App\Services\Inventory\Unit\UnitEditService;
use App\Services\Inventory\Unit\UnitListService;
use App\Services\Inventory\Unit\UnitUpdateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UnitListService $unitListService, Request $request)
    {
        $result = $this->renderArrayOutput($unitListService, $request, null);
        return $result['units'];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UnitCreateService $unitCreateService, UnitRequest $request)
    {
//        return $request->all();
        return $this->renderJsonOutput($unitCreateService, $request, null);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id, UnitEditService $unitEditService, Request $request)
    {
        return $this->renderJsonOutput($unitEditService, $request, ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Services\Inventory\Unit\UnitUpdateService $unitUpdateService
     * @param \App\Http\Requests\Inventory\UnitRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UnitUpdateService $unitUpdateService, UnitRequest $request, int $id)
    {
        return $this->renderJsonOutput($unitUpdateService, $request, ['id'=>$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param UnitDeleteService $unitDeleteService
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function destroy(UnitDeleteService $unitDeleteService, Request $request, int $id)
    {
        return $this->renderJsonOutput($unitDeleteService, $request, ['id' => $id]);
    }

    public function getUnits(Request $request)
    {
        $productId = $request->input('productId');
        return response()->json(['units' => Unit::leftJoin('product_unit AS pu', 'units.id', '=', 'pu.unit_id')->whereCompanyId(request()->input('company_id'))->where('pu.product_id', $productId)->orderBy('key', 'ASC')->get()])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
