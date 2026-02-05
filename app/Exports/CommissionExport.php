<?php

namespace App\Exports;

use App\Models\Commission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CommissionExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Commission::with(['reseller', 'order'])->get();
    }

    public function headings(): array
    {
        return [
            'Commission ID',
            'Reseller Name',
            'Reseller Email',
            'Order ID',
            'Order Total',
            'Commission Amount',
            'Status',
            'Created Date',
            'Paid Date'
        ];
    }

    public function map($commission): array
    {
        return [
            $commission->id,
            $commission->reseller ? $commission->reseller->name : 'N/A',
            $commission->reseller ? $commission->reseller->email : 'N/A',
            $commission->order ? $commission->order->order_id : 'N/A',
            $commission->order ? '$' . number_format($commission->order->total, 2) : 'N/A',
            '$' . number_format($commission->amount, 2),
            ucfirst($commission->status),
            $commission->created_at->format('M d, Y'),
            $commission->paid_at ? $commission->paid_at->format('M d, Y') : 'Not Paid'
        ];
    }
}