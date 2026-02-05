<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BuyReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Order::with(['customer', 'reseller', 'b2b', 'distributer', 'orderProducts.product'])
            ->whereNotNull('reseller_id')
            ->orWhereNotNull('b2b_id')
            ->orWhereNotNull('distributer_id')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer Name',
            'Customer Email',
            'User Type',
            'User Name',
            'Order Total',
            'Order Date',
            'Status',
            'Products Count'
        ];
    }

    public function map($order): array
    {
        $userType = 'Customer';
        $userName = $order->customer ? $order->customer->name : 'N/A';

        if ($order->reseller) {
            $userType = 'Reseller';
            $userName = $order->reseller->name;
        } elseif ($order->b2b) {
            $userType = 'B2B';
            $userName = $order->b2b->business_name;
        } elseif ($order->distributer) {
            $userType = 'Distributer';
            $userName = $order->distributer->company_name;
        }

        return [
            $order->order_id,
            $order->customer ? $order->customer->name : 'N/A',
            $order->customer ? $order->customer->email : 'N/A',
            $userType,
            $userName,
            '$' . number_format($order->total, 2),
            $order->created_at->format('M d, Y'),
            ucfirst($order->status),
            $order->orderProducts->count()
        ];
    }
}