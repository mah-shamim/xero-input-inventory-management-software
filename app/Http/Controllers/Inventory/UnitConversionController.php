<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\UnitConversionRequest;
use App\Models\Inventory\UnitConversion;
use App\Services\Inventory\UnitConversion\ShowUnitConversionService;
use App\Services\Inventory\UnitConversion\UnitConversionConvertService;
use App\Services\Inventory\UnitConversion\UnitConversionCreateService;
use Illuminate\Http\Request;

class UnitConversionController extends Controller
{
    public function index()
    {
        $this->checkAuthorization('unitMapping', 'index');

        $query = UnitConversion::with('from_unit', 'to_unit')
            ->whereCompanyId(request()->input('company_id'));

        if (request()->input('sortOrder')) {

            $query->orderBy('id', 'DESC');
        }

        return $query->paginate(getResultPerPage());
    }

    public function create(ShowUnitConversionService $showUnitConversionService, Request $request)
    {
        $this->checkAuthorization('unitMapping', 'index');

        return $this->renderJsonOutput($showUnitConversionService, $request, null);
    }

    public function store(UnitConversionCreateService $unitConversionCreateService, UnitConversionRequest $request)
    {
        $this->checkAuthorization('unitMapping', 'index');

        return $this->renderJsonOutput($unitConversionCreateService, $request, null);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function convert(UnitConversionConvertService $unitConversionConvertService, Request $request, $fromUnitId, $toUnitId, $quantity)
    {
        return $this->renderJsonOutput($unitConversionConvertService, $request, ['from_unit_id' => $fromUnitId, 'to_unit_id' => $toUnitId, 'quantity' => $quantity]);
    }
}
