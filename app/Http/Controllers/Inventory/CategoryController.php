<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\CategoryRequest;
use App\Models\Inventory\Category;
use App\Services\Inventory\Category\CategoryCreateService;
use App\Services\Inventory\Category\CategoryEditService;
use App\Services\Inventory\Category\CategoryUpdateService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $categoryCreateService;

    public function __construct(CategoryCreateService $categoryCreateService)
    {
        $this->categoryCreateService = $categoryCreateService;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {

        //        $this->authorize('viewAny', Category::class);
        $categories = request()->get('dropdown')
            ? Category::productCategoryList()->get()
            : Category::categoryListRaw();


        return response()->json($categories)->setEncodingOptions(JSON_NUMERIC_CHECK);
        //        return Category::categoryWithLabelAndValue();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request)
    {
//        $this->authorize('create', Category::class);

        return response()->json([
            'categories' => Category::whereCompanyId($request->input('company_id'))->get(),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CategoryRequest $request)
    {
//        $this->authorize('create', Category::class);

        return $this->renderJsonOutput($this->categoryCreateService, $request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        return response()->json(Category::with('parent_category')->whereId($id)->first())->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(int $id)
    {
//        $this->authorize('update', Category::find($id));

        return response()->json(Category::with('parent_category')->whereId($id)->first())->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return string
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CategoryUpdateService $categoryUpdateService, Request $request, int $id)
    {
//        $this->authorize('update', Category::find($id));

        return $this->renderJsonOutput($categoryUpdateService, $request, ['id'=>$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(int $id, Request $request)
    {
//        $this->authorize('delete', Category::find($id));
        $cat = Category::find($id);
        $asParent = Category::whereParentId($id)->count();
        if (!$asParent) {
            if (!$cat->products()->count()) {
                Category::whereId($id)->whereCompanyId($request->input('company_id'))->delete();

                return response()->json([
                    'type' => 'success',
                    'message' => 'Category Deleted Successfully',
                ]);
            } else {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Sorry you have already purchase with this category',
                ]);
            }
        } else {
            return response()->json([
                'type' => 'error',
                'message' => 'Sorry, You have already child category',
            ]);
        }
    }
}
