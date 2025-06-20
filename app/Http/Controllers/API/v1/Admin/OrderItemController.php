<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\API\v1\BaseAPI;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOrderItemRequest;
use App\Http\Requests\UpdateOrderItemRequest;
use App\Services\OrderItemSV;
use App\Models\OrderItem; 

class OrderItemController extends BaseAPI
{
    /**
     * Display a listing of the resource.
     */
    protected $orderItemsService;
    public function __construct()
    {
        $this->orderItemsService = new OrderItemSV();
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
        $orderItems = $this->orderItemsService->getAllOrderItems($params);
        return $this->successResponse($orderItems, 'OrderItems retrieved successfully');
    }

    public function getOrderItemById($id)
    {
        try {
            $orderItem = $this->orderItemsService->getOrderItemById($id);
            if (!$orderItem) {
                return $this->errorResponse('OrderItem not found', 404);
            }
            return $this->successResponse($orderItem, 'OrderItem retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
    */
    public function store(StoreOrderItemRequest $request)
    {
        $validated = $request->validated();
        $orderId = $validated['order_id'];
        $items = $validated['items'];

        $orderItems = $this->orderItemsService->createOrderItems($orderId, $items);

        foreach ($orderItems as $item) {
                if (!$item->save()) {
                logger('Failed to save order item', $item->toArray());
            }
        }

        return $this->successResponse($orderItems, 'OrderItems created successfully', 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(OrderItem $orderItem)
    {
        return response()->json($orderItem->load(['category', 'brand']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateOrderItem(UpdateOrderItemRequest $request, $id)
    {
        try {
        $params = $request->validated();
        $updatedOrderItem = $this->orderItemsService->updateOrderItem($params, $id);
        return $this->successResponse($updatedOrderItem, 'OrderItem updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderItem $OrderItem)
    {
        $OrderItem->delete();
        return response()->json(['message' => 'OrderItem deleted successfully'], 204);
    }
}
