<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\API\v1\BaseAPI;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Services\BrandSV;
use Illuminate\Support\Facades\DB;

class BrandController extends BaseAPI
{
    /**
     * Display a listing of the resource.
     */
    public $brandService;
    public function __construct()
    {
        $this->brandService = new BrandSV();
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

        $brand = $this->brandService->getAllBrands($params);
        return $this->successResponse($brand, 'Brand retrieved successfully');
    }
    
    public function getBrandById($id)
    {
        try {
            $brand = $this->brandService->getBrandById($id);
            if (!$brand) {
                return $this->errorResponse('Brand not found', 404);
            }
            return $this->successResponse($brand, 'Brand retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        $data = $request->validated();
        $brand = $this->brandService->createbrand($data);   
        return $this->successResponse($brand, 'Brand created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return response()->json($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateBrand(UpdateBrandRequest $request, $id)
    {
        try {   
            $params = $request->validated();
            DB::beginTransaction();
            $brand = $this->brandService->updateBrand($params, $id);
            DB::commit();
            return $this->successResponse($brand, 'Brand updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
        $params = $request->validated();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->json(['message' => 'Brand deleted successfully!', 204]);
    }
}
