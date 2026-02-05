@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Inventory Valuation Report</h4>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <label class="mr-2">Valuation Method:</label>
                                <select name="method" class="form-control">
                                    <option value="fifo" {{ request('method', 'fifo') == 'fifo' ? 'selected' : '' }}>FIFO</option>
                                    <option value="lifo" {{ request('method') == 'lifo' ? 'selected' : '' }}>LIFO</option>
                                    <option value="average" {{ request('method') == 'average' ? 'selected' : '' }}>Average Cost</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Inventory Valuation Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Total Inventory Value</h5>
                                    <h3>${{ number_format($totalInventoryValue, 2) }}</h3>
                                    <small>Based on {{ strtoupper(request('method', 'fifo')) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Total Products</h5>
                                    <h3>{{ $totalProducts }}</h3>
                                    <small>In inventory</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Slow Moving Items</h5>
                                    <h3>{{ $slowMovingCount }}</h3>
                                    <small>30+ days no movement</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Valuation Table -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Inventory Valuation Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>SKU</th>
                                                    <th>Current Stock</th>
                                                    <th>Avg Cost</th>
                                                    <th>Total Value</th>
                                                    <th>Last Movement</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($inventoryValuation as $item)
                                                <tr>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>{{ $item->sku }}</td>
                                                    <td>{{ $item->current_stock }}</td>
                                                    <td>${{ number_format($item->average_cost, 2) }}</td>
                                                    <td>${{ number_format($item->total_value, 2) }}</td>
                                                    <td>{{ $item->last_movement && $item->last_movement != '0000-00-00 00:00:00' ? \Carbon\Carbon::parse($item->last_movement)->format('Y-m-d') : 'Never' }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $item->status == 'active' ? 'success' : ($item->status == 'slow' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($item->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Movement Analysis -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Stock Movement Trends</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="stockMovementChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Turnover -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Inventory Turnover Analysis</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Inventory Turnover Ratio:</strong>
                                        <span class="float-right">{{ number_format($inventoryTurnoverRatio, 2) }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Days Sales of Inventory:</strong>
                                        <span class="float-right">{{ number_format($daysSalesOfInventory, 1) }} days</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Stock-to-Sales Ratio:</strong>
                                        <span class="float-right">{{ number_format($stockToSalesRatio, 2) }}</span>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-info" style="width: {{ min($inventoryTurnoverRatio * 10, 100) }}%"></div>
                                    </div>
                                    <small class="text-muted">Higher turnover ratio indicates better inventory management</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slow Moving & Dead Stock -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Slow Moving & Dead Stock</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Days Since Last Sale</th>
                                                    <th>Current Stock</th>
                                                    <th>Stock Value</th>
                                                    <th>Potential Loss</th>
                                                    <th>Recommendation</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($slowMovingItems as $item)
                                                <tr>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>{{ $item->days_since_last_sale }}</td>
                                                    <td>{{ $item->current_stock }}</td>
                                                    <td>${{ number_format($item->stock_value, 2) }}</td>
                                                    <td class="text-danger">${{ number_format($item->potential_loss, 2) }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $item->recommendation == 'sell_at_discount' ? 'warning' : ($item->recommendation == 'write_off' ? 'danger' : 'info') }}">
                                                            {{ ucwords(str_replace('_', ' ', $item->recommendation)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expiry Analysis -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Expiry Alerts</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <h6>Expiring Within 30 Days</h6>
                                        <ul class="mb-0">
                                            @foreach($expiringSoon as $item)
                                            <li>{{ $item->product_name }} - {{ $item->lot_number }} ({{ $item->days_until_expiry }} days)</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="alert alert-danger">
                                        <h6>Expired Items</h6>
                                        <ul class="mb-0">
                                            @foreach($expiredItems as $item)
                                            <li>{{ $item->product_name }} - {{ $item->lot_number }} (Expired {{ $item->days_expired }} days ago)</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Age Analysis -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Stock Age Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="stockAgeChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Optimization Recommendations -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Inventory Optimization Recommendations</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="alert alert-info">
                                                <h6>Reorder Recommendations</h6>
                                                <ul class="mb-0">
                                                    @foreach($reorderRecommendations as $item)
                                                    <li>{{ $item->product_name }} (Current: {{ $item->current_stock }}, Min: {{ $item->min_stock }})</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="alert alert-warning">
                                                <h6>Overstock Alerts</h6>
                                                <ul class="mb-0">
                                                    @foreach($overstockItems as $item)
                                                    <li>{{ $item->product_name }} (Stock: {{ $item->current_stock }}, Max: {{ $item->max_stock }})</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="alert alert-success">
                                                <h6>Fast Moving Items</h6>
                                                <ul class="mb-0">
                                                    @foreach($fastMovingItems as $item)
                                                    <li>{{ $item->product_name }} ({{ $item->sales_velocity }} units/day)</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
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
    // Stock Movement Chart
    const stockMovementCtx = document.getElementById('stockMovementChart').getContext('2d');
    const stockMovementChart = new Chart(stockMovementCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($stockMovementData->pluck('month')) !!},
            datasets: [{
                label: 'Stock In',
                data: {!! json_encode($stockMovementData->pluck('stock_in')) !!},
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }, {
                label: 'Stock Out',
                data: {!! json_encode($stockMovementData->pluck('stock_out')) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Stock Age Chart
    const stockAgeCtx = document.getElementById('stockAgeChart').getContext('2d');
    const stockAgeChart = new Chart(stockAgeCtx, {
        type: 'pie',
        data: {
            labels: ['0-30 days', '31-90 days', '91-180 days', '180+ days'],
            datasets: [{
                data: {!! json_encode($stockAgeDistribution) !!},
                backgroundColor: [
                    'rgb(75, 192, 192)',
                    'rgb(255, 205, 86)',
                    'rgb(255, 99, 132)',
                    'rgb(153, 102, 255)'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
});
</script>
@endsection
