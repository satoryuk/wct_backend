<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Http\Controllers\API\v1\BaseAPI;
use App\Services\InventorySV;
use App\Models\Product;
class InventoryController extends BaseAPI
{
    /**
     * Display a listing of the resource.
     */
    protected $inventoryService;
    public function __construct()
    {
        $this->inventoryService = new InventorySV();
    }
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $perPage = $request->query('perPage', 10);

        $products = $query->select('product_id', 'product_name', 'stock_qty', 'status')->paginate($perPage);

        $products->getCollection()->transform(function ($item) {
            $item->stock_status = $item->stock_qty > 0 ? 'in stock' : 'out of stock';
            return $item;
        });

        return response()->json($products);
    }

    public function getById($id)
    {
        $product = Product::select('product_id', 'product_name', 'stock_qty', 'status')
            ->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->stock_status = $product->stock_qty > 0 ? 'in stock' : 'out of stock';

        return response()->json($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryRequest $request)
    {
        $inventory = Inventory::create($request->validated());
        return response()->json($inventory, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        return response()->json($inventory->load(['product']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $inventory->update($request->validated());
        return response()->json($inventory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return response()->json(['message' => 'Inventory record deleted successfully!', 204]);
    }
}
