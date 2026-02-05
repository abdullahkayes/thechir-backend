<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Invoice - {{ $order_id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }
        
        .invoice-wrapper {
            max-width: 850px;
            margin: 0 auto;
        }
        
        .invoice-container {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(0,0,0,0.15), 0 10px 30px rgba(0,0,0,0.1);
        }
        
        /* Premium Header */
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: white;
            padding: 50px 50px 60px;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .header::after {
            content: "";
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(233,69,96,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .brand-section {
            flex: 1;
        }
        
        .brand-logo {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #fff 0%, #e8e8e8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .brand-tagline {
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        
        .invoice-badge {
            background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%);
            padding: 20px 35px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(233,69,96,0.3);
        }
        
        .invoice-badge .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .invoice-badge .number {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        /* Invoice Info Bar */
        .info-bar {
            display: flex;
            background: #f8f9fa;
            padding: 25px 50px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item {
            flex: 1;
            padding: 0 20px;
            border-right: 1px solid #dee2e6;
        }
        
        .info-item:last-child {
            border-right: none;
        }
        
        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #6c757d;
            margin-bottom: 6px;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
        }
        
        /* Client Section */
        .client-section {
            padding: 40px 50px;
            display: flex;
            gap: 60px;
        }
        
        .client-box {
            flex: 1;
        }
        
        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #e94560;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title::before {
            content: "";
            width: 30px;
            height: 2px;
            background: #e94560;
        }
        
        .client-name {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }
        
        .client-details {
            font-size: 14px;
            color: #495057;
            line-height: 1.8;
        }
        
        .client-details strong {
            color: #1a1a2e;
        }
        
        /* Items Table */
        .items-section {
            padding: 0 50px 40px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
        }
        
        .items-table thead th {
            background: #1a1a2e;
            color: white;
            padding: 18px 20px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }
        
        .items-table thead th:first-child {
            border-radius: 10px 0 0 10px;
        }
        
        .items-table thead th:last-child {
            border-radius: 0 10px 10px 0;
        }
        
        .items-table tbody tr {
            transition: all 0.3s ease;
        }
        
        .items-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .items-table tbody td {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        
        .items-table tbody tr:last-child td {
            border-bottom: 2px solid #1a1a2e;
        }
        
        .product-name {
            font-weight: 600;
            color: #1a1a2e;
        }
        
        .product-sku {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .qty-badge {
            background: #e9ecef;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            color: #1a1a2e;
        }
        
        .price {
            font-weight: 600;
            color: #1a1a2e;
        }
        
        /* Totals Section */
        .totals-section {
            padding: 0 50px 50px;
        }
        
        .totals-wrapper {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 16px;
            padding: 30px;
            margin-left: auto;
            max-width: 400px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 14px;
            border-bottom: 1px dashed #ced4da;
        }
        
        .total-row:last-child {
            border-bottom: none;
        }
        
        .total-label {
            color: #495057;
        }
        
        .total-value {
            font-weight: 600;
            color: #1a1a2e;
        }
        
        .delivery-row {
            background: #fff3cd;
            margin: 0 -30px;
            padding: 12px 30px;
            border-left: 4px solid #ffc107;
        }
        
        .discount-row {
            color: #28a745;
        }
        
        .discount-row .total-value {
            color: #28a745;
        }
        
        .grand-total {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            margin: 20px -30px -30px;
            padding: 25px 30px;
            border-radius: 0 0 16px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .grand-total .total-label {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .grand-total .total-value {
            color: white;
            font-size: 28px;
            font-weight: 700;
        }
        
        /* Payment Info */
        .payment-section {
            padding: 0 50px 40px;
        }
        
        .payment-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-left: 5px solid #2196f3;
            padding: 25px 30px;
            border-radius: 0 12px 12px 0;
        }
        
        .payment-title {
            font-size: 14px;
            font-weight: 700;
            color: #1565c0;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .payment-details {
            font-size: 13px;
            color: #424242;
            line-height: 1.8;
        }
        
        /* Footer */
        .footer {
            background: #1a1a2e;
            color: white;
            padding: 40px 50px;
            text-align: center;
        }
        
        .thank-you {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #fff 0%, #e8e8e8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .footer-message {
            font-size: 14px;
            color: rgba(255,255,255,0.7);
            margin-bottom: 20px;
        }
        
        .contact-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            font-size: 13px;
            color: rgba(255,255,255,0.6);
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .copyright {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 12px;
            color: rgba(255,255,255,0.4);
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
@php
    $order = App\Models\Order::where('order_id', $order_id)->first();
    $billing = App\Models\Billing::where('order_id', $order_id)->first();
    $order_product = App\Models\OrderProduct::with('product')->where('order_id', $order_id)->get();
    
    // Fallback values if order or billing data isn't available
    if (!$order) {
        $order = (object) [
            'created_at' => now(),
            'sub_total' => 0,
            'discount' => 0,
            'balance_used' => 0,
            'delivery_charge' => 0,
            'total' => 0
        ];
    }
    
    if (!$billing) {
        $billing = (object) [
            'company' => 'Test Company',
            'name' => 'Test Customer',
            'street' => '123 Test Street',
            'apartment' => 'Apt 1',
            'city' => 'Test City',
            'state' => 'TS',
            'zip' => '12345',
            'email' => 'test@example.com'
        ];
    }
    
    // Ensure delivery_charge has a default value
    $deliveryCharge = $order->delivery_charge ?? 0;
@endphp
<body>
    <div class="invoice-wrapper">
        <div class="invoice-container">
            <!-- Premium Header -->
            <div class="header">
                <div class="header-content">
                    <div class="brand-section">
                        <div class="brand-logo">THE CHIR JERSEY</div>
                        <div class="brand-tagline">Premium Quality Apparel</div>
                    </div>
                    <div class="invoice-badge">
                        <div class="label">Invoice</div>
                        <div class="number">{{ $order_id }}</div>
                    </div>
                </div>
            </div>

            <!-- Info Bar -->
            <div class="info-bar">
                <div class="info-item">
                    <div class="info-label">Invoice Date</div>
                    <div class="info-value">{{ $order->created_at ? $order->created_at->format('F d, Y') : now()->format('F d, Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Order ID</div>
                    <div class="info-value">{{ $order_id }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-badge status-paid">Paid</span>
                    </div>
                </div>
            </div>

            <!-- Client Section -->
            <div class="client-section">
                <div class="client-box">
                    <div class="section-title">Bill To</div>
                    <div class="client-name">{{ $billing->name }}</div>
                    <div class="client-details">
                        @if($billing->company)<strong>{{ $billing->company }}</strong><br>@endif
                        {{ $billing->street }}<br>
                        @if($billing->apartment){{ $billing->apartment }}<br>@endif
                        {{ $billing->city }}, {{ $billing->state }} {{ $billing->zip }}<br>
                        <strong>Email:</strong> {{ $billing->email }}
                    </div>
                </div>
                <div class="client-box">
                    <div class="section-title">Ship To</div>
                    <div class="client-name">{{ $billing->name }}</div>
                    <div class="client-details">
                        @if($billing->company)<strong>{{ $billing->company }}</strong><br>@endif
                        {{ $billing->street }}<br>
                        @if($billing->apartment){{ $billing->apartment }}<br>@endif
                        {{ $billing->city }}, {{ $billing->state }} {{ $billing->zip }}<br>
                        <strong>Email:</strong> {{ $billing->email }}
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="items-section">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Product Description</th>
                            <th class="text-center" style="width: 15%;">Qty</th>
                            <th class="text-right" style="width: 20%;">Unit Price</th>
                            <th class="text-right" style="width: 20%;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order_product as $product)
                        <tr>
                            <td>
                                <div class="product-name">{{ $product->product->product_name ?? 'Unknown Product' }}</div>
                                @if(isset($product->product->sku))
                                <div class="product-sku">SKU: {{ $product->product->sku }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="qty-badge">{{ $product->quantity }}</span>
                            </td>
                            <td class="text-right price">${{ number_format($product->price, 2) }}</td>
                            <td class="text-right price">${{ number_format($product->price * $product->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="totals-section">
                <div class="totals-wrapper">
                    <div class="total-row">
                        <span class="total-label">Subtotal</span>
                        <span class="total-value">${{ number_format($order->sub_total ?? 0, 2) }}</span>
                    </div>
                    
                    @if($deliveryCharge > 0)
                    <div class="total-row delivery-row">
                        <span class="total-label">🚚 Delivery Charge</span>
                        <span class="total-value">${{ number_format($deliveryCharge, 2) }}</span>
                    </div>
                    @endif
                    
                    @if(($order->discount ?? 0) > 0)
                    <div class="total-row discount-row">
                        <span class="total-label">Discount</span>
                        <span class="total-value">-${{ number_format($order->discount, 2) }}</span>
                    </div>
                    @endif
                    
                    @if(($order->balance_used ?? 0) > 0)
                    <div class="total-row discount-row">
                        <span class="total-label">Commission Balance Used</span>
                        <span class="total-value">-${{ number_format($order->balance_used, 2) }}</span>
                    </div>
                    @endif
                    
                    <div class="grand-total">
                        <span class="total-label">Total Amount</span>
                        <span class="total-value">${{ number_format($order->total ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="payment-section">
                <div class="payment-box">
                    <div class="payment-title">Payment Information</div>
                    <div class="payment-details">
                        <strong>Payment Method:</strong> {{ $order->payment_method ?? 'Online Payment' }}<br>
                        <strong>Transaction ID:</strong> {{ $order_id }}<br>
                        <strong>Payment Status:</strong> Completed<br>
                        If you have any questions about this invoice, please contact our support team.
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="thank-you">Thank You For Your Business!</div>
                <div class="footer-message">
                    We appreciate your trust in The Chir Jersey. Your order has been confirmed and will be processed shortly.
                </div>
                <div class="contact-info">
                    <div class="contact-item">📧 support@thechirjersey.com</div>
                    <div class="contact-item">📞 (212) 555-0100</div>
                    <div class="contact-item">🌐 www.thechirjersey.com</div>
                </div>
                <div class="copyright">
                    © {{ date('Y') }} The Chir Jersey. All rights reserved. | Invoice generated on {{ now()->format('F d, Y') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>