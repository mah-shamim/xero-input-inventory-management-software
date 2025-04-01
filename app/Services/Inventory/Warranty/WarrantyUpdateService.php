<?php
/**
 * Created by PhpStorm.
 * User: MDIT-Raz
 * Date: 9/11/2019
 * Time: 6:03 PM
 */

namespace App\Services\Inventory\Warranty;


use App\Models\Inventory\Warranty;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class WarrantyUpdateService implements ActionIntf
{
    public function executePreCondition(Request $request, $params): array
    {
        $result = ['type' => 'error', 'message' => ''];
        $warranty = Warranty::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        if (! $warranty) {
            $result['message'] = 'Selected Warranty does not exists anymore.Please refresh and try again';

            return $result;
        }
        $result['warranty'] = $warranty;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $warranty = $array['warranty'];
        $warranty->update($request->all());

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Warranty has been updated successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}
