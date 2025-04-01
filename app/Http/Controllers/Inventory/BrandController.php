<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\BrandRequest;
use App\Services\Inventory\Brand\BrandDeleteService;
use App\Services\Inventory\Brand\BrandEditService;
use App\Services\Inventory\Brand\BrandListService;
use App\Services\Inventory\Brand\BrandStoreService;
use App\Services\Inventory\Brand\BrandUpdateService;
use Illuminate\Http\Request;

class BrandController extends Controller
{

    public function index(BrandListService $brandListService, Request $request): mixed
    {
//        @todo: authorization
//        $this->authorize('viewAny', Brand::class);
        $result = $this->renderArrayOutput($brandListService, $request, null);

        return $result['brands'];
    }

    public function create()
    {
        //
    }

    public function store(BrandStoreService $brandStoreService, BrandRequest $request)
    {
        //        @todo: authorization
//        $this->authorize('create', Brand::class);

        return $this->renderJsonOutput($brandStoreService, $request, null);
    }

    public function show(int $id)
    {
        //
    }

    public function edit(int $id, BrandEditService $brandEditService, Request $request)
    {
        //        @todo: authorization
//        $this->authorize('update', Brand::find($id));

        return $this->renderJsonOutput($brandEditService, $request, ['id' => $id]);
    }

    public function update(BrandUpdateService $brandUpdateService, BrandRequest $request, int $id)
    {
        //        @todo: authorization
//        $this->authorize('update', Brand::find($id));

        return $this->renderJsonOutput($brandUpdateService, $request, ['id' => $id]);
    }

    public function destroy(BrandDeleteService $brandDeleteService, Request $request, int $id)
    {
        //        @todo: authorization
//        $this->authorize('delete', Brand::find($id));

        return $this->renderJsonOutput($brandDeleteService, $request, ['id' => $id]);
    }
}
