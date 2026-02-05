@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">🏠 ERP Dashboard Overview</h1>
            <p class="text-muted">Real-time sales statistics, current stock status, pending orders, and profit–loss overview</p>
        </div>
    </div>

    <!-- Main KPI Cards Row -->
    <div class="row">
        <!-- Monthly Sales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Monthly Sales
                                <small class="{{ $kpis['total_sales']['growth_rate'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    ({{ number_format($kpis['total_sales']['growth_rate'], 1) }}%)
                                </small>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($kpis['total_sales']['current'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Stock Value -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Current Stock Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($kpis['current_stock_value'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Profit -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Monthly Profit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($kpis['monthly_profit'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $kpis['pending_orders'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary KPI Cards Row -->
    <div class="row">
        <!-- Low Stock Alerts -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $kpis['low_stock_items'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Growth Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Orders Growth
                                <small class="{{ $kpis['orders_growth_rate'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    ({{ number_format($kpis['orders_growth_rate'], 1) }}%)
                                </small>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $kpis['orders_growth_rate'] >= 0 ? '+' : '' }}{{ number_format($kpis['orders_growth_rate'], 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trending-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Customers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Active Customers (This Month)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $kpis['active_customers'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Suppliers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Suppliers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $kpis['active_suppliers'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🚀 Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('product') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> New Product
                            </a>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('purchase-orders.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-shopping-cart"></i> Purchase Order
                            </a>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('order') }}" class="btn btn-info btn-block">
                                <i class="fas fa-shopping-bag"></i> New Order
                            </a>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('suppliers.create') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-truck"></i> Add Supplier
                            </a>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('inventory.index') }}" class="btn btn-danger btn-block">
                                <i class="fas fa-sliders-h"></i> Stock Adj.
                            </a>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('reports.profit-loss') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-chart-pie"></i> P&L Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4 border-warning">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">📱 Pending QR Payment Approvals</h6>
                </div>
                <div class="card-body">
                    @if(isset($pendingQRPayments) && $pendingQRPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingQRPayments as $payment)
                                    <tr>
                                        <td>#{{ $payment->id }}</td>
                                        <td>{{ $payment->customer_name }}</td>
                                        <td>
                                            @if($payment->payment_type === 'venmo')
                                                <span class="badge badge-primary">Venmo</span>
                                            @else
                                                <span class="badge badge-success">Cash App</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.qr-payments.index') }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-eye"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">No pending QR payments to review!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Row -->
    <div class="row">
        <!-- Sales Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Sales Trend (Last 12 Months)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🏆 Top Selling Products</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Units Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($topProductsData->isEmpty())
                                <tr>
                                    <td colspan="2" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        No products sold this month
                                    </td>
                                </tr>
                                @else
                                @foreach($topProductsData as $product)
                                <tr>
                                    <td>{{ $product->product_name }}</td>
                                    <td><span class="badge badge-primary">{{ $product->total_sold }}</span></td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🛒 Recent Orders</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($recentOrders->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        No orders found
                                    </td>
                                </tr>
                                @else
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @if($order->orderTracking)
                                            @if($order->orderTracking->status == 1)
                                                <span class="badge badge-warning">Processing</span>
                                            @elseif($order->orderTracking->status == 2)
                                                <span class="badge badge-info">Shipped</span>
                                            @elseif($order->orderTracking->status == 3)
                                                <span class="badge badge-success">Delivered</span>
                                            @else
                                                <span class="badge badge-secondary">Pending</span>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">No Status</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">⚠️ Low Stock Alerts</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Alert Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($lowStockAlerts->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                                        All products have sufficient stock
                                    </td>
                                </tr>
                                @else
                                @foreach($lowStockAlerts as $product)
                                <tr>
                                    <td>{{ $product->product_name }}</td>
                                    <td><span class="badge badge-danger">{{ $product->stock_quantity }}</span></td>
                                    <td><span class="text-danger">Critical</span></td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Movements Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📦 Recent Inventory Movements</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Movement Type</th>
                                    <th>Quantity</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentInventoryMovements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $movement->product->product_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($movement->movement_type == 'in')
                                            <span class="badge badge-success">Stock In</span>
                                        @elseif($movement->movement_type == 'out')
                                            <span class="badge badge-danger">Stock Out</span>
                                        @elseif($movement->movement_type == 'adjustment')
                                            <span class="badge badge-warning">Adjustment</span>
                                        @else
                                            <span class="badge badge-info">{{ ucfirst($movement->movement_type) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $movement->quantity }}</td>
                                    <td>{{ $movement->reference_type ?? '-' }}</td>
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

@push('scripts')
<script>
// Sales Chart
var ctx = document.getElementById("salesChart").getContext('2d');
var salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(collect($salesChartData)->pluck('month')) !!},
        datasets: [{
            label: 'Sales ($)',
            data: {!! json_encode(collect($salesChartData)->pluck('sales')) !!},
            borderColor: "rgb(78, 115, 223)",
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            tension: 0.1
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value, index, values) {
                        return '$' + number_format(value, 0);
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Sales: $' + number_format(context.parsed.y, 2);
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection
