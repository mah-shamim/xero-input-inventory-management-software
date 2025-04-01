<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Services\Inventory\Import\ImportCreateService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function store(ImportCreateService $importCreateService, Request $request)
    {

        return $this->renderJsonOutput($importCreateService, $request, null);
    }
}
