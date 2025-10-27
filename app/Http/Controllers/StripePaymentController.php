<?php

namespace App\Http\Controllers;

use App\Mail\OrderMail;
use App\Models\Billing;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\StripeOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe(): View
    {
        return view('stripe');
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request, $order_id): RedirectResponse
    {

        $data =StripeOrder::where('order_id',$order_id)->first();



        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        Stripe\Charge::create ([
                "amount" => $data['total'],
                "currency" => "bdt",
                "source" => $request->stripeToken,
                "description" => "Test payment from itsolutionstuff.com."
        ]);

     Order::insert([
'order_id'=>$order_id,
'coustomer_id'=>$data['coustomer_id'],
'sub_total'=>$data['sub_total'],
'total'=>$data['total'],
'discount'=>$data['discount'],
'payment_method'=>$data['payment_method'],
'coupon'=>$data['coupon'],
'created_at'=>Carbon::now(),
]);

Billing::insert([
    'order_id'=>$order_id,
    'coustomer_id'=>$data['coustomer_id'],
    'name'=>$data['name'],
    'company'=>$data['company'],
    'street'=>$data['street'],
    'apartment'=>$data['apartment'],
    'city'=>$data['city'],
    'phone'=>$data['phone'],
    'email'=>$data['email'],
    'created_at'=>Carbon::now(),
]);

$carts = Cart::with('rel_to_product')->where('coustomer_id',$data['coustomer_id'])->get();
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

Mail::to($data['email'])->send(new OrderMail($order_id));

        return redirect("http://127.0.0.1:8000/order/success/{$order_id}");
    }
}

