<?php

use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\CoustomerController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\VideoSliderController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\BannerDownController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\B2bController;
use App\Http\Controllers\DistributerController;
use App\Http\Controllers\AmazonController;

// analytics
use App\Http\Controllers\AnalyticController;
Route::post('/track', [AnalyticController::class, 'track']);

// quick health check
Route::get('/ping', function () { return response()->json(['ok' => true]); });

// Shipping Calculation API
Route::post('/shipping/calculate', [App\Http\Controllers\API\ShippingController::class, 'calculateShipping']);
Route::post('/shipping/options', [App\Http\Controllers\API\ShippingController::class, 'getShippingOptions']);

// Distributor Points API
Route::get('/distributor-points/active', [App\Http\Controllers\DistributorPointController::class, 'getActivePoints']);

// API Keys for frontend
Route::get('/api-keys', [App\Http\Controllers\DistributorPointController::class, 'getApiKeys']);

Route::get('/customer/info2', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// customer
Route::post('/coustomer/register',[CoustomerController::class,'register']);
Route::post('/coustomer/login',[CoustomerController::class,'login']);
Route::middleware('coustomer.auth')->post('/coustomer/logout',[CoustomerController::class,'logout']);
Route::middleware('coustomer.auth')->get('/coustomer/user',[CoustomerController::class,'user']);
Route::post('/coustomer/profile/update/{id}',[CoustomerController::class,'update']);

//categories
Route::get('/categories',[CategoryController::class,'categories'])->middleware('cacheResponse');
Route::get('/all_categories',[CategoryController::class,'all_categories'])->middleware('cacheResponse');
Route::get('/all_subcategories',[CategoryController::class,'all_subcategories'])->middleware('cacheResponse');

// products
Route::get('/new_products',[ProductController::class,'new_products']);
Route::get('/new_products_light2', function () {
    $out = [];
    foreach (App\Models\Product::with(['rel_to_gal','inventories.size','inventories.color'])->latest()->take(4)->get() as $p) {
        $inventories = $p->inventories;
        $colors = $inventories->pluck('color')->unique()->filter()->values()->map(function($c){ return $c ? ['id'=>$c->id ?? null,'name'=>$c->name ?? null] : null; })->filter()->values();
        $sizes = $inventories->pluck('size')->unique()->filter()->values()->map(function($s){ return $s ? ['id'=>$s->id ?? null,'name'=>$s->size ?? null] : null; })->filter()->values();
        $out[] = [
            'id' => $p->id,
            'name' => $p->product_name,
            'colors' => $colors,
            'sizes' => $sizes,
            'inventories_count' => $inventories->count(),
        ];
    }
    return response()->json($out);
});
// lightweight test route returning minimal product data
Route::get('/new_products_light', function () {
    return response()->json(App\Models\Product::latest()->take(4)->get(['id','product_name']));
});
Route::get('/all_products',[ProductController::class,'getAllProducts']);
Route::get('/search/products',[ProductController::class,'searchProducts']);
Route::post('/scerch',[ProductController::class,'scerch']);
Route::get('/products/detailes/{id}',[ProductController::class,'products_detailes']);
Route::post('/products/update/{id}',[ProductController::class,'update']);

// Cart
Route::post('add/cart/',[CartController::class,'add_cart']);
Route::get('cart/{id}',[CartController::class,'cart']);
Route::post('cart/update',[CartController::class,'cart_update']);
Route::delete('cart/delete/{id}',[CartController::class,'delete_cart_item']);


// checkout
Route::post('apply/coupon',[CheckoutController::class,'apply_coupon']);
Route::post('checkout',[CheckoutController::class,'checkout']);

// QR Payment submission (API route to avoid CORS issues)
use App\Http\Controllers\QRPaymentController;
Route::post('qr-payment/submit', [QRPaymentController::class, 'store']);

// order
Route::get('/myorder/{id}',[OrderController::class,'myorder']);
Route::get('/invoice/{id}',[OrderController::class,'invoice']);
Route::get('/orders/{id}',[OrderController::class,'orders']);
Route::get('/order-details/{order_id}',[OrderController::class,'orderDetails']);

// forgot password
Route::post('/forgot/password',[CoustomerController::class,'forgot_password']);
Route::post('/password/reset',[CoustomerController::class,'reset_password']);

// Google authentication
Route::post('/coustomer/google-auth',[CoustomerController::class,'google_auth']);


// color
Route::get('color',[ProductController::class,'color']);

// sliders
Route::get('/sliders',[SliderController::class,'sliders'])->middleware('cacheResponse');

// videoSlider
Route::get('/videos', [VideoSliderController::class, 'videos'])->middleware('cacheResponse')->name('videos');

// The frontend will post messages to this route
Route::post('/chat', [ChatController::class, 'handleChat'])->middleware('cors');
Route::get('/chat/context', [ChatController::class, 'getChatContext'])->middleware('cors');

// banner down
Route::get('/banner/down',[BannerDownController::class, 'get_banner_down']);

// reviews
Route::apiResource('reviews', ReviewController::class);

Route::get('/brands', [BrandController::class, 'index']);

// ERP API Routes
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductInventoryController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;

// Dashboard APIs
Route::get('/dashboard/kpis', [DashboardController::class, 'getKPIs']);
Route::get('/dashboard/recent-movements', [DashboardController::class, 'getRecentMovements']);
Route::get('/dashboard/top-products', [DashboardController::class, 'getTopProducts']);

// Inventory APIs
Route::get('/inventory/stock-status', [ProductInventoryController::class, 'getStockStatus']);
Route::get('/inventory/stock-details/{product_id}', [ProductInventoryController::class, 'getStockDetails']);
Route::get('/inventory/movements', [ProductInventoryController::class, 'getMovements']);
Route::get('/inventory/low-stock', [ProductInventoryController::class, 'getLowStock']);
Route::get('/inventory/expiry-alerts', [ProductInventoryController::class, 'getExpiryAlerts']);

// Purchase APIs
Route::get('/purchases/orders', [PurchaseOrderController::class, 'index']);
Route::get('/purchases/orders/{id}', [PurchaseOrderController::class, 'show']);
Route::post('/purchases/orders', [PurchaseOrderController::class, 'store']);
Route::put('/purchases/orders/{id}', [PurchaseOrderController::class, 'update']);
Route::delete('/purchases/orders/{id}', [PurchaseOrderController::class, 'destroy']);
Route::post('/purchases/orders/{id}/receive', [PurchaseOrderController::class, 'receive']);

// Supplier APIs
Route::get('/suppliers', [SupplierController::class, 'index']);
Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
Route::post('/suppliers', [SupplierController::class, 'store']);
Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);

