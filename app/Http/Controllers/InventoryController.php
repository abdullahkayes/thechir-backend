<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    function color(){
        $colors=Color::all();
        return view('backend.inventory.color.color',compact('colors'));
    }
    function size(){
        $sizes=Size::all();
        return view('backend.inventory.size.size',compact('sizes'));
    }

    function color_add (Request $request ){
       Color::insert([
       'color_name'=>$request->color_name,
       'color_code'=>$request->color_code,
       ]);
       return back();
    }
    function size_add (Request $request ){
       Size::insert([
       'size_name'=>$request->size_name,
       ]);
       return back();
    }

    function color_delete($id){
        Color::find($id)->delete();
        return back();
    }
    function size_delete($id){
        Size::find($id)->delete();
        return back();
    }

    function inventory($id){
        $products=Product::find($id);
        $colors=Color::all();
        $sizes=Size::all();
       $inventores= Inventory::where('product_id',$id)->get();
        return view('backend.inventory.inventory', compact('colors','sizes','products','inventores'));
    }

    function inventory_store(Request $request ,$id){
 
        if(Inventory::where('product_id',$id)->where('color_id', $request->color_id)->where('size_id',$request->size_id)->exists()){
    
            Inventory::where('product_id',$id)->where('color_id', $request->color_id)->where('size_id',$request->size_id)->increment('quaintity', $request->quaintity);
            return back();
        }   
        Inventory::insert([
           'product_id'=>$id,
           'color_id'=>$request->color_id,
           'size_id'=>$request->size_id,
           'price'=>$request->price,
           'discount'=>$request->discount,
           'discount_price'=>$request->price -($request->price * $request->discount) / 100,
           'quaintity'=>$request->quaintity,
        ]);
        return back();
    }

  function inventory_delete($id){
    Inventory::find($id)->delete();
    return back();
  }

}
