<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/11/2017
 * Time: 2:56 PM
 */

namespace App\Services;

use Illuminate\Http\Request;

interface ActionIntf
{

    public function executePreCondition(Request $request, $params);

    public function execute($array, Request $request);

    public function executePostCondition($array, Request $request);

    public function buildSuccess($array);

    public function buildFailure($array);
}