// Accounting APIs
Route::get('/accounting/ledger', [AccountingController::class, 'getLedger']);
Route::get('/accounting/pnl', [AccountingController::class, 'getPNL']);
Route::get('/accounting/sales-report', [AccountingController::class, 'getSalesReport']);
Route::get('/accounting/inventory-valuation', [AccountingController::class, 'getInventoryValuation']);

// Reports APIs
Route::get('/reports/sales-analytics', [ReportController::class, 'salesAnalytics']);
Route::get('/reports/inventory-report', [ReportController::class, 'inventoryReport']);
// Return APIs
Route::get('/returnable-orders', [ReturnController::class, 'getReturnableOrders']);

Route::get('/new-arrivals', [ProductController::class, 'new_arrivals']);
Route::get('/all-products', [ProductController::class, 'all_products']);

// product by subcategory
Route::get('/subcategory-products/{id}', [ProductController::class, 'products_by_subcategory']);
Route::get('/subcategory/{id}', [ProductController::class, 'subcategory_with_products']);
// product by category
Route::get('/category-products/{id}', [ProductController::class, 'products_by_category']);
Route::get('/category/{id}', [ProductController::class, 'category_with_products']);
// product by brand
Route::get('/brand-products/{id}', [ProductController::class, 'products_by_brand']);

// Test routes for inventory debugging
use App\Models\ProductInventory;
use App\Models\Cart;

Route::get('/test-inventory', function () {
    // Get first product inventory record
    $inventory = ProductInventory::first();

    if (!$inventory) {
        return response()->json(['error' => 'No inventory records found']);
    }

    return response()->json([
        'message' => 'Current Inventory',
        'product_id' => $inventory->product_id,
        'current_quantity' => $inventory->quantity,
        'size_id' => $inventory->size_id,
        'color_id' => $inventory->color_id
    ]);
});

// Test products endpoint
Route::get('/test-products', function () {
    $products = \App\Models\Product::limit(5)->get();

    return response()->json([
        'message' => 'Products test',
        'count' => $products->count(),
        'products' => $products->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->product_name,
                'price' => $p->price,
                'has_inventory' => $p->inventories()->exists()
            ];
        })
    ]);
});

