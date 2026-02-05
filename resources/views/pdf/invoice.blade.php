<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Invoice</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #f5f7fa, #c3cfe2);
      margin: 0;
      padding: 2rem;
    }

    .invoice-box {
      max-width: 850px;
      margin: auto;
      background: #fff;
      border-radius: 12px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      color: #333;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 3px solid #4a90e2;
      padding-bottom: 20px;
    }

    .header .company {
      font-size: 2rem;
      font-weight: bold;
      color: #2d3e50;
    }

    .header .invoice-title {
      font-size: 1.25rem;
      background: #4a90e2;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
    }

    .section {
      margin-top: 30px;
    }

    .section h3 {
      margin-bottom: 10px;
      color: #2c3e50;
    }

    .details, .totals {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }

    .details div, .totals div {
      flex: 1 1 45%;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 25px;
    }

    table thead {
      background: #4a90e2;
      color: white;
    }

    table th, table td {
      padding: 14px 12px;
      border: 1px solid #e0e0e0;
      text-align: left;
    }

    table tbody tr:hover {
      background: #f4faff;
      transition: 0.2s ease-in-out;
    }

    .total-section {
      text-align: right;
      margin-top: 30px;
    }

    .total-section h2 {
      margin: 0.3em 0;
    }

    .total-section h2:last-child {
      background: #4a90e2;
      color: white;
      padding: 10px 20px;
      display: inline-block;
      border-radius: 8px;
    }

    .footer {
      margin-top: 40px;
      font-size: 0.95rem;
      color: #666;
      text-align: center;
    }

    @media print {
      body {
        background: white;
        padding: 0;
      }
      .invoice-box {
        box-shadow: none;
        border: none;
        padding: 0;
      }
    }
  </style>
</head>
@php
    $order =App\Models\Order::where('order_id',$data->order_id)->first();
    $billing =App\Models\Billing::where('order_id',$data->order_id)->first();
    $order_product =App\Models\OrderProduct::with('product')->where('order_id',$data->order_id)->get();
@endphp
<body>

  <div class="invoice-box">
    <div class="header">
      <div class="company">Dazzling Shoppers</div>
      <div class="invoice-title">Invoice {{ $data->order_id }}</div>
    </div>

    <div class="section details">
      <div>
        <h3>Bill To:</h3>
        <p>Client Name: {{ $billing ? $billing->name : 'N/A' }}<br>
        <p>Address: {{ $billing ? $billing->street . ', ' . $billing->apartment : 'N/A' }}</p>
        <p>Email: {{ $billing ? $billing->email : 'N/A' }}</p><br>
        @if($order && $order->reseller_id)
          <p style="background: #ecf0f1; padding: 8px; border-left: 4px solid #3498db;"><strong>Order Type:</strong> Reseller Order</p>
        @endif
      </div>

      <div>
        <h3>Invoice Details:</h3>
        <p>Date: {{ $order ? $order->created_at : 'N/A' }}<br>
        @if($order && $order->balance_used && $order->balance_used > 0)
          <p style="background: #d5f4e6; padding: 8px; border-left: 4px solid #27ae60;"><strong>Commission Deducted:</strong> ${{ number_format($order->balance_used, 2) }}</p>
        @endif
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Description</th>
          <th>Qty</th>
          <th>Unit Price</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
         @forelse ($order_product as $product)
                   <tr>
                       <td>{{ $product->product ? $product->product->product_name : 'Product not found' }}</td>
                       <td class="text-center">{{ $product->quantity }}</td>
                       <td class="text-right"> ${{ number_format($product->price, 2) }}</td>
                       <td class="text-right"> ${{ number_format($product->price * $product->quantity, 2) }}</td>
                   </tr>
         @empty
                   <tr>
                       <td colspan="4" class="text-center">No products found for this order</td>
                   </tr>
         @endforelse
      </tbody>
    </table>

    <div class="total-section">
      <h2>Subtotal: ${{ number_format($order ? $order->sub_total : 0, 2) }}</h2>
      <h2>Discount: ${{ number_format($order ? $order->discount : 0, 2) }}</h2>
      @if($order && $order->balance_used && $order->balance_used > 0)
        <h2 style="background: #27ae60; color: white; padding: 10px 20px; display: inline-block; border-radius: 8px; margin-top: 15px;">Commission Applied: -${{ number_format($order->balance_used, 2) }}</h2>
      @endif
      <h2>Total Due: ${{ number_format($order ? $order->total : 0, 2) }}</h2>
    </div>

    <div class="footer">
      <p>Payment is due within 14 days. Late payments may incur a 5% fee.<br>
      Please contact billing@yourcompany.com for any questions.</p>
    </div>
  </div>

</body>
</html>
