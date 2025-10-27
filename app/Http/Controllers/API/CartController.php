<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Inventory;
use Illuminate\Http\Request;

class CartController extends Controller
{

function add_cart(Request $request){
 $request->validate([
    'color_id'=>'required',
    'size_id'=>'required',
 ],[
    'color_id.required'=>'Please Select The Color',
    'size_id.required'=>'Please Select The Size',
 ]);

  $quantity = Inventory::where('product_id',$request->product_id)->where('color_id',$request->color_id)->where('size_id', $request->size_id)->first()->quaintity;

  if($quantity > $request->quantity){
     if(Cart::where('product_id',$request->product_id)->where('color_id',$request->color_id)->where('size_id', $request->size_id)->exists()){

        Cart::where('product_id',$request->product_id)->where('color_id',$request->color_id)->where('size_id', $request->size_id)->increment('quantity',$request->quantity);

        return response()->json([
            'success'=>'Product Added To Cart Successfully'
         ]);
     }

     else{
        Cart::insert([
            'product_id'=>$request->product_id,
            'coustomer_id'=>$request->coustomer_id,
            'color_id'=>$request->color_id,
            'size_id'=>$request->size_id,
            'quantity'=>$request->quantity,
          ]);
        return response()->json([
            'success'=>'Product Added To Cart Successfully'
         ]);
     }
  }

    }


function cart($id){
$carts = Cart::with('rel_to_product')->where('coustomer_id',$id)->get();

$carts->each(function($cart){
   $cart->inventory = $cart->inventory;
});
return response()->json([
'carts'=>$carts
]);
}

function cart_update(Request $request){
foreach($request->carts as $cart){
    Cart::where('id', $cart['id'])->update([
        'quantity'=>$cart['quantity']
    ]);
}

    return response()->json([
     'success'=>'Cart Updated Successfully'
    ]);
}



}
