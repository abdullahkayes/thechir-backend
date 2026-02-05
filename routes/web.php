<?php

use App\Http\Controllers\BackendController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FrontendController;
// use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductInventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RollController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\PaypalPaymentController;
use App\Http\Controllers\API\VideoSliderController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\BannerDownController;
use App\Http\Controllers\AnalyticController;
use App\Http\Controllers\BusinessPlanController;
use App\Http\Controllers\Backend\BrandsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminResellerController;
use App\Http\Controllers\ApplePayPaymentController;
use App\Http\Controllers\AmazonController;
use App\Http\Controllers\QRPaymentController;

Route::get('/', function () {
    return view('welcome');
});

// excel report download
Route::get('/analytics/export', [AnalyticController::class, 'export'])->name('analytics.export');
// Accounts view
Route::get('/accounts', [ProductController::class, 'accounts'])->name('accounts');
Route::get('/show/seo', [ProductController::class, 'show_seo'])->name('seo');
Route::get('/seo/dashboard', [ProductController::class, 'seo_dashboard'])->name('seo.dashboard');


// Brand Routes for backend admin
Route::get('/brand/create', [BrandsController::class, 'create'])->name('brand.create');
Route::get('/brand/list', [BrandsController::class, 'index'])->name('brand.index');
Route::post('/brand/store', [BrandsController::class, 'store'])->name('brand.store');
Route::post('/brand/delete/{id}', [BrandsController::class, 'delete'])->name('brand.delete');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// analytic
Route::get('/analytic',[AnalyticController::class,'index'])->name('analytic');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// users
Route::get('/users',[BackendController::class,'users'])->name('users');
Route::get('/users/delete{id}',[BackendController::class,'users_delete'])->name('users.delete');
Route::get('/user/edit',[BackendController::class,'user_edit'])->name('user.edit');
Route::post('/user/update',[BackendController::class,'user_update'])->name('user.update');
Route::post('/user/update/photo',[BackendController::class,'user_photo'])->name('user.photo');
Route::post('/user/password',[BackendController::class,'user_password'])->name('user.password');

// Category
Route::get('/category',[CategoryController::class,'category'])->name('category');
Route::get('/category/delete{id}',[CategoryController::class,'category_delete'])->name('category.delete');
Route::post('/category/add',[CategoryController::class,'category_add'])->name('category.add');
Route::post('/category/checked/delete',[CategoryController::class,'category_checked_delete'])->name('category.checked.delete');
Route::get('/category/trash',[CategoryController::class,'category_trash'])->name('category.trash');
Route::post('/category/trash/checked',[CategoryController::class,'category_trash_checked'])->name('category.trash.checked');
Route::get('/trash/delete{id}',[CategoryController::class,'trash_delete'])->name('trash.delete');
Route::get('/trash/restore{id}',[CategoryController::class,'trash_restore'])->name('trash.restore');

// Subcategory
Route::get('/subcategory',[SubcategoryController::class,'subcategory'])->name('subcategory');
Route::post('/subcategory/add',[SubcategoryController::class,'subcategory_add'])->name('subcategory.add');
Route::get('/subcategory/delete{id}',[SubcategoryController::class,'subcategory_delete'])->name('subcategory.delete');
Route::get('/subcategory/trash',[SubcategoryController::class,'subcategory_trash'])->name('subcategory.trash');
Route::get('/subcategory/trash/delete{id}',[SubcategoryController::class,'subcategory_trash_delete'])->name('subcategory.trash.delete');
Route::get('/subcategory/trash/restore{id}',[SubcategoryController::class,'subcategory_trash_restore'])->name('subcategory.trash.restore');

// tag
Route::get('/tag',[TagController::class,'tag'])->name('tag');
Route::post('/tag/add',[TagController::class,'tag_add'])->name('tag.add');
Route::get('/tag/delete{id}',[TagController::class,'tag_delete'])->name('tag.delete');

