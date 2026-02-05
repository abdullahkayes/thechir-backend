<!DOCTYPE html>
<html>
<head>
    <title>PayPal Payment Gateway Integration - Your Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style type="text/css">
        .paypal-button-container {
            margin-top: 20px;
        }
    </style>
</head>
<?php
$order_id = request()->get('order_id');
$order = App\Models\PaypalOrder::where('order_id', $order_id)->first();
?>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card mt-5">
                <h3 class="card-header p-3">PayPal Payment Gateway Integration - Your Store</h3>
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
                        <p><strong>Payment Method:</strong> PayPal</p>
                        <p><strong>Customer:</strong> {{ $order->name }}</p>
                        <p><strong>Email:</strong> {{ $order->email }}</p>
                    </div>

                    <div class="mb-4">
                        <h5>Payment Instructions</h5>
                        <p>1. Click the PayPal button below to proceed with payment</p>
                        <p>2. You will be redirected to PayPal to complete the transaction</p>
                        <p>3. After successful payment, your order will be processed</p>
                    </div>

                    <form id='paypal-form' method='post' action="{{ route('paypal.post', $order_id) }}">
                        @csrf
                        <input type="hidden" name="paypal_order_id" id="paypal_order_id">
                        <input type="hidden" name="paypal_payer_id" id="paypal_payer_id">
                        <div id="paypal-button-container" class="paypal-button-container"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
                    @endif

@if($order && env('PAYPAL_CLIENT_ID'))
<script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID') }}&currency=USD"></script>
<script type="text/javascript">
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '{{ number_format($order->total, 2, '.', '') }}', // Amount in USD (shipping included)
                        currency_code: 'USD'
                    },
                    description: 'Payment for order {{ $order->order_id }}'
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Payment was successful
                console.log('PayPal payment successful:', details);
                document.getElementById('paypal_order_id').value = data.orderID;
                document.getElementById('paypal_payer_id').value = details.payer.payer_id;
                console.log('Submitting form to:', "{{ route('paypal.post', $order_id) }}");
                document.getElementById('paypal-form').submit();
            });
        },
        onError: function(err) {
            console.error('PayPal payment error:', err);
            alert('Payment failed. Please try again.');
            // Optionally redirect back to checkout or show error details
        },
        onCancel: function(data) {
            console.log('PayPal payment cancelled:', data);
            alert('Payment was cancelled. You can try again or choose another payment method.');
            // Optionally redirect back to checkout page
            window.location.href = '/checkout';
        }
    }).render('#paypal-button-container');

    // Add loading state
    document.addEventListener('DOMContentLoaded', function() {
        const paypalButton = document.querySelector('#paypal-button-container');
        if (paypalButton) {
            paypalButton.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Loading PayPal payment options...</p></div>';
        }
    });

    // Add error handling for PayPal SDK loading
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('paypal')) {
            console.error('PayPal SDK failed to load:', e);
            const paypalButton = document.querySelector('#paypal-button-container');
            if (paypalButton) {
                paypalButton.innerHTML = '<div class="alert alert-danger">PayPal payment is currently unavailable. Please try again later or choose another payment method.</div>';
            }
        }
    });

    // Add timeout for PayPal button loading
    setTimeout(function() {
        const paypalButton = document.querySelector('#paypal-button-container');
        if (paypalButton && paypalButton.innerHTML.includes('Loading')) {
            paypalButton.innerHTML = '<div class="alert alert-warning">PayPal is taking longer than expected to load. Please refresh the page or try again later.</div>';
        }
    }, 10000); // 10 second timeout

    // Add retry functionality
    function retryPayPal() {
        const paypalButton = document.querySelector('#paypal-button-container');
        if (paypalButton) {
            paypalButton.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Retrying PayPal payment options...</p></div>';

            // Re-render PayPal button
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '{{ number_format($order->total, 2, '.', '') }}',
                                currency_code: 'USD'
                            },
                            description: 'Payment for order {{ $order->order_id }}'
                        }],
                        application_context: {
                            return_url: '{{ url("paypal?order_id=" . $order->order_id) }}',
                            cancel_url: '{{ url("checkout") }}'
                        }
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        console.log('PayPal payment successful:', details);
                        document.getElementById('paypal_order_id').value = data.orderID;
                        document.getElementById('paypal_payer_id').value = details.payer.payer_id;
                        document.getElementById('paypal-form').submit();
                    });
                },
                onError: function(err) {
                    console.error('PayPal payment error:', err);
                    alert('Payment failed. Please try again.');
                },
                onCancel: function(data) {
                    console.log('PayPal payment cancelled:', data);
                    alert('Payment was cancelled. You can try again or choose another payment method.');
                    window.location.href = '/checkout';
                }
            }).render('#paypal-button-container');
        }
    }

    // Add retry button for failed loading
    setTimeout(function() {
        const paypalButton = document.querySelector('#paypal-button-container');
        if (paypalButton && paypalButton.innerHTML.includes('warning')) {
            paypalButton.innerHTML += '<div class="text-center mt-3"><button type="button" class="btn btn-primary" onclick="retryPayPal()">Try Again</button></div>';
        }
    }, 12000);

    // Add payment note
    const paymentNote = document.createElement('div');
    paymentNote.className = 'alert alert-info mt-3';
    paymentNote.innerHTML = '<strong>Note:</strong> The total amount includes all charges including shipping. You will be charged in USD.';
    document.querySelector('.card-body').appendChild(paymentNote);
</script>
@else
<script>
    // PayPal client ID not configured
    document.addEventListener('DOMContentLoaded', function() {
        const paypalButton = document.querySelector('#paypal-button-container');
        if (paypalButton) {
            paypalButton.innerHTML = '<div class="alert alert-danger">PayPal payment is not configured. Please contact support or choose another payment method.</div>';
        }
    });
</script>
@endif

</html>