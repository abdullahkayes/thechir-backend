<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class OrederController extends Controller
{
    function order(Request $request){

        $query =Order::query();

       if($request->startDate && $request->endDate){
        $start =Carbon::parse($request->startDate)->startOfDay();
        $end =Carbon::parse($request->endDate)->endOfDay();
        $query->whereBetween('created_at',[$start, $end]);
       }
       elseif($request->startDate){
                $start =Carbon::parse($request->startDate)->startOfDay();
              $query->whereBetween('created_at', '>=', $start );
       }
       elseif($request->startDate){
                $end =Carbon::parse($request->endDate)->startOfDay();
              $query->whereBetween('created_at', '<=', $end );
       }



       $orders=$query->latest()->get();

    //order by month
        $MonthlyOrders =Order::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->when($request->startDate, function($q) use ($request){
        $q->whereDate('created_at', '>=', $request->startDate);
       })->when($request->endDate, function($q) use ($request){
        $q->whereDate('created_at', '<=', $request->endDate);
       })->groupBy('month')->orderBy('month')->pluck('total','month');

        $orderData=[];
        for($i=1; $i<=12; $i++){
            $orderData[]= $MonthlyOrders[$i] ?? 0;
        }

    //   order by day
       $dailyOrders =Order::selectRaw('DATE(created_at) as day, COUNT(*) as total')
       ->when($request->startDate, function($q) use ($request){
        $q->whereDate('created_at', '>=', $request->startDate);
       })->when($request->endDate, function($q) use ($request){
        $q->whereDate('created_at', '<=', $request->endDate);
       })->groupBy('day')->orderBy('day')->pluck('total','day');

       $days =$dailyOrders->keys()->toArray();
       $dayWiseData =$dailyOrders->values()->toArray();

        //seles by month
        $selesMonth =Order::selectRaw('MONTH(created_at) as month, SUM(total) as total_amount')
        ->when($request->startDate, function($q) use ($request){
        $q->whereDate('created_at', '>=', $request->startDate);
       })->when($request->endDate, function($q) use ($request){
        $q->whereDate('created_at', '<=', $request->endDate);
       })->groupBy('month')->orderBy('month')->pluck('total_amount','month');

        $selesData=[];
        for($i=1; $i<=12; $i++){
            $selesData[]= $selesMonth[$i] ?? 0;
        }

        return view('Backend.order.order',compact('orders','orderData','days','dayWiseData','selesData'));
    }
    function status_change(Request $request ,$id){
       Order::find($id)->update([
             'status'=>$request->status,
       ]);
       return back();
    }
    function invoice($id){
        $data =Order::find($id);
       $pdf = Pdf::loadView('pdf.invoice',[
        'data'=>$data,
       ]);
      return $pdf->download('invoice.pdf');
    }
    function invoice_print($id){
        $data =Order::find($id);
       $pdf = Pdf::loadView('pdf.invoice',[
        'data'=>$data,
       ]);
      return $pdf->stream();
    }


}