// product
Route::get('/product',[ProductController::class,'product'])->name('product');
Route::post('/getSubcategory',[ProductController::class,'getSubcategory']);
Route::post('/product/store',[ProductController::class,'product_store'])->name('product.store');
Route::get('/product/list',[ProductController::class,'product_list'])->name('product.list');
Route::get('/product/delete{id}',[ProductController::class,'product_delete'])->name('product.delete');
Route::get('/product/trash',[ProductController::class,'product_trash'])->name('product.trash');
Route::get('/product/trash/delete{id}',[ProductController::class,'product_trash_delete'])->name('product.trash.delete');
Route::get('/product/trash/restore{id}',[ProductController::class,'product_trash_restore'])->name('product.trash.restore');
Route::get('/product/view{id}',[ProductController::class,'product_view'])->name('product.view');
Route::get('/product/edit/{id}',[ProductController::class,'product_edit'])->name('product.edit');
Route::post('/product/update/{id}',[ProductController::class,'product_update'])->name('product.update');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

// inventory
// Route::get('/inventory{id}',[InventoryController::class,'inventory'])->name('inventory');
// Route::get('/inventory/delete{id}',[InventoryController::class,'inventory_delete'])->name('inventory.delete');
// Route::post('/inventory/add{id}',[InventoryController::class,'inventory_store'])->name('inventory.store');

// Product Inventory
Route::get('product-inventory-view', [ProductInventoryController::class, 'index'])->name('product-inventory.index');
Route::get('product-inventory/search-suggestions', [ProductInventoryController::class, 'searchSuggestions'])->name('product-inventory.search-suggestions');
Route::get('product-inventory/product-suggestions', [ProductInventoryController::class, 'productSuggestions'])->name('product-inventory.product-suggestions');
Route::get('product-inventory/create', [ProductInventoryController::class, 'create'])->name('product-inventory.create');
Route::post('product-inventory/store', [ProductInventoryController::class, 'store'])->name('product-inventory.store');
Route::get('product-inventory/{inventory}/edit', [ProductInventoryController::class, 'edit'])->name('product-inventory.edit');
Route::put('product-inventory/{inventory}', [ProductInventoryController::class, 'update'])->name('product-inventory.update');
Route::delete('product-inventory/{inventory}', [ProductInventoryController::class, 'destroy'])->name('product-inventory.destroy');

Route::get('/color',[ProductInventoryController::class,'color'])->name('color');
Route::get('/color/delete{id}',[ProductInventoryController::class,'color_delete'])->name('color.delete');
Route::post('/color/add',[ProductInventoryController::class,'color_add'])->name('color.add');

Route::get('size',[ProductInventoryController::class,'size'])->name('size');
Route::get('size/delete{id}',[ProductInventoryController::class,'size_delete'])->name('size.delete');
Route::post('/size/add',[ProductInventoryController::class,'size_add'])->name('size.add');

// coupon
Route::get('/coupon',[CouponController::class,'coupon'])->name('coupon');
Route::post('/coupon/add',[CouponController::class,'coupon_add'])->name('coupon.add');
Route::get('/coupon/delete{id}',[CouponController::class,'coupon_delete'])->name('coupon.delete');


// order
Route::get('/order',[OrderController::class,'order'])->name('order');
Route::post('/status/change/{id}',[OrderController::class,'status_change'])->name('status.change');
Route::get('/invoice/{order_id}',[OrderController::class,'invoice'])->name('invoice');
Route::get('/invoice/print/{order_id}',[OrderController::class,'invoice_print'])->name('invoice.print');


// stripe
Route::controller(StripePaymentController::class)->group(function(){
    Route::get('stripe', 'stripe');
    Route::post('stripe/{order_id}', 'stripePost')->name('stripe.post');
    Route::get('order/success/{order_id}', 'orderSuccess')->name('order.success');
    Route::post('stripe/webhook', 'webhook')->name('stripe.webhook');
});

// paypal
Route::controller(PaypalPaymentController::class)->group(function(){
    Route::get('paypal', 'paypal');
    Route::post('paypal/{order_id}', 'paypalPost')->name('paypal.post');
});

// apple pay
Route::controller(ApplePayPaymentController::class)->group(function(){
    Route::get('apple-pay', 'applePay');
    Route::post('apple-pay/{order_id}', 'applePayPost')->name('apple-pay.post');
});


// roll manager
Route::get('/roll/manager',[RollController::class,'roll_manager'])->name('roll.manager');
Route::post('/permission/create',[RollController::class,'permission_create'])->name('permissiom.create');
Route::post('/roll/create',[RollController::class,'roll_create'])->name('roll.create');
Route::post('/asign/roll',[RollController::class,'asign_roll'])->name('asign.roll');
Route::get('/roll/remove/{id}',[RollController::class,'roll_remove'])->name('roll.remove');


