<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\API\v1\BaseAPI;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriesRequest;
use App\Http\Requests\UpdateCategoriesRequest;
use App\Models\Category;
use App\Services\CategoriesSV;
use Illuminate\Http\Request;

class CategoryController extends BaseAPI
{
    /**
     * Display a listing of the resource.
     */
    public $categoryService;
    public function __construct()
    {
        $this->categoryService = new \App\Services\CategoriesSV();
    }
    public function index(Request $request)
    {
        $filters = [];

        if ($request->has('status')) {
            $filters['status'] = $request->query('status'); 
        }

        $params = [
            'filterBy' => $filters,
            'perPage' => $request->query('perPage', 10), // default to 10
        ];

        $category = $this->categoryService->getAllCategorys($params);
        return $this->successResponse($category, 'Category retrieved successfully');
    }

    public function getCategoryById($id)
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            if (!$category) {
                return $this->errorResponse('Category not found', 404);
            }
            return $this->successResponse($category, 'Category retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoriesRequest $request)
    {
        $data = $request->validated();
        $category = $this->categoryService->createCategory($data);
        return $this->successResponse($category, 'Category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateCategory(UpdateCategoriesRequest $request, $id)
    {
        try{
            $params = $request->validated();
            $category = $this->categoryService->updateCategory($params, $id);
            return $this->successResponse($category, 'Category updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());

        }
        $params = $request->validated();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully!', 204]);
    }
}
