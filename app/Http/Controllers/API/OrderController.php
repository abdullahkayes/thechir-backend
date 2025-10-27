<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    function myorder($id){
        $myorders =Order::where('coustomer_id',$id)->get();
        return response()->json([
            'myorders'=>$myorders
        ]);
    }

   function invoice($id){
        $data =Order::find($id);
       $pdf = Pdf::loadView('pdf.invoice',[
        'data'=>$data,
       ]);
      return $pdf->download('invoice.pdf');
    }
   


}
