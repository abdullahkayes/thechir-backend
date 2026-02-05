<?php

namespace App\Mail;

use App\Models\QRPayment;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;

class PaymentApprovedMail extends Mailable
{
    public $qrPayment;

    public function __construct(QRPayment $qrPayment)
    {
        $this->qrPayment = $qrPayment;
    }

    public function build()
    {
        $orderUrl = URL::route('order.success', ['order_id' => $this->qrPayment->order_id ?? 0]);

        return $this->subject('Payment Approved - The Chir Jersey')
            ->markdown('emails.payment-approved', [
                'qrPayment' => $this->qrPayment,
                'orderUrl' => $orderUrl,
            ]);
    }
}
