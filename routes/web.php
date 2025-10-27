<?php

use App\Http\Controllers\BackendController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrederController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RollController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripePaymentController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [FrontendController::class, 'dashboard'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
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
route::get('/trash/delete{id}',[CategoryController::class,'trash_delete'])->name('trash.delete');
route::get('/trash/restore{id}',[CategoryController::class,'trash_restore'])->name('trash.restore');

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

// inventory
Route::get('/inventory{id}',[InventoryController::class,'inventory'])->name('inventory');
Route::get('/inventory/delete{id}',[InventoryController::class,'inventory_delete'])->name('inventory.delete');
Route::post('/inventory/add{id}',[InventoryController::class,'inventory_store'])->name('inventory.store');

Route::get('/color',[InventoryController::class,'color'])->name('color');
Route::get('/color/delete{id}',[InventoryController::class,'color_delete'])->name('color.delete');
Route::post('/color/add',[InventoryController::class,'color_add'])->name('color.add');

Route::get('size',[InventoryController::class,'size'])->name('size');
Route::get('size/delete{id}',[InventoryController::class,'size_delete'])->name('size.delete');
Route::post('/size/add',[InventoryController::class,'size_add'])->name('size.add');

// coupon
Route::get('/coupon',[CouponController::class,'coupon'])->name('coupon');
Route::post('/coupon/add',[CouponController::class,'coupon_add'])->name('coupon.add');
Route::get('/coupon/delete{id}',[CouponController::class,'coupon_delete'])->name('coupon.delete');


// order
route::get('/order',[OrederController::class,'order'])->name('order');
route::post('/status/change/{id}',[OrederController::class,'status_change'])->name('status.change');
route::get('/invoice/{id}',[OrederController::class,'invoice'])->name('invoice');
route::get('/invoice/print/{id}',[OrederController::class,'invoice_print'])->name('invoice.print');


// stripe
Route::controller(StripePaymentController::class)->group(function(){
    Route::get('stripe', 'stripe');
    Route::post('stripe/{order_id}', 'stripePost')->name('stripe.post');
});


// roll manager
Route::get('/roll/manager',[RollController::class,'roll_manager'])->name('roll.manager');
Route::post('/permission/create',[RollController::class,'permission_create'])->name('permissiom.create');
Route::post('/roll/create',[RollController::class,'roll_create'])->name('roll.create');
Route::post('/asign/roll',[RollController::class,'asign_roll'])->name('asign.roll');
Route::get('/roll/remove/{id}',[RollController::class,'roll_remove'])->name('roll.remove');