// banner slider 
Route::get('/slider',[SliderController::class, 'slider'])->name('slider');
Route::post('/slider/add',[SliderController::class, 'slider_add'])->name('slider.add');
Route::match(['get', 'post'], '/slider/delete/{id}', [SliderController::class, 'slider_delete'])->name('slider.delete');

// Bannrr down slider
Route::get('/banner/down',[BannerDownController::class, 'banner_down'])->name('banner.down');
Route::post('/banner/store',[BannerDownController::class, 'banner_down_store'])->name('banner.down.store');
Route::post('/banner/delete/{id}',[BannerDownController::class, 'banner_down_delete'])->name('banner.down.delete');
Route::post('/banner/trash/restore/{id}',[BannerDownController::class, 'banner_down_restore'])->name('banner.down.restore');
Route::post('/banner/trash/delete/{id}',[BannerDownController::class, 'banner_down_force_delete'])->name('banner.down.force.delete');
Route::get('/banner/trash/',[BannerDownController::class, 'banner_down_trash'])->name('banner.down.trash');

// video slider
Route::get('/video/slider/',[VideoSliderController::class, 'videoSlider'])->name('videoSlider');
Route::post('/video/add/',[VideoSliderController::class, 'videoSlider_add'])->name('videoSlider.add');
Route::get('/video/edit/{id}',[VideoSliderController::class, 'videoSlider_edit'])->name('videoSlider.edit');
Route::post('/video/update/{id}',[VideoSliderController::class, 'videoSlider_update'])->name('videoSlider.update');
Route::post('/video/delete/{id}',[VideoSliderController::class, 'videoSlider_delete'])->name('videoSlider.delete');
Route::post('/video/trash/restore/{id}',[VideoSliderController::class, 'videoSlider_trash_restore'])->name('videoSlider.trash.restore');
Route::post('/video/trash/delete/{id}',[VideoSliderController::class, 'videoSlider_trash_delete'])->name('videoSlider.trash.delete');
Route::get('/video/trash/',[VideoSliderController::class, 'videoSlider_trash'])->name('videoSlider.trash');

// Business Plans
Route::resource('business-plans', BusinessPlanController::class);
Route::get('/video/trash/',[VideoSliderController::class, 'videoSlider_trash'])->name('videoSlider.trash');

// ERP Routes
Route::get('/inventory/index', [ProductInventoryController::class, 'erpIndex'])->name('inventory.index');
Route::post('/inventory/adjust/{productId}', [ProductInventoryController::class, 'adjustStock'])->name('inventory.adjust');