// Simple test for all_products logic
Route::get('/test-all-products', function () {
    try {
        $products = \App\Models\Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->limit(20)->get();
        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'message' => 'Query successful'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Static test route
Route::get('/static-test', function () {
    return response()->json([
        'message' => 'Static test works',
        'data' => [
            ['id' => 1, 'name' => 'Test Product 1'],
            ['id' => 2, 'name' => 'Test Product 2']
        ]
    ]);
});

// Test raw products without processing
Route::get('/test-raw-products', function () {
    try {
        $products = \App\Models\Product::limit(5)->get();
        return response()->json($products);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Reseller and B2B routes
Route::post('/reseller/register', [ResellerController::class, 'register']);
Route::post('/reseller/login', [ResellerController::class, 'login']);
Route::middleware('reseller.auth')->group(function () {
    Route::get('/reseller/dashboard', [ResellerController::class, 'dashboard']);
    Route::post('/reseller/payout', [ResellerController::class, 'requestPayout']);
    Route::get('/reseller/user', [ResellerController::class, 'user']);
    Route::post('/reseller/logout', [ResellerController::class, 'logout']);
});
Route::post('/wholesale/register', [B2bController::class, 'register']);
Route::post('/wholesale/login', [B2bController::class, 'login']);
Route::middleware('b2b.auth')->group(function () {
    Route::get('/b2b/dashboard', [B2bController::class, 'dashboard']);
    Route::get('/b2b/user', [B2bController::class, 'user']);
    Route::post('/b2b/logout', [B2bController::class, 'logout']);
});

// Distributer routes
Route::post('/distributer/register', [DistributerController::class, 'register']);
Route::post('/distributer/login', [DistributerController::class, 'login']);
Route::middleware('distributer.auth')->group(function () {
    Route::get('/distributer/dashboard', [DistributerController::class, 'dashboard']);
    Route::get('/distributer/user', [DistributerController::class, 'user']);
    Route::post('/distributer/logout', [DistributerController::class, 'logout']);
});

// Amazon routes
Route::post('/amazon/register', [AmazonController::class, 'register']);
Route::post('/amazon/login', [AmazonController::class, 'login']);
// Backwards-compatible endpoints (some frontend code may call these)
Route::post('/amazon-register', [AmazonController::class, 'register']);
Route::post('/amazon-login', [AmazonController::class, 'login']);
Route::middleware('amazon.auth')->group(function () {
    Route::get('/amazon/dashboard', [AmazonController::class, 'dashboard']);
    Route::get('/amazon/user', [AmazonController::class, 'user']);
    Route::post('/amazon/payment-update', [AmazonController::class, 'paymentUpdate']);
    Route::post('/amazon/logout', [AmazonController::class, 'logout']);
});

// Distributor Points API
Route::get('/distributor-points/active', [App\Http\Controllers\DistributorPointController::class, 'getActivePoints']);

Route::post('/test-inventory-deduct', function () {
    // Get first product inventory record
    $inventory = ProductInventory::first();
    
    if (!$inventory) {
        return response()->json(['error' => 'No inventory records found']);
    }
    
    $originalQuantity = $inventory->quantity;
    $deductQuantity = 1;
    
    // Update inventory
    $newQuantity = max(0, $originalQuantity - $deductQuantity);
    $inventory->update(['quantity' => $newQuantity]);
    
    return response()->json([
        'message' => 'Inventory Updated Successfully',
        'product_id' => $inventory->product_id,
        'original_quantity' => $originalQuantity,
        'deducted_quantity' => $deductQuantity,
        'new_quantity' => $newQuantity
    ]);
});

Route::get('/test-carts', function () {
    $carts = Cart::with('rel_to_product')->limit(5)->get();
    
    return response()->json([
        'message' => 'Cart Items',
        'count' => $carts->count(),
        'items' => $carts->map(function($cart) {
            return [
                'id' => $cart->id,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'size_id' => $cart->size_id,
                'color_id' => $cart->color_id
            ];
        })
    ]);
});

// Messages API routes
use App\Http\Controllers\MessageController;
Route::post('/messages', [MessageController::class, 'store']);
Route::get('/messages/unread', [MessageController::class, 'unread']);
Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
Route::post('/messages/mark-all-read', [MessageController::class, 'markAllAsRead']);
