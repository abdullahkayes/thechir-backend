<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\OrderMail;
use App\Models\StripeOrder;
use Illuminate\Support\Facades\Mail;


class CheckoutController extends Controller
{
    function apply_coupon(Request $request){
        $coupon = $request->coupon;
     if(Coupon::where('coupon',$coupon)->exists()){
   if(Carbon::now()->format('Y-m-d')<=Coupon::where('coupon',$coupon)->first()->validity){
    return response()->json([
        'amount'=>Coupon::where('coupon',$coupon)->first()->amount
    ]);
   }else{
    return response()->json([
        'notexists'=>'Coupon Expired'
    ]);
   }
}else{
    return response()->json([
        'notexists'=>'Coupon Dose Not Exists'
    ]);
}
    }

function checkout(Request $request){
    $order_id=uniqid();
if($request->payment_method == '1'){
Order::insert([
'order_id'=>$order_id,
'coustomer_id'=>$request->coustomer_id,
'sub_total'=>$request->sub_total,
'total'=>$request->total,
'discount'=>$request->discount,
'payment_method'=>$request->payment_method,
'coupon'=>$request->coupon,
'created_at'=>Carbon::now(),
]);

Billing::insert([
    'order_id'=>$order_id,
    'coustomer_id'=>$request->coustomer_id,
    'name'=>$request->name,
    'company'=>$request->company,
    'street'=>$request->street,
    'apartment'=>$request->apartment,
    'city'=>$request->city,
    'phone'=>$request->phone,
    'email'=>$request->email,
    'created_at'=>Carbon::now(),
]);

$carts = Cart::with('rel_to_product')->where('coustomer_id',$request->coustomer_id)->get();
$carts->each(function($cart){
    $cart->inventory = $cart->inventory;
 });

 foreach($carts as $cart){
    OrderProduct::insert([
'order_id'=>$order_id,
'product_id'=>$cart->product_id,
'color_id'=>$cart->color_id,
'size_id'=>$cart->size_id,
'quantity'=>$cart->quantity,
'price'=>$cart->inventory->discount_price,
'created_at'=>Carbon::now(),
    ]);
 }
//  Cart::where('coustomer_id',$request->coustomer_id)->delete();

Mail::to($request->email)->send(new OrderMail($order_id));

 return response()->json([
'order_id'=>$order_id,
]);
}
else if($request->payment_method == '2'){

StripeOrder::insert([
    'order_id'=>$order_id,
    'coustomer_id'=>$request->coustomer_id,
    'sub_total'=>$request->sub_total,
    'total'=>$request->total,
    'discount'=>$request->discount,
    'payment_method' =>$request->payment_method,
    'coupon' =>$request->coupon,
    'name' =>$request->name,
    'company' =>$request->company,
    'street' =>$request->street,
    'apartment' =>$request->apartment,
    'city' =>$request->city,
    'phone' =>$request->phone,
    'email' =>$request->email,
    'created_at' =>Carbon::now(),
]);

 return response()->json([
'redirect'=>url('stripe?order_id='.$order_id),
]);
}

}











}