// Purchase Orders CRUD Routes
Route::get('/purchase-orders/index', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
Route::get('/purchase-orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
Route::put('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
Route::get('/suppliers/index', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
Route::get('/accounting/index', [AccountingController::class, 'index'])->name('accounting.index');
Route::get('/returns/index', [ReturnController::class, 'index'])->name('returns.index');
Route::get('/returns/create/{order}', [ReturnController::class, 'create'])->name('returns.create');
Route::post('/returns/process/{order}', [ReturnController::class, 'processReturn'])->name('returns.process');
Route::get('/returns/show/{order}', [ReturnController::class, 'show'])->name('returns.show');
Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');

// Reviews CRUD Routes (Admin)
Route::resource('reviews', ReviewController::class)->names([
    'index' => 'admin.reviews.index',
    'create' => 'admin.reviews.create',
    'store' => 'admin.reviews.store',
    'show' => 'admin.reviews.show',
    'edit' => 'admin.reviews.edit',
    'update' => 'admin.reviews.update',
    'destroy' => 'admin.reviews.destroy',
]);

// Return API Route (web version for easier access)
Route::get('/returnable-orders', [ReturnController::class, 'getReturnableOrders'])->name('returnable.orders');

// QR Payment Routes - Public route for customers to submit payments (outside auth group)
Route::match(['get', 'post', 'options'], '/qr-payment/submit', [QRPaymentController::class, 'store'])->name('qr-payment.submit');
Route::get('/qr-payment/statistics', [QRPaymentController::class, 'statistics'])->name('qr-payment.statistics');

// Reseller and B2B Admin Routes
Route::middleware('auth')->group(function () {
    Route::get('/admin/reseller-dashboard', [AdminResellerController::class, 'index'])->name('admin.reseller.dashboard');
    Route::post('/admin/approve-b2b/{id}', [AdminResellerController::class, 'approveB2b'])->name('admin.approve.b2b');
    Route::post('/admin/reject-b2b/{id}', [AdminResellerController::class, 'rejectB2b'])->name('admin.reject.b2b');
    Route::post('/admin/approve-reseller/{id}', [AdminResellerController::class, 'approveReseller'])->name('admin.approve.reseller');
    Route::post('/admin/reject-reseller/{id}', [AdminResellerController::class, 'rejectReseller'])->name('admin.reject.reseller');
    Route::post('/admin/approve-distributer/{id}', [AdminResellerController::class, 'approveDistributer'])->name('admin.approve.distributer');
    Route::post('/admin/reject-distributer/{id}', [AdminResellerController::class, 'rejectDistributer'])->name('admin.reject.distributer');
    Route::post('/admin/approve-payout/{id}', [AdminResellerController::class, 'approvePayoutRequest'])->name('admin.approve.payout');
    Route::post('/admin/reject-payout/{id}', [AdminResellerController::class, 'rejectPayoutRequest'])->name('admin.reject.payout');
    Route::post('/admin/approve-amazon/{id}', [AdminResellerController::class, 'approveAmazon'])->name('admin.approve.amazon');
    Route::post('/admin/reject-amazon/{id}', [AdminResellerController::class, 'rejectAmazon'])->name('admin.reject.amazon');
    Route::post('/admin/approve-payment-update/{id}', [AdminResellerController::class, 'approvePaymentUpdate'])->name('admin.approve.payment.update');
    Route::get('/admin/download-invoice/{orderId}', [AdminResellerController::class, 'downloadInvoice'])->name('admin.download.invoice');
    Route::get('/admin/view-resale-certificate/{b2bId}', [AdminResellerController::class, 'viewResaleCertificate'])->name('admin.view.resale.certificate');
    Route::get('/admin/download-resale-certificate/{b2bId}', [AdminResellerController::class, 'downloadResaleCertificate'])->name('admin.download.resale.certificate');
    Route::get('/admin/export-commission-report', [AdminResellerController::class, 'exportCommissionReport'])->name('admin.export.commission.report');
    Route::get('/admin/export-buy-report', [AdminResellerController::class, 'exportBuyReport'])->name('admin.export.buy.report');

    // Distributor Points Management
    Route::get('/admin/distributor-points', [App\Http\Controllers\DistributorPointController::class, 'index'])->name('admin.distributor-points.index');
    Route::get('/admin/distributor-points/{distributorPoint}/edit', [App\Http\Controllers\DistributorPointController::class, 'edit'])->name('admin.distributor-points.edit');
    Route::post('/admin/distributor-points', [App\Http\Controllers\DistributorPointController::class, 'store'])->name('admin.distributor-points.store');
    Route::put('/admin/distributor-points/{distributorPoint}', [App\Http\Controllers\DistributorPointController::class, 'update'])->name('admin.distributor-points.update');
    Route::delete('/admin/distributor-points/{distributorPoint}', [App\Http\Controllers\DistributorPointController::class, 'destroy'])->name('admin.distributor-points.destroy');
    Route::post('/admin/update-api-keys', [App\Http\Controllers\DistributorPointController::class, 'updateApiKeys'])->name('admin.update-api-keys');


    // Admin QR Payment Routes (require authentication)
    Route::get('/admin/qr-payments', [QRPaymentController::class, 'index'])->name('admin.qr-payments.index');
    Route::post('/admin/qr-payment/{id}/approve', [QRPaymentController::class, 'approve'])->name('admin.qr-payment.approve');
    Route::post('/admin/qr-payment/{id}/reject', [QRPaymentController::class, 'reject'])->name('admin.qr-payment.reject');
    Route::delete('/admin/qr-payment/{id}', [QRPaymentController::class, 'destroy'])->name('admin.qr-payment.destroy');
    Route::get('/admin/qr-payment/{id}', [QRPaymentController::class, 'show'])->name('admin.qr-payment.show');
});

// Customer Routes
Route::middleware('auth')->group(function () {
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
});

// Message Routes (Admin)
Route::middleware('auth')->group(function () {
    Route::resource('admin/messages', App\Http\Controllers\MessageController::class)->names([
        'index' => 'admin.messages.index',
        'store' => 'admin.messages.store',
        'show' => 'admin.messages.show',
        'update' => 'admin.messages.update',
        'destroy' => 'admin.messages.destroy',
    ]);
});

require __DIR__.'/auth.php';



