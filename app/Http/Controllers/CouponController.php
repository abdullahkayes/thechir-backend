<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    function coupon(){
       $coupons= Coupon::all();
        return view('Backend.coupon.coupon',compact('coupons'));
    }
    function coupon_add(Request $request){
     Coupon::insert([
   'coupon'=>$request->coupon,
   'amount'=>$request->amount,
   'validity'=>$request->validity,
     ]);
     return back()->with('success','Coupon Added Successfully');

    }
function coupon_delete($id){
  Coupon::find($id)->delete();
  return back()->with('error','Coupon Deleted Successfully');
}

}
