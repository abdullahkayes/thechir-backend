@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">Accounting & Financial Reports</h1>
        </div>
    </div>

    <!-- Accounting Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <ul class="nav nav-tabs" id="accountingTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ledger-tab" data-toggle="tab" href="#ledger" role="tab">General Ledger</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pnl-tab" data-toggle="tab" href="#pnl" role="tab">Profit & Loss</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="analytics-tab" data-toggle="tab" href="#analytics" role="tab">Sales Analytics</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="valuation-tab" data-toggle="tab" href="#valuation" role="tab">Inventory Valuation</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="accountingTabContent">
                        <!-- General Ledger Tab -->
                        <div class="tab-pane fade show active" id="ledger" role="tabpanel">
                            <div class="mb-3">
                                <form method="GET" class="form-inline">
                                    <div class="form-group mr-2">
                                        <label for="start_date" class="mr-2">Start Date:</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                                    </div>
                                    <div class="form-group mr-2">
                                        <label for="end_date" class="mr-2">End Date:</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                                    </div>
                                    <div class="form-group mr-2">
                                        <label for="account_id" class="mr-2">Account:</label>
                                        <select class="form-control" id="account_id" name="account_id">
                                            <option value="">All Accounts</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                                    {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Account</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $runningBalance = 0; @endphp
                                        @foreach($ledgerEntries as $entry)
                                        @foreach($entry->lines as $line)
                                        <tr>
                                            <td>{{ $entry->entry_date->format('M d, Y') }}</td>
                                            <td>{{ $entry->description }}</td>
                                            <td>{{ $line->account->account_name ?? $line->account->name ?? 'Unknown' }}</td>
                                            <td>${{ $line->type === 'debit' ? number_format($line->amount, 2) : '0.00' }}</td>
                                            <td>${{ $line->type === 'credit' ? number_format($line->amount, 2) : '0.00' }}</td>
                                            <td>
                                                @php
                                                    if ($line->type === 'debit') {
                                                        $runningBalance += $line->amount;
                                                    } else {
                                                        $runningBalance -= $line->amount;
                                                    }
                                                @endphp
                                                ${{ number_format($runningBalance, 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Profit & Loss Tab -->
                        <div class="tab-pane fade" id="pnl" role="tabpanel">
                            @php
                                $orders = App\Models\Order::with(['orderProducts.rel_to_product.productInventory'])->get();

                                // Calculate P&L data
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
                        </div>

                        <!-- Sales Analytics Tab -->
                        <div class="tab-pane fade" id="analytics" role="tabpanel">
                            @php
                                // Calculate Sales Analytics
                                $monthlySales = \DB::table('orders')
                                    ->whereYear('orders.created_at', \Carbon\Carbon::now()->year)
                                    ->selectRaw('MONTH(orders.created_at) as month, SUM(orders.total) as sales')
                                    ->groupBy('month')
                                    ->orderBy('month')
                                    ->get()
                                    ->map(function ($item) {
                                        return [
                                            'month' => \Carbon\Carbon::create()->month($item->month)->format('F'),
                                            'sales' => $item->sales,
                                        ];
                                    });

                                $topProducts = \DB::table('orders')
                                    ->join('order_products', 'orders.order_id', '=', 'order_products.order_id')
                                    ->join('products', 'order_products.product_id', '=', 'products.id')
                                    ->select('products.product_name', \DB::raw('SUM(order_products.quantity) as total_sold'))
                                    ->groupBy('products.id', 'products.product_name')
                                    ->orderBy('total_sold', 'desc')
                                    ->take(10)
                                    ->get()
                                    ->map(function ($item) {
                                        return [
                                            'product_name' => $item->product_name,
                                            'total_sold' => $item->total_sold,
                                        ];
                                    });

                                $salesByCategory = \DB::table('orders')
                                    ->join('order_products', 'orders.order_id', '=', 'order_products.order_id')
                                    ->join('products', 'order_products.product_id', '=', 'products.id')
                                    ->join('categories', 'products.category_id', '=', 'categories.id')
                                    ->select('categories.category_name', \DB::raw('SUM(order_products.quantity * order_products.price) as revenue'))
                                    ->groupBy('categories.id', 'categories.category_name')
                                    ->orderBy('revenue', 'desc')
                                    ->get()
                                    ->map(function ($item) {
                                        return [
                                            'category_name' => $item->category_name,
                                            'revenue' => $item->revenue,
                                        ];
                                    });
                            @endphp

                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Monthly Sales</h5>
                                    <canvas id="monthlySalesChart"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h5>Top Products</h5>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Total Sold</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topProducts as $product)
                                            <tr>
                                                <td>{{ $product['product_name'] }}</td>
                                                <td>{{ $product['total_sold'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Sales by Category</h5>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Category Name</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($salesByCategory as $category)
                                            <tr>
                                                <td>{{ $category['category_name'] }}</td>
                                                <td>${{ number_format($category['revenue'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Valuation Tab -->
                        <div class="tab-pane fade" id="valuation" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Total Inventory Value</h5>
                                        </div>
                                        <div class="card-body">
                                            <h2 class="text-primary">${{ number_format($inventoryValuation['total_value'], 2) }}</h2>
                                            <p class="text-muted">Based on current stock levels and purchase costs</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Slow Moving Items</h5>
                                        </div>
                                        <div class="card-body">
                                            <h2 class="text-warning">{{ $inventoryValuation['slow_moving_count'] }}</h2>
                                            <p class="text-muted">Items with low turnover</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h4>Inventory Valuation Details</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Current Stock</th>
                                                <th>Average Cost</th>
                                                <th>Total Value</th>
                                                <th>Last Movement</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inventoryValuation['details'] as $item)
                                            <tr>
                                                <td>{{ $item->product_name }}</td>
                                                <td>{{ $item->current_stock }}</td>
                                                <td>${{ number_format($item->avg_cost, 2) }}</td>
                                                <td>${{ number_format($item->total_value, 2) }}</td>
                                                <td>{{ $item->last_movement && $item->last_movement != '0000-00-00 00:00:00' ? \Carbon\Carbon::parse($item->last_movement)->format('M d, Y') : 'Never' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
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
    const profitTrendCtx = document.getElementById('profitTrendChart');
    if (profitTrendCtx) {
        const profitTrendChart = new Chart(profitTrendCtx.getContext('2d'), {
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
    }

    // Monthly Sales Chart
    const monthlySalesCtx = document.getElementById('monthlySalesChart');
    if (monthlySalesCtx) {
        const monthlySalesData = @json($monthlySales);
        new Chart(monthlySalesCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthlySalesData.map(item => item.month),
                datasets: [{
                    label: 'Sales',
                    data: monthlySalesData.map(item => item.sales),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endsection
