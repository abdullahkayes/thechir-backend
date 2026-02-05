@extends('layouts.admin')
@section('content')
@can('Orders_access')
@php
    $orders = App\Models\Order::with(['orderProducts.rel_to_product.productInventory'])->get();
@endphp
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3>Order Products Profit Analysis</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>SL</th>
                        <th>Order ID</th>
                        <th>Ordered Products Quantity</th>
                        <th>Per Unit Sell Price</th>
                        <th>Per Unit Buy Price</th>
                        <th>Total Revenue</th>
                        <th>Total COGS</th>
                        <th>Total Profit</th>
                    </tr>
                    @forelse ($orders as $index => $order)
                        @php
                            $totalRevenue = 0;
                            $totalCogs = 0;
                            $totalQuantity = 0;
                            foreach ($order->orderProducts as $orderProduct) {
                                $product = $orderProduct->rel_to_product;
                                $productInventory = $product->productInventory ?? null;
                                $buyPrice = $productInventory ? $productInventory->buy_price : 0;
                                $sellPrice = $orderProduct->price ?? 0;
                                $quantity = $orderProduct->quantity ?? 0;
                                $totalRevenue += $sellPrice * $quantity;
                                $totalCogs += $buyPrice * $quantity;
                                $totalQuantity += $quantity;
                            }
                            $avgSellPrice = $totalQuantity > 0 ? $totalRevenue / $totalQuantity : 0;
                            $avgBuyPrice = $totalQuantity > 0 ? $totalCogs / $totalQuantity : 0;
                            $totalProfit = $totalRevenue - $totalCogs;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $order->id }}</td>
                            <td>{{ $totalQuantity }}</td>
                            <td>{{ number_format($avgSellPrice, 2) }}</td>
                            <td>{{ number_format($avgBuyPrice, 2) }}</td>
                            <td>{{ number_format($totalRevenue, 2) }}</td>
                            <td>{{ number_format($totalCogs, 2) }}</td>
                            <td>{{ number_format($totalProfit, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No Data Found
                            </td>
                        </tr>
                    @endforelse
                </table>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection
