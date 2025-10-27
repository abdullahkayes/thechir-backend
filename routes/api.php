<?php

use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\CoustomerController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/customer/info2', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// customer
Route::post('/coustomer/register',[CoustomerController::class,'register']);
Route::post('/coustomer/login',[CoustomerController::class,'login']);
Route::post('/coustomer/logout',[CoustomerController::class,'logout']);
Route::post('/coustomer/profile/update/{id}',[CoustomerController::class,'update']);

//categories
Route::get('/categories',[CategoryController::class,'categories']);

// products
Route::get('/new_products',[ProductController::class,'new_products']);
Route::get('/all_products',[ProductController::class,'all_products']);
Route::post('/scerch',[ProductController::class,'scerch']);
Route::get('/products/detailes/{id}',[ProductController::class,'products_detailes']);

// Cart
Route::post('add/cart/',[CartController::class,'add_cart']);
Route::get('cart/{id}',[CartController::class,'cart']);
Route::post('cart/update',[CartController::class,'cart_update']);


// checkout
Route::post('apply/coupon',[CheckoutController::class,'apply_coupon']);
Route::post('checkout',[CheckoutController::class,'checkout']);

// order
Route::get('/myorder/{id}',[OrderController::class,'myorder']);
Route::get('/invoice/{id}',[OrderController::class,'invoice']);
Route::get('/orders/{id}',[OrderController::class,'orders']);

// forgot password
Route::post('/forgot/password',[CoustomerController::class,'forgot_password']);
Route::post('/password/reset',[CoustomerController::class,'reset_password']);


// color
Route::get('color',[ProductController::class,'color']);

// traker
// routes/api.php
