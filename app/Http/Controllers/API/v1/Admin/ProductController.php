<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\API\v1\BaseAPI;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProducRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductSV;
use App\Models\Product;
use Illuminate\Container\Attributes\DB;

class ProductController extends BaseAPI
{
    /**
     * Display a listing of the resource.
     */
    protected $productsService;
    public function __construct()
    {
        $this->productsService = new ProductSV();
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
        $products = $this->productsService->getAllProducts($params);
        return $this->successResponse($products, 'products retrieved successfully');
    }

    public function getProductById($id)
    {
        try {
            $product = $this->productsService->getProductById($id);
            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }
            return $this->successResponse($product, 'Product retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProducRequest $request)
    {
        // $data = $request->validated();
        // $data['price'] = (float) $data['price'];
        // $product = $this->productsService->createProduct($data);
        // return $this->successResponse($product, 'Product created successfully', 201);
        try {
            $params = $request->validated();
            $params['status'] = isset($params['status']) ? $params['status'] : 1; // Default status to 1 if not provided
            $createdProduct = $this->productsService->createProduct($params);
            return $this->successResponse($createdProduct, 'Product created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json($product->load(['category', 'brand']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProduct(UpdateProductRequest $request, $id)
    {
        try {
        $params = $request->validated();
        $updatedProduct = $this->productsService->updateProduct($params, $id);
        return $this->successResponse($updatedProduct, 'Product updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function deactivateProduct($id)
    {
        try {
            
            $product = $this->productsService->deactivateProduct($id);
            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }
            return $this->successResponse($product, 'Product deactivate successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 204);
    }
}
