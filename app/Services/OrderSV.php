<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use App\Services\BaseService;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderSV extends BaseService
{
    public function getQuery()
    {
        return Order::query();
    }

    public function getAllOrders($params)
    {
        $query = $this->getQuery();

        return $this->getAll($query, $params);
    }
    /**
     * Create a new Order with associated OrderItems.
     *
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function createOrder(array $data)
    {
        DB::beginTransaction();
            try {
                // 1. Create Order
                $order = Order::create([
                    'customer_id' => $data['customer_id'],
                    'order_status' => $data['order_status'],
                    'total_amount' => 0, // Temporary
                ]);

                // 2. Process Items (directly from product/quantity)
                $totalAmount = 0;
                foreach ($data['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    if ($product->stock_qty < $item['quantity']) {
                        throw new \Exception("Not enough stock for product: {$product->product_name}");
                    }
                    
                    $subtotal = $product->price * $item['quantity'];

                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $product->product_id,
                        'quantity' => $item['quantity'],
                        'subtotal' => $subtotal,
                    ]);

                    $totalAmount += $subtotal;
                    $product->stock_qty -= $item['quantity'];
                    $product->save();
                }

                // 3. Update Order Total
                $order->update(['total_amount' => $totalAmount]);
                DB::commit();
                return $order;

            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception("Order creation failed: " . $e->getMessage());
            }
    }

    public function getOrdersByCustomerId($id)
    {
        $query =  $this->getQuery();
        $order = $query->where('user_id', $id)->first();
        return $order;
    }

    
    public function getOrderById($id)
    {
        $query =  $this->getQuery();
        $order = $query->where('order_id', $id)->first();
        return $order;
    }


    public function updateOrder($data, $id){
       try {
            $query = $this->getQuery();
            $order = $query->where('Order_id', $id)->first();
            if (!$order) {
                throw new \Exception('Order not found', 404);
            }
           $order->update($data);
            return $order;
        } catch (\Exception $e) {
            throw new \Exception('Error updating Order: ' . $e->getMessage(), 500);
        }
    }
 
}
