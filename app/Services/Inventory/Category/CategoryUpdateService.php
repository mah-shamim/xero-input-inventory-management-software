<?php
/**
 * Created by PhpStorm.
 * User: MDIT
 * Date: 21-Apr-19
 * Time: 1:25 PM
 */

namespace App\Services\Inventory\Category;

use App\Models\Inventory\Category;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class CategoryUpdateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params)
    {
        $result = ['type' => 'error', 'message' => ''];
        $category = Category::whereId($params['id'])->whereCompanyId($request->input('company_id'))->first();
        if (! $category) {
            $result['message'] = 'Selected Category does not exists anymore.Please refresh and try again';

            return $result;
        }
        $result['category'] = $category;
        $result['type'] = 'success';

        return $result;
    }

    public function execute($array, Request $request)
    {
        $category = $array['category'];
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->type = $request->input('type');
        $category->parent_id = $request->input('parent_id');
        if (! $request->input('parent_id')) {
            $category->parent_id = 0;
        }
        $category->save();

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Category has been updated successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}