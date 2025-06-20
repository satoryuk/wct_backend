<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\API\v1\BaseAPI;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderSV;

class OrderController extends BaseAPI
{
    /**
     * Display a listing of the resource.
     */
    public $orderService;
    public function __construct()
    {
        $this->orderService = new OrderSV;
    }

    public function index()
    {
        $filters = [];
        if (request()->has('status')) {
            $filters['status'] = request()->query('status');
        }

        $params = [
            'filterBy' => $filters,
            'perPage' => request()->query('perPage', 10), // default to 10
        ];
        $orders = $this->orderService->getAllOrders($params);
        return $this->successResponse($orders, 'Orders retrieved successfully');   
    }

    public function getOrderById($order_id)
    {
        try {
            $orders = $this->orderService->getOrderById($order_id);
            return $this->successResponse($orders, 'Orders retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve orders: ' . $e->getMessage(), 500); 
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getOrdersByCustomerId($user_id)
    {
        try{
            $orders = $this->orderService->getOrdersByCustomerId($user_id);
            return $this->successResponse($orders, 'Orders retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve orders: ' . $e->getMessage(), 500); 
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $order = $this->orderService->createOrder($data);
        return $this->successResponse($order, 'Order created successfully!', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->json($order->load(['customer', 'orderItems', 'payment']));	
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());
        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully!']);
    }
}
