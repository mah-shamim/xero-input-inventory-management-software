<?php
/**
 * Created by PhpStorm.
 * User: mdit
 * Date: 12/12/2017
 * Time: 10:40 AM
 */

namespace App\Services\Inventory\Category;


use App\Models\Inventory\Category;
use App\Services\ActionIntf;
use Illuminate\Http\Request;

class CategoryCreateService implements ActionIntf
{

    public function executePreCondition(Request $request, $params): array
    {
        return ['type' => 'success', 'message' => ''];
    }

    public function execute($array, Request $request)
    {
        if (! $request->input('parent_id')) {
            $request->merge(['parent_id' => 0]);
        }

        $array['category'] = Category::create($request->all());

        return $array;
    }

    public function executePostCondition($array, Request $request)
    {
        $array['categories'] = Category::categoryList()->get();

        return $array;
    }

    public function buildSuccess($array)
    {
        $array['message'] = 'Category has been created successfully';

        return $array;
    }

    public function buildFailure($array)
    {
        return $array;
    }
}