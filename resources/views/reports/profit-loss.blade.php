@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Profit & Loss Report</h4>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <label class="mr-2">Period:</label>
                                <select name="period" class="form-control">
                                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ request('period') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <label class="mr-2">From:</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}">
                            </div>
                            <div class="form-group mr-2">
                                <label class="mr-2">To:</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date', now()->format('Y-m-d')) }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $totalRevenue = 0;
                        $totalCOGS = 0;
                        $netProfit = 0;
                        $grossProfit = 0;
                        $operatingExpenses = collect([]);
                        $topProfitableProducts = collect([]);
                        $monthlyProfit = collect([]);
                        $monthlyData = [];
                        foreach ($orders as $order) {
                            $month = $order->created_at->format('M Y');
                            if (!isset($monthlyData[$month])) {
                                $monthlyData[$month] = 0;
                            }
                            $orderRevenue = 0;
                            $orderCogs = 0;
                            foreach ($order->orderProducts as $orderProduct) {
                                $product = $orderProduct->rel_to_product;
                                $productInventory = $product->productInventory ?? null;
                                $buyPrice = $productInventory ? $productInventory->buy_price : 0;
                                $sellPrice = $orderProduct->price ?? 0;
                                $quantity = $orderProduct->quantity ?? 0;
                                $orderRevenue += $sellPrice * $quantity;
                                $orderCogs += $buyPrice * $quantity;
                            }
                            $monthlyData[$month] += $orderRevenue - $orderCogs;
                        }
                        foreach ($monthlyData as $month => $profit) {
                            $monthlyProfit->push(['month' => $month, 'profit' => $profit]);
                        }

                        foreach ($orders as $order) {
                            $orderRevenue = 0;
                            $orderCogs = 0;
                            foreach ($order->orderProducts as $orderProduct) {
                                $product = $orderProduct->rel_to_product;
                                $productInventory = $product->productInventory ?? null;
                                $buyPrice = $productInventory ? $productInventory->buy_price : 0;
                                $sellPrice = $orderProduct->price ?? 0;
                                $quantity = $orderProduct->quantity ?? 0;
                                $orderRevenue += $sellPrice * $quantity;
                                $orderCogs += $buyPrice * $quantity;
                            }
                            $totalRevenue += $orderRevenue;
                            $totalCOGS += $orderCogs;
                        }
                        $grossProfit = $totalRevenue - $totalCOGS;
                        $netProfit = $grossProfit; // Assuming no operating expenses
                    @endphp
                    <!-- P&L Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Total Revenue</h5>
                                    <h3>${{ number_format($totalRevenue, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5>Total COGS</h5>
                                    <h3>${{ number_format($totalCOGS, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-{{ $netProfit >= 0 ? 'primary' : 'warning' }} text-white">
                                <div class="card-body">
                                    <h5>Net Profit</h5>
                                    <h3>${{ number_format($netProfit, 2) }}</h3>
                                    <small>{{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed P&L Statement -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Profit & Loss Statement</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Category</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Revenue Section -->
                                                <tr class="table-primary">
                                                    <td colspan="2"><strong>REVENUE</strong></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>Sales Revenue</td>
                                                    <td>Product sales from orders</td>
                                                    <td class="text-success">${{ number_format($totalRevenue, 2) }}</td>
                                                </tr>

                                                <!-- Cost of Goods Sold -->
                                                <tr class="table-danger">
                                                    <td colspan="2"><strong>COST OF GOODS SOLD</strong></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>Purchase Cost</td>
                                                    <td>FIFO cost of goods sold</td>
                                                    <td class="text-danger">${{ number_format($totalCOGS, 2) }}</td>
                                                </tr>

                                                <!-- Gross Profit -->
                                                <tr class="table-info">
                                                    <td colspan="2"><strong>GROSS PROFIT</strong></td>
                                                    <td class="text-{{ $grossProfit >= 0 ? 'success' : 'danger' }}">
                                                        <strong>${{ number_format($grossProfit, 2) }}</strong>
                                                    </td>
                                                </tr>

                                                <!-- Operating Expenses -->
                                                <tr class="table-warning">
                                                    <td colspan="2"><strong>OPERATING EXPENSES</strong></td>
                                                    <td></td>
                                                </tr>
                                                @foreach($operatingExpenses as $expense)
                                                <tr>
                                                    <td>{{ $expense->account_name }}</td>
                                                    <td>{{ $expense->description }}</td>
                                                    <td class="text-danger">(${number_format($expense->amount, 2)})</td>
                                                </tr>
                                                @endforeach

                                                <!-- Net Profit -->
                                                <tr class="table-success">
                                                    <td colspan="2"><strong>NET PROFIT</strong></td>
                                                    <td class="text-{{ $netProfit >= 0 ? 'success' : 'danger' }}">
                                                        <strong>${{ number_format($netProfit, 2) }}</strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profit Margin Analysis -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Profit Margins</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Gross Margin:</strong>
                                        <span class="float-right">{{ $totalRevenue > 0 ? number_format(($grossProfit / $totalRevenue) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Net Margin:</strong>
                                        <span class="float-right">{{ $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 2) : 0 }}%</span>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-success" style="width: {{ $totalRevenue > 0 ? (($grossProfit / $totalRevenue) * 100) : 0 }}%"></div>
                                    </div>
                                    <small class="text-muted">Green bar shows gross profit margin</small>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Trend -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Monthly Profit Trend</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="profitTrendChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Profit Analysis -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Orders Profit Analysis</h5>
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
                                                $orderRevenue = 0;
                                                $orderCogs = 0;
                                                $totalQuantity = 0;
                                                foreach ($order->orderProducts as $orderProduct) {
                                                    $product = $orderProduct->rel_to_product;
                                                    $productInventory = $product->productInventory ?? null;
                                                    $buyPrice = $productInventory ? $productInventory->buy_price : 0;
                                                    $sellPrice = $orderProduct->price ?? 0;
                                                    $quantity = $orderProduct->quantity ?? 0;
                                                    $orderRevenue += $sellPrice * $quantity;
                                                    $orderCogs += $buyPrice * $quantity;
                                                    $totalQuantity += $quantity;
                                                }
                                                $avgSellPrice = $totalQuantity > 0 ? $orderRevenue / $totalQuantity : 0;
                                                $avgBuyPrice = $totalQuantity > 0 ? $orderCogs / $totalQuantity : 0;
                                                $orderProfit = $orderRevenue - $orderCogs;
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $order->id }}</td>
                                                <td>{{ $totalQuantity }}</td>
                                                <td>{{ number_format($avgSellPrice, 2) }}</td>
                                                <td>{{ number_format($avgBuyPrice, 2) }}</td>
                                                <td>{{ number_format($orderRevenue, 2) }}</td>
                                                <td>{{ number_format($orderCogs, 2) }}</td>
                                                <td>{{ number_format($orderProfit, 2) }}</td>
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
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profit Trend Chart
    const profitTrendCtx = document.getElementById('profitTrendChart').getContext('2d');
    const profitTrendChart = new Chart(profitTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyProfit->pluck('month')) !!},
            datasets: [{
                label: 'Monthly Profit',
                data: {!! json_encode($monthlyProfit->pluck('profit')) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
