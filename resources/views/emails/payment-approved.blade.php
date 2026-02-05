@component('mail::message')
# ✅ Payment Approved!

Dear {{ $qrPayment->customer_name }},

Great news! Your payment has been successfully approved.

## Payment Details

| Detail | Information |
|--------|-------------|
| **Payment Type** | {{ ucfirst($qrPayment->payment_type) }} |
| **Amount** | ${{ number_format($qrPayment->amount, 2) }} |
| **Transaction ID** | {{ $qrPayment->transaction_id ?? 'N/A' }} |
| **Date** | {{ $qrPayment->approved_at->format('M d, Y H:i') }} |

## What's Next?

Your order is now being processed and will be shipped soon. You will receive another email with tracking information once your order ships.

@if($qrPayment->order_id)
@component('mail::button', ['url' => $orderUrl])
View Order Details
@endcomponent
@endif

Thank you for shopping with **The Chir Jersey**!

Best regards,<br>
The Chir Jersey Team

---
*If you have any questions, please don't hesitate to contact us.*
@endcomponent
