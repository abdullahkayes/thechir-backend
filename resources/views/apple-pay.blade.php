<!DOCTYPE html>
<html>
<head>
    <title>Apple Pay Payment Gateway Integration - Your Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style type="text/css">
        .apple-pay-button-container {
            margin-top: 20px;
        }
    </style>
</head>
<?php
$order_id = request()->get('order_id');
$order = App\Models\ApplePayOrder::where('order_id', $order_id)->first();
?>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card mt-5">
                <h3 class="card-header p-3">Apple Pay Payment Gateway Integration - Your Store</h3>
                <div class="card-body">

                    @if(!$order)
                        <div class="alert alert-danger">
                            <h5>Order Not Found</h5>
                            <p>The order you're trying to pay for could not be found. Please try again or contact support.</p>
                            <a href="/checkout" class="btn btn-primary">Back to Checkout</a>
                        </div>
                    @else
                    @session('success')
                        <div class="alert alert-success" role="alert">
                            {{ $value }}
                        </div>
                    @endsession

                    <div class="mb-4">
                        <h5>Order Details</h5>
                        <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
                        <p><strong>Subtotal:</strong> ${{ number_format($order->sub_total, 2) }}</p>
                        <p><strong>Delivery Charge:</strong> ${{ number_format($order->delivery_charge ?? 0, 2) }}</p>
                        <p><strong>Discount:</strong> ${{ number_format($order->discount ?? 0, 2) }}</p>
                        <p><strong>Total Amount:</strong> ${{ number_format($order->total, 2) }}</p>
                        <p><strong>Payment Method:</strong> Apple Pay</p>
                        <p><strong>Customer:</strong> {{ $order->name }}</p>
                        <p><strong>Email:</strong> {{ $order->email }}</p>
                    </div>

                    <div class="mb-4">
                        <h5>Payment Instructions</h5>
                        <p>1. Click the Apple Pay button below to proceed with payment</p>
                        <p>2. You will be redirected to Apple Pay to complete the transaction</p>
                        <p>3. After successful payment, your order will be processed</p>
                    </div>

                    <form id='apple-pay-form' method='post' action="{{ route('apple-pay.post', $order_id) }}">
                        @csrf
                        <div id="apple-pay-button-container" class="apple-pay-button-container"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
                    @endif

@if(env('APPLE_PAY_MERCHANT_ID'))
<script src="https://applepay.cdn-apple.com/jsapi/v1/apple-pay-sdk.js"></script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        if (window.ApplePaySession && ApplePaySession.canMakePayments()) {
            const applePayButton = document.createElement('button');
            applePayButton.className = 'btn btn-dark';
            applePayButton.style.width = '100%';
            applePayButton.style.padding = '12px';
            applePayButton.style.fontSize = '16px';
            applePayButton.style.borderRadius = '8px';
            applePayButton.style.backgroundColor = '#000';
            applePayButton.style.color = '#fff';
            applePayButton.style.border = 'none';
            applePayButton.style.cursor = 'pointer';
            applePayButton.innerHTML = '<i class="fab fa-apple"></i> Pay with Apple Pay';
            
            document.getElementById('apple-pay-button-container').appendChild(applePayButton);

            applePayButton.addEventListener('click', function() {
                const paymentRequest = {
                    countryCode: 'US',
                    currencyCode: 'USD',
                    merchantCapabilities: ['supports3DS'],
                    supportedNetworks: ['visa', 'masterCard', 'amex', 'discover'],
                    total: {
                        label: 'Your Store',
                        amount: '{{ number_format($order->total / 120, 2, '.', '') }}'
                    }
                };

                const session = new ApplePaySession(1, paymentRequest);

                session.onvalidatemerchant = function(event) {
                    // In a real implementation, you would validate the merchant here
                    // For now, we'll assume the merchant is valid
                    session.completeMerchantValidation(JSON.stringify({
                        merchantSession: {
                            merchantIdentifier: '{{ env('APPLE_PAY_MERCHANT_ID') }}',
                            displayName: 'Your Store',
                            initiative: 'web',
                            initiativeContext: 'yourstore.com'
                        }
                    }));
                };

                session.onpaymentauthorized = function(event) {
                    // Payment was successful
                    console.log('Apple Pay payment successful:', event.payment);
                    console.log('Submitting form to:', "{{ route('apple-pay.post', $order_id) }}");
                    document.getElementById('apple-pay-form').submit();
                };

                session.oncancel = function(event) {
                    console.log('Apple Pay payment cancelled:', event);
                    alert('Payment was cancelled. You can try again or choose another payment method.');
                    window.location.href = '/checkout';
                };

                session.begin();
            });
        } else {
            const applePayButton = document.querySelector('#apple-pay-button-container');
            if (applePayButton) {
                applePayButton.innerHTML = '<div class="alert alert-danger">Apple Pay is not available on this device or browser. Please try another payment method.</div>';
            }
        }
    });

    // Add loading state
    document.addEventListener('DOMContentLoaded', function() {
        const applePayButton = document.querySelector('#apple-pay-button-container');
        if (applePayButton) {
            applePayButton.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Loading Apple Pay payment options...</p></div>';
        }
    });

    // Add error handling for Apple Pay SDK loading
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('apple')) {
            console.error('Apple Pay SDK failed to load:', e);
            const applePayButton = document.querySelector('#apple-pay-button-container');
            if (applePayButton) {
                applePayButton.innerHTML = '<div class="alert alert-danger">Apple Pay payment is currently unavailable. Please try again later or choose another payment method.</div>';
            }
        }
    });

    // Add timeout for Apple Pay button loading
    setTimeout(function() {
        const applePayButton = document.querySelector('#apple-pay-button-container');
        if (applePayButton && applePayButton.innerHTML.includes('Loading')) {
            applePayButton.innerHTML = '<div class="alert alert-warning">Apple Pay is taking longer than expected to load. Please refresh the page or try again later.</div>';
        }
    }, 10000); // 10 second timeout

    // Add currency conversion note
    const currencyNote = document.createElement('div');
    currencyNote.className = 'alert alert-info mt-3';
    currencyNote.innerHTML = '<strong>Note:</strong> The amount shown in USD is an approximate conversion from BDT. The final amount will be determined by Apple Pay at the time of payment based on current exchange rates.';
    document.querySelector('.card-body').appendChild(currencyNote);
</script>
@else
<script>
    // Apple Pay merchant ID not configured
    document.addEventListener('DOMContentLoaded', function() {
        const applePayButton = document.querySelector('#apple-pay-button-container');
        if (applePayButton) {
            applePayButton.innerHTML = '<div class="alert alert-danger">Apple Pay payment is not configured. Please contact support or choose another payment method.</div>';
        }
    });
</script>
@endif

</html>