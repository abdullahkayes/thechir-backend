<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Invoice</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .invoice-container {
            border: 1px solid #e1e1e1;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            background: white;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #4a6fdc 100%);
            color: white;
            padding: 40px 30px;
            position: relative;
            overflow: hidden;
        }
        .header::after {
            content: "";
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .company-info {
            position: relative;
            z-index: 2;
        }
        .company-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }
        .company-details {
            font-size: 14px;
            opacity: 0.9;
        }
        .invoice-title {
            font-size: 32px;
            margin: 30px 0 15px;
            text-align: center;
            font-weight: 300;
            letter-spacing: 1px;
            position: relative;
        }
        .invoice-title::after {
            content: "";
            display: block;
            width: 60px;
            height: 3px;
            background: #4a6fdc;
            margin: 15px auto;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            padding: 25px 40px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #e1e1e1;
        }
        .info-box {
            flex: 1;
        }
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 12px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 16px;
            font-weight: 500;
        }
        .client-info {
            padding: 30px 40px;
            background-color: white;
            border-bottom: 1px solid #f0f0f0;
        }
        .section-title {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
            color: #4a6fdc;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background-color: #f5f7fa;
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            background-color: #f9f9f9;
            padding: 25px 40px;
            border-top: 1px solid #e1e1e1;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
        }
        .total-label {
            font-weight: 600;
            color: #555;
        }
        .grand-total {
            font-size: 18px;
            color: #2c3e50;
            font-weight: 700;
            border-top: 1px solid #ddd;
            padding-top: 12px;
            margin-top: 12px;
        }
        .footer {
            text-align: center;
            padding: 30px;
            color: #777;
            font-size: 14px;
            border-top: 1px solid #e1e1e1;
            background-color: #f9f9f9;
        }
        .thank-you {
            font-size: 18px;
            margin-bottom: 15px;
            color: #4a6fdc;
            font-weight: 500;
        }
        .payment-info {
            background: #f5f7fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4a6fdc;
        }
        .payment-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .stamp {
            float: right;
            padding: 10px 15px;
            background: #f5f7fa;
            border: 2px dashed #ccc;
            border-radius: 5px;
            font-style: italic;
            color: #777;
            margin-top: -50px;
        }
    </style>
</head>
@php
    $order =App\Models\Order::where('order_id',$order_id)->first();
    $billing =App\Models\Billing::where('order_id',$order_id)->first();
    $order_product =App\Models\OrderProduct::where('order_id',$order_id)->get();
@endphp
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <div class="company-name">ELITE DESIGNS</div>
                <div class="company-details">123 Premium Avenue • New York, NY 10001</div>
                <div class="company-details">contact@elitedesigns.com • (212) 555-0100</div>
            </div>
        </div>

        <div class="invoice-title">INVOICE</div>
        <div class="stamp">PAID</div>
        <div class="invoice-info">
            <div class="info-box">
                <div class="info-label">Invoice Number</div>
                <div class="info-value">{{ $order_id }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Date Issued</div>
                <div class="info-value">{{ $order->created_at }}</div>
            </div>
        </div>

        <div class="client-info">
            <div class="section-title">Bill To</div>
            <div style="font-weight: 500; margin-bottom: 5px;">{{ $billing->company }}</div>
            <div>Attn:{{ $billing->name }}</div>
            <div>{{ $billing->street }}, {{ $billing->apartment }}</div>
            <div>{{ $billing->city }}</div>
            <div>{{ $billing->email }}</div>
        </div>

        <div style="padding: 0 40px;">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order_product as $product)
                    <tr>
                        <td>{{ $product->rel_to_product->product_name }}</td>
                        <td class="text-center">{{ $product->quantity }}</td>
                        <td class="text-right"> &#2547;{{ $product->price }}</td>
                        <td class="text-right"> &#2547;{{ $product->price * $product->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="payment-info">
                <div class="payment-title">Payment Information</div>
                <div>Bank Transfer: Chase Bank • Account #987654321 • Routing #021000021</div>
                <div>PayPal: payments@elitedesigns.com</div>
            </div>

            <div class="total-section">
                <div class="total-row">
                    <div class="total-label">Subtotal</div>
                    <div>&#2547;{{ $order->sub_total }}</div>
                </div>
                <div class="total-row">
                    <div class="total-label">Discount</div>
                    <div>-&#2547;{{ $order->discount }}</div>
                </div>
                <div class="total-row grand-total">
                    <div class="total-label">Total Due</div>
                    <div>&#2547;{{ $order->total }}</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="thank-you">Thank you for your business!</div>
            <div>Please make payment by the due date to avoid late fees</div>
            <div style="margin-top: 15px;">Questions? Email accounting@elitedesigns.com or call (212) 555-0100 ext. 2</div>
            <div style="font-size: 12px; margin-top: 20px; color: #999;">Invoice generated on June 15, 2023 • Invoice ID: 8XJ9K2L4</div>
        </div>
    </div>
</body>
</html>