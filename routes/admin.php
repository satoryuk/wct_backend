<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\Admin\ProductController;
use App\Http\Controllers\API\v1\Admin\CategoryController;
use App\Http\Controllers\API\v1\Admin\BrandController;
use App\Http\Controllers\API\v1\Admin\OrderController;
use App\Http\Controllers\API\v1\Admin\PaymentController;
use App\Http\Controllers\API\v1\Admin\AnalyticsController;
use App\Http\Controllers\API\v1\Auth\AdminAuthController;
use App\Http\Controllers\API\v1\Admin\OrderItemController;
use App\Http\Controllers\API\v1\Admin\InventoryController;
use App\Http\Controllers\API\v1\Admin\UserController;
use App\Models\Inventory;

/*///////////////////////////////////////////
*
*           PUBLIC API
*
*/ //////////////////////////////////////////

Route::post('/register', [AdminAuthController::class,'register']);
Route::post('/login', [AdminAuthController::class, 'login']);

/*///////////////////////////////////////////
*
*           PRIVATE API
*
*   ///////////////////////////////////////*/

Route::group(['middleware' => 'auth:api', 'prefix' => 'auth/v1'], function ($router) {
    Route::post('/refresh-token', [AdminAuthController::class,'refreshToken']);
    Route::post('/logout', [AdminAuthController::class,'logout']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'getUserById']);
    Route::put('/users/{user_id}', [UserController::class, 'updateUser']);
    Route::patch('/users/{id}/deactivate', [UserController::class, 'deactivateUser']);

    // Product routes
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class,'getProductById']);
    Route::put('/products/{id}', [ProductController::class, 'updateProduct']);
    Route::patch('/products/{id}/deactivate', [ProductController::class, 'deactivateProduct']);
    // Route::delete('/products', [ProductController::class, 'deleteProduct']);
    // Route::delete('/products/{id}', [ProductController::class, 'deleteProduct']);

    // Category routes
    Route::post('/categories', [CategoryController::class,'store']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class,'getCategoryById']);
    Route::put('/categories/{id}', [CategoryController::class, 'updateCategory']);
    Route::patch('/categories/{id}/deactivate', [CategoryController::class, 'deactivateCategory']);

    // Brand routes
    Route::post('/brands', [BrandController::class,'store']);
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/{id}', [BrandController::class,'getBrandById']);
    Route::put('/brands/{id}', [BrandController::class, 'updateBrand']);
    Route::patch('/brands/{id}/deactivate', [BrandController::class, 'deactivateBrand']);

    // Inventory routes
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/inventory/{id}', [InventoryController::class, 'getById']);
    // Route::put('/inventory/{id}', [InventoryController::class, 'updateInventory']);

    // Order routes
    Route::post('/orders', [OrderController::class,'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class,'getOrderById']);
    Route::get('/orders/user/{id}', [OrderController::class, 'getOrdersByCustomerId']);
    Route::put('/orders/{id}', [OrderController::class, 'updateOrder']);
    Route::patch('/orders/{id}/deactivate', [OrderController::class, 'deactivateOrder']);

    // Order Item routes
    Route::post('/orderItems', [OrderItemController::class, 'store']);
    Route::get('/orderItems', [OrderItemController::class, 'index']);
    Route::get('/orderItems/{id}', [OrderItemController::class, 'getOrderItemById']);
    Route::put('/orderItems/{id}', [OrderItemController::class, 'updateOrderItem']);

    // Payment routes
    Route::post('/payments', [PaymentController::class,'store']);
    Route::get('/payments', [PaymentController::class, 'getAllPayments']);
    Route::get('/payments/{id}', [PaymentController::class,'getPaymentById']);
    Route::put('/payments/{id}', [PaymentController::class, 'updatePayment']);

    // Analytics routes
    Route::prefix('analytics')->group(function () {
        Route::get('top-selling-products', [AnalyticsController::class, 'topSellingProducts']);
        Route::get('payment-statistics', [AnalyticsController::class, 'paymentStatistics']);
        Route::get('pending-payments', [AnalyticsController::class, 'pendingPayments']);
        Route::get('high-value-customers', [AnalyticsController::class, 'highValueCustomers']);
        Route::get('inactive-customers', [AnalyticsController::class, 'inactiveCustomers']);
        Route::get('sales-by-category', [AnalyticsController::class,'salesByCategory']);
        Route::get('sales-by-brand', [AnalyticsController::class,'salesByBrand']);
        Route::get('never-ordered-products', [AnalyticsController::class, 'neverOrderedProducts']);
        Route::get('abandoned-carts', [AnalyticsController::class, 'abandonedCarts']);
        Route::get('check-order-items-consistency', [AnalyticsController::class, 'checkOrderItemsConsistency']);
        Route::get('duplicate-emails', [AnalyticsController::class, 'duplicateEmails']);
    });
});