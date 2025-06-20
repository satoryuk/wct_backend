<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get top 5 best-selling products
     */
    public function topSellingProducts()
    {
        $products = Product::select(
                'products.product_id', 
                'products.product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
            )
            ->join('order_items', 'products.product_id', '=', 'order_items.product_id')
            ->groupBy('products.product_id', 'products.product_name')
            ->orderBy('total_quantity_sold', 'desc')
            ->limit(5)
            ->get();
            
        return response()->json($products);
    }
    
    /**
     * Get payment statistics by payment method
     */
    public function paymentStatistics()
    {
        $statistics = Payment::select(
                'payment_method',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->where('payment_status', 'completed')
            ->groupBy('payment_method')
            ->get();
            
        return response()->json($statistics);
    }
    
    /**
     * Get orders with pending payments
     */
    public function pendingPayments()
    {
        $pendingPayments = Order::select(
                'orders.order_id', 
                'orders.order_date', 
                'users.username', 
                'payments.amount', 
                'payments.payment_status'
            )
            ->join('users', 'orders.customer_id', '=', 'users.user_id')
            ->join('payments', 'orders.order_id', '=', 'payments.order_id')
            ->where('payments.payment_status', 'pending')
            ->get();
            
        return response()->json($pendingPayments);
    }
    
    /**
     * Get customers who have spent more than $1000
     */
    public function highValueCustomers()
    {
        $customers = User::select(
                'users.user_id', 
                'users.username', 
                'users.email',
                DB::raw('SUM(order_items.quantity * order_items.price) as total_spent')
            )
            ->join('orders', 'users.user_id', '=', 'orders.customer_id')
            ->join('order_items', 'orders.order_id', '=', 'order_items.order_id')
            ->groupBy('users.user_id', 'users.username', 'users.email')
            ->having('total_spent', '>', 1000)
            ->orderBy('total_spent', 'desc')
            ->get();
            
        return response()->json($customers);
    }
    
    /**
     * Get customers who haven't placed an order in the last 3 months
     */
    public function inactiveCustomers()
    {
        $customers = User::select(
                'users.user_id', 
                'users.username', 
                'users.email',
                DB::raw('MAX(orders.order_date) as last_order_date')
            )
            ->leftJoin('orders', 'users.user_id', '=', 'orders.customer_id')
            ->where('users.role', 'customer')
            ->groupBy('users.user_id', 'users.username', 'users.email')
            ->having(DB::raw('MAX(orders.order_date)'), '<', DB::raw('DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH)'))
            ->orHavingRaw('MAX(orders.order_date) IS NULL')
            ->get();
            
        return response()->json($customers);
    }
    
    /**
     * Get sales by category
     */
    public function salesByCategory()
    {
        $categorySales = Category::select(
                'categories.category_name',
                DB::raw('COUNT(DISTINCT orders.order_id) as order_count'),
                DB::raw('SUM(order_items.quantity) as items_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
            )
            ->join('products', 'categories.category_id', '=', 'products.category_id')
            ->join('order_items', 'products.product_id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->groupBy('categories.category_name')
            ->orderBy('total_sales', 'desc')
            ->get();
            
        return response()->json($categorySales);
    }
    
    /**
     * Get sales by brand
     */
    public function salesByBrand()
    {
        $brandSales = Brand::select(
                'brand.brand_name',
                DB::raw('COUNT(DISTINCT orders.order_id) as order_count'),
                DB::raw('SUM(order_items.quantity) as items_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
            )
            ->join('products', 'brand.brand_id', '=', 'products.brand_id')
            ->join('order_items', 'products.product_id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->groupBy('brand.brand_name')
            ->orderBy('total_sales', 'desc')
            ->get();
            
        return response()->json($brandSales);
    }
    
    /**
     * Find products that have never been ordered
     */
    public function neverOrderedProducts()
    {
        $products = Product::select('products.product_id', 'products.product_name', 'products.price')
            ->leftJoin('order_items', 'products.product_id', '=', 'order_items.product_id')
            ->whereNull('order_items.order_item_id')
            ->get();
            
        return response()->json($products);
    }
    
    /**
     * Find users who have abandoned carts (orders with status 'pending' older than 24 hours)
     */
    public function abandonedCarts()
    {
        $abandonedCarts = User::select(
                'users.user_id', 
                'users.username', 
                'users.email', 
                'orders.order_id', 
                'orders.order_date'
            )
            ->join('orders', 'users.user_id', '=', 'orders.customer_id')
            ->where('orders.order_status', 'pending')
            ->where('orders.order_date', '<', DB::raw('DATE_SUB(NOW(), INTERVAL 24 HOUR)'))
            ->get();
            
        return response()->json($abandonedCarts);
    }
    
    /**
     * Check database consistency - find order items without valid products
     */
    public function checkOrderItemsConsistency()
    {
        $invalidOrderItems = OrderItem::select('order_items.order_item_id', 'order_items.order_id', 'order_items.product_id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.product_id')
            ->whereNull('products.product_id')
            ->get();
            
        return response()->json($invalidOrderItems);
    }
    
    /**
     * Find duplicate user emails
     */
    public function duplicateEmails()
    {
        $duplicateEmails = User::select('email', DB::raw('COUNT(*) as count'))
            ->groupBy('email')
            ->having('count', '>', 1)
            ->get();
            
        return response()->json($duplicateEmails);
    }
}