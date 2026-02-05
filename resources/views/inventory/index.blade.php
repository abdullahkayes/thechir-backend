@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Inventory Management</h4>
                </div>
                <div class="card-body">
                    <!-- Stock Status Overview -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Total Products</h5>
                                    <h3>{{ $totalProducts }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Total Stock</h5>
                                    <h3>{{ $totalStock }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Low Stock Items</h5>
                                    <h3>{{ $lowStockCount }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5>Out of Stock</h5>
                                    <h3>{{ $outOfStockCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expiry Alerts Overview -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Expired Products</h5>
                                    <h3>{{ $expiredCount ?? 0 }}</h3>
                                    <small>Products past expiry date</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5><i class="fas fa-clock"></i> Expiring Soon</h5>
                                    <h3>{{ $expiringSoonCount ?? 0 }}</h3>
                                    <small>Products expiring within 30 days</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Current Stock</th>
                                    <th>Reserved</th>
                                    <th>Available</th>
                                    <th>Expiry Status</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventory as $item)
                                <tr class="{{ $item->expiry_status == 'expired' ? 'table-danger' : ($item->expiry_status == 'expiring_soon' ? 'table-warning' : '') }}">
                                    <td>
                                        {{ $item->product_name }}
                                        @if($item->expiry_status == 'expired')
                                            <span class="badge badge-danger ml-1">EXPIRED</span>
                                        @elseif($item->expiry_status == 'expiring_soon')
                                            <span class="badge badge-warning ml-1">EXPIRING SOON</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->sku }}</td>
                                    <td>{{ $item->size ?? 'N/A' }}</td>
                                    <td>{{ $item->color ?? 'N/A' }}</td>
                                    <td>{{ $item->current_stock }}</td>
                                    <td>{{ $item->reserved_stock }}</td>
                                    <td>{{ $item->available_stock }}</td>
                                    <td>
                                        @if($item->expiry_status == 'expired')
                                            <span class="text-danger font-weight-bold">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                EXPIRED {{ $item->days_until_expiry ? abs($item->days_until_expiry) . ' days ago' : '' }}
                                            </span>
                                        @elseif($item->expiry_status == 'expiring_soon')
                                            <span class="text-warning font-weight-bold">
                                                <i class="fas fa-clock"></i>
                                                {{ $item->days_until_expiry }} days left
                                            </span>
                                        @elseif($item->expiry_status == 'valid')
                                            <span class="text-success">
                                                <i class="fas fa-check-circle"></i>
                                                Valid until {{ $item->expiry_date }}
                                            </span>
                                        @else
                                            <span class="text-muted">No expiry date</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $item->status == 'in_stock' ? 'success' : ($item->status == 'low_stock' ? 'warning' : 'danger') }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewStockDetails({{ $item->product_id }})">Details</button>
                                        <button class="btn btn-sm btn-warning" onclick="adjustStock({{ $item->product_id }})">Adjust</button>
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

    <!-- Recent Movements -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Inventory Movements</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Reference</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMovements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $movement->product->product_name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $movement->movement_type == 'IN' ? 'success' : 'danger' }}">
                                            {{ $movement->movement_type }}
                                        </span>
                                    </td>
                                    <td>{{ $movement->quantity }}</td>
                                    <td>{{ $movement->reference_type }} #{{ $movement->reference_id }}</td>
                                    <td>{{ $movement->reason }}</td>
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

<!-- Stock Details Modal -->
<div class="modal fade" id="stockDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="stockDetailsContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="adjustStockForm" action="" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="adjustProductId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Adjustment Type</label>
                        <select name="type" class="form-control" required>
                            <option value="ADJUSTMENT">Add Stock</option>
                            <option value="DAMAGE">Subtract Stock (Damaged)</option>
                            <option value="MISSING">Subtract Stock (Missing)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <input type="text" name="reason" class="form-control" required placeholder="Enter reason for adjustment">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Adjust Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
function viewStockDetails(productId) {
    $.get(`/api/inventory/stock-details/${productId}`, function(data) {
        let html = '<table class="table table-striped"><thead><tr><th>Batch Number</th><th>Manufacture Date</th><th>Expiry Date</th><th>Days Until Expiry</th><th>Status</th></tr></thead><tbody>';
        data.forEach(item => {
            let statusClass = '';
            let statusText = '';

            if (item.is_expired) {
                statusClass = 'text-danger font-weight-bold';
                statusText = 'EXPIRED';
            } else if (item.is_expiring_soon) {
                statusClass = 'text-warning font-weight-bold';
                statusText = 'EXPIRING SOON';
            } else {
                statusClass = 'text-success';
                statusText = 'VALID';
            }

            html += `<tr>
                <td>${item.batch_number || 'N/A'}</td>
                <td>${item.manufacture_date || 'N/A'}</td>
                <td>${item.expiry_date || 'N/A'}</td>
                <td>${item.days_until_expiry !== null ? item.days_until_expiry : 'N/A'}</td>
                <td class="${statusClass}">${statusText}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        $('#stockDetailsContent').html(html);
        $('#stockDetailsModal').modal('show');
    });
}

function adjustStock(productId) {
    $('#adjustStockForm').attr('action', `/inventory/adjust/${productId}`);
    $('#adjustStockModal').modal('show');
}
</script>
@endsection
