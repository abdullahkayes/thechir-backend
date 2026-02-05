<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Coustomer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AnalyticsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::with(['orderProducts.rel_to_product', 'customer'])->get()->map(function ($order) {
            $products = $order->orderProducts->map(function ($op) {
                return ($op->rel_to_product->product_name ?? 'N/A') . ' (Qty: ' . $op->quantity . ')';
            })->implode(', ');

            return [
                'Order ID' => $order->order_id,
                'Customer Name' => $order->customer->name ?? 'N/A',
                'Customer Email' => $order->customer->email ?? 'N/A',
                'Sub Total' => $order->sub_total,
                'Total' => $order->total,
                'Discount' => $order->discount ?? 0,
                'Payment Method' => $order->payment_method == 1 ? 'Cash' : 'Online',
                'Coupon' => $order->coupon ?? 'N/A',
                'Products' => $products,
                'Created At' => $order->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer Name',
            'Customer Email',
            'Sub Total',
            'Total',
            'Discount',
            'Payment Method',
            'Coupon',
            'Products',
            'Created At',
        ];
    }
}
