<!DOCTYPE html>
<html>
<head>
    <title>Laravel 12 Stripe Payment Gateway Integration Example - ItSolutionStuff.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style type="text/css">
        #card-element{
            height: 50px;
            padding-top: 16px;
        }
    </style>
</head>
<?php
$order_id = request()->get('order_id');
$order = App\Models\StripeOrder::where('order_id', $order_id)->first();
?>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card mt-5">
                <h3 class="card-header p-3">Stripe Payment</h3>
                <div class="card-body">

                    @if(!$order)
                        <div class="alert alert-danger">
                            Order not found. Please try again.
                        </div>
                    @elseif(!isset($paymentIntent))
                        <div class="alert alert-danger">
                            Payment system error. Please contact support.
                        </div>
                    @else
                    @session('success')
                        <div class="alert alert-success" role="alert">
                            {{ $value }}
                        </div>
                    @endsession

                    <form id='payment-form'>
                        <div class="mb-3">
                            <label for="cardholder-name" class="form-label">Cardholder Name</label>
                            <input type="text" id="cardholder-name" class="form-control" placeholder="Enter cardholder name" required>
                        </div>
                        <div id="card-element" class="form-control mb-3"></div>
                        <div id="card-errors" class="text-danger mb-3" role="alert"></div>
                        <button
                            id='pay-btn'
                            class="btn btn-success"
                            type="button"
                            style="width: 100%;padding: 7px;"
                        >PAY ${{ number_format($order->total, 2) }}</button>
                    </form>

                    <form id='success-form' method='POST' action='{{ route("stripe.post", $order_id) }}' style='display: none;'>
                        @csrf
                        <input type="hidden" name="payment_intent_id" value="{{ $paymentIntent->id }}">
                        <input type="hidden" name="order_id" value="{{ $order_id }}">
                        <input type="hidden" name="payment_status" id="payment_status" value="success">
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

</body>

<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
    try {
        @if(!isset($paymentIntent) || !$paymentIntent)
            console.error('Payment intent is missing from server');
            alert('Payment system error: Payment intent not created. Please contact support.');
            throw new Error('Payment intent is missing');
        @endif

        // Initialize Stripe
        const stripeKey = '{{ env('STRIPE_KEY') }}';
        console.log('Initializing Stripe with key:', stripeKey.substring(0, 20) + '...');
        
        const stripe = Stripe(stripeKey);
        if (!stripe) {
            console.error('Failed to initialize Stripe');
            alert('Stripe is not properly configured. Please check with support.');
            throw new Error('Stripe initialization failed');
        }
        
        console.log('✓ Stripe initialized successfully');

        const elements = stripe.elements();
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#424770',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
                invalid: {
                    color: '#fa755a',
                },
            }
        });
        
        cardElement.mount('#card-element');
        console.log('✓ Card element mounted');

        // Handle real-time validation errors
        cardElement.addEventListener('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
                console.warn('Card validation error:', event.error.message);
            } else {
                displayError.textContent = '';
            }
        });

        const payBtn = document.getElementById('pay-btn');
        const paymentForm = document.getElementById('payment-form');
        const successForm = document.getElementById('success-form');
        const cardholderName = document.getElementById('cardholder-name');

        payBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            // Validate cardholder name
            if (!cardholderName.value.trim()) {
                alert('Please enter cardholder name');
                return;
            }

            payBtn.disabled = true;
            payBtn.textContent = 'Processing...';
            console.log('Starting payment confirmation...');

            try {
                const clientSecret = '{{ $paymentIntent->client_secret ?? '' }}';
                console.log('Using client secret:', clientSecret.substring(0, 20) + '...');

                if (!clientSecret) {
                    throw new Error('Client secret is missing');
                }

                // Confirm card payment
                const { paymentIntent, error } = await stripe.confirmCardPayment(clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: cardholderName.value.trim(),
                        },
                    },
                });

                if (error) {
                    console.error('Payment confirmation error:', error);
                    document.getElementById('payment_status').value = 'error';
                    successForm.submit();
                    return;
                }

                if (!paymentIntent) {
                    console.error('No payment intent returned');
                    document.getElementById('payment_status').value = 'error';
                    successForm.submit();
                    return;
                }

                console.log('Payment intent status:', paymentIntent.status);
                console.log('Payment intent ID:', paymentIntent.id);

                // Set status and submit form for backend processing
                document.getElementById('payment_status').value = paymentIntent.status;
                successForm.submit();

            } catch (exception) {
                console.error('Exception during payment:', exception);
                payBtn.disabled = false;
                payBtn.textContent = 'PAY ${{ number_format($order->total, 2) }}';
                alert('An error occurred: ' + exception.message);
            }
        });

        console.log('✓ Payment form initialized and ready');

    } catch (error) {
        console.error('Stripe initialization failed:', error);
        alert('Payment system is not properly configured. Please contact support.');
    }
</script>

</html>
