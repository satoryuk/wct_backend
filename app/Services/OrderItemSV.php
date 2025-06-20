<?php

namespace App\Services;

use App\Models\OrderItem;
use Exception;
use App\Services\BaseService;
use App\Models\Product;
use App\Models\Order;

class OrderItemSV extends BaseService
{
    public function getQuery()
    {
        return OrderItem::with('product');
    }

    public function getAllOrderItems($params)
    {
        $query = $this->getQuery();

        return $this->getAll($query, $params);
    }

    /**
     * Store multiple products in OrderItems and return them
     */
    public function createOrderItems($orderId, array $items)
    {
        $orderItems = [];

        foreach ($items as $item) {
            if (!isset($item['product_id']) || !isset($item['quantity'])) {
                continue;
            }

            $product = Product::where('product_id', $item['product_id'])->firstOrFail();

            $quantity = $item['quantity'];
            $price = $product->price;
            $subtotal = $quantity * $price;

            $orderItems[] = new OrderItem([
                'order_id' => $orderId,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
            ]);
        }

        return $orderItems;
    }


    public function getOrderItemById($id)
    {
        $query =  $this->getQuery();
        $oderItem = $query->where('OrderItem_id', $id)->first();
        return $oderItem;
    }


    // public function updateOrderItem($data, $id){
    //    try {
    //         $query = $this->getQuery();
    //         $orderItem = $query->where('OrderItem_id', $id)->first();
    //         if (!$orderItem) {
    //             throw new \Exception('OrderItem not found', 404);
    //         }
    //        $orderItem->update($data);
    //         return $orderItem;
    //     } catch (\Exception $e) {
    //         throw new \Exception('Error updating OrderItem: ' . $e->getMessage(), 500);
    //     }
    // }
    
    public function updateOrderItem($data, $id)
    {
        $item = OrderItem::findOrFail($id);
        if (isset($data['quantity'])) {
            $product = $item->product;
            $data['subtotal'] = $product->price * $data['quantity']; // Recalculate
        }
        $item->update($data);
        
        // Update parent Order's total
        $order = $item->order;
        $order->update([
            'total_amount' => $order->orderItems()->sum('subtotal')
        ]);
        
        return $item;
    }
}
