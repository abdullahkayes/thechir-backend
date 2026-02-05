<?php
// Get orders directly for the page
use App\Models\Order;
$pageOrders = Order::with(['customer', 'orderProducts.product', 'orderTracking'])
    ->latest()
    ->get()
    ->map(function ($order) {
        return [
            'id' => $order->id,
            'order_id' => $order->order_id,
            'customer_name' => $order->customer->name ?? 'N/A',
            'total' => $order->total,
            'status' => $order->status,
            'tracking_status' => $order->orderTracking ? $order->orderTracking->status : 1,
            'created_at' => $order->created_at ? $order->created_at->format('Y-m-d') : 'N/A',
            'products' => $order->orderProducts->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->product_name ?? 'Unknown',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            })->toArray(),
        ];
    })->toArray();
?>

@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Returns & Refunds</h1>
                <div class="btn-group">
                    <a href="{{ route('order') }}" class="btn btn-info">
                        <i class="fas fa-shopping-cart"></i> Order Management
                    </a>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newReturnModal">
                        <i class="fas fa-plus"></i> Process New Return
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Returns Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Returns
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $returns->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-undo fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resellable Items
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $returns->where('orderProducts', '!=', null)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Damaged Items
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Refund Amount
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($returns->sum('total'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Returns Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Return History</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="returnsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Return Date</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $return)
                        <tr>
                            <td>{{ $return->order_id }}</td>
                            <td>{{ $return->customer->name ?? 'N/A' }}</td>
                            <td>{{ $return->updated_at ? $return->updated_at->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $return->orderProducts->count() }}</td>
                            <td>${{ number_format($return->total, 2) }}</td>
                            <td>
                                <span class="badge badge-warning">
                                    <i class="fas fa-undo"></i> Returned
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('order') }}?order={{ $return->id }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('returns.show', $return) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-list"></i> Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No returns found.
                                    <br>
                                    <small>Returns will appear here once customers start returning items.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $returns->links() }}
        </div>
    </div>
</div>

<!-- New Return Modal -->
<div class="modal fade" id="newReturnModal" tabindex="-1" role="dialog" aria-labelledby="newReturnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newReturnModalLabel">Process New Return</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="orderSearch">Search Order</label>
                    <select class="form-control" id="orderSearch" onchange="loadOrderDetails()">
                        <option value="">Select an order to return</option>
                        <!-- Orders will be loaded via AJAX -->
                    </select>
                </div>
                <div id="orderDetails" style="display: none;">
                    <h6>Order Details</h6>
                    <div id="selectedOrderInfo"></div>
                    <hr>
                    <form id="returnForm">
                        @csrf
                        <div class="form-group">
                            <label>Select Items to Return</label>
                            <div id="orderItems"></div>
                        </div>
                        <div class="form-group">
                            <label for="returnReason">Return Reason</label>
                            <select class="form-control" name="return_reason" required>
                                <option value="">Select reason</option>
                                <option value="damaged">Damaged Product</option>
                                <option value="wrong_item">Wrong Item Received</option>
                                <option value="not_as_described">Not as Described</option>
                                <option value="defective">Defective Product</option>
                                <option value="customer_request">Customer Request</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="refundAmount">Refund Amount</label>
                            <input type="number" class="form-control" name="refund_amount" step="0.01" min="0" required>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="processReturn()" id="processReturnBtn" disabled>
                    <i class="fas fa-undo"></i> Process Return
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
// Embedded order data from server
window.returnableOrdersData = <?php echo json_encode($pageOrders); ?>;

$(document).ready(function() {
    // Load returnable orders when modal opens
    $('#newReturnModal').on('show.bs.modal', function() {
        loadReturnableOrders();
    });
});

function loadReturnableOrders() {
    const data = window.returnableOrdersData || [];
    const select = document.getElementById('orderSearch');
    select.innerHTML = '<option value="">Select an order to return</option>';
    
    if (data.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'No orders available for return';
        option.disabled = true;
        select.appendChild(option);
        return;
    }
    
    data.forEach(order => {
        const option = document.createElement('option');
        option.value = order.id;
        option.textContent = `Order #${order.order_id} - ${order.customer_name} - $${order.total}`;
        select.appendChild(option);
    });
}

function loadOrderDetails() {
    const orderId = document.getElementById('orderSearch').value;
    if (!orderId) {
        document.getElementById('orderDetails').style.display = 'none';
        document.getElementById('processReturnBtn').disabled = true;
        return;
    }

    const data = window.returnableOrdersData || [];
    const order = data.find(o => o.id == orderId);
    if (order) {
        displayOrderDetails(order);
        document.getElementById('orderDetails').style.display = 'block';
        document.getElementById('processReturnBtn').disabled = false;
    }
}

function displayOrderDetails(order) {
    // Display order info
    document.getElementById('selectedOrderInfo').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <strong>Order ID:</strong> ${order.order_id}<br>
                <strong>Customer:</strong> ${order.customer_name}<br>
                <strong>Date:</strong> ${order.created_at}
            </div>
            <div class="col-md-6">
                <strong>Total Amount:</strong> $${order.total}<br>
                <strong>Tracking Status:</strong> <span class="badge badge-info">Status ${order.tracking_status}</span>
            </div>
        </div>
    `;

    // Display order items with return checkboxes
    const itemsContainer = document.getElementById('orderItems');
    
    if (!order.products || order.products.length === 0) {
        itemsContainer.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> This order has no products to return.
                <br><small>The order may be incomplete or the products were not properly associated.</small>
            </div>
        `;
        document.getElementById('processReturnBtn').disabled = true;
        return;
    }
    
    itemsContainer.innerHTML = order.products.map(product => `
        <div class="card mb-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6>${product.product_name}</h6>
                        <small class="text-muted">Quantity: ${product.quantity} | Price: $${product.price}</small>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="return_quantity_${product.id}"
                               min="0" max="${product.quantity}" placeholder="Qty to return"
                               onchange="updateRefundAmount(${product.id}, ${product.price})">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="return_type_${product.id}">
                            <option value="resellable">Resellable</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    // Re-enable the process button if there are products
    document.getElementById('processReturnBtn').disabled = false;
}

function updateRefundAmount(productId, price) {
    const quantity = document.querySelector(`[name="return_quantity_${productId}"]`).value || 0;
    
    // Calculate total refund amount based on all selected items
    let total = 0;
    const order = window.returnableOrdersData.find(o => o.id == document.getElementById('orderSearch').value);
    
    if (order && order.products) {
        document.querySelectorAll('[name^="return_quantity_"]').forEach(input => {
            const qty = input.value || 0;
            if (qty > 0) {
                const id = input.name.replace('return_quantity_', '');
                const product = order.products.find(p => p.id == id);
                if (product) {
                    total += parseFloat(qty) * parseFloat(product.price);
                }
            }
        });
    }
    
    document.querySelector('[name="refund_amount"]').value = total.toFixed(2);
}

function processReturn() {
    const orderId = document.getElementById('orderSearch').value;
    if (!orderId) {
        alert('Please select an order');
        return;
    }

    // Validate return reason is selected
    const returnReason = document.querySelector('[name="return_reason"]').value;
    if (!returnReason) {
        alert('Please select a return reason');
        return;
    }

    // Validate refund amount is provided
    const refundAmount = document.querySelector('[name="refund_amount"]').value;
    if (!refundAmount || refundAmount <= 0) {
        alert('Please provide a valid refund amount');
        return;
    }

    // Collect return items data
    const returnItems = [];
    document.querySelectorAll('[name^="return_quantity_"]').forEach(input => {
        const quantity = input.value;
        if (quantity > 0) {
            const productId = input.name.replace('return_quantity_', '');
            const type = document.querySelector(`[name="return_type_${productId}"]`).value;
            returnItems.push({
                order_product_id: productId,
                quantity: parseInt(quantity),
                type: type,
                reason: returnReason
            });
        }
    });

    if (returnItems.length === 0) {
        alert('Please select at least one item to return');
        return;
    }

    // Check if CSRF token exists
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken ? csrfToken.content : ''
    };

    const requestData = {
        return_items: returnItems,
        return_reason: returnReason,
        refund_amount: refundAmount
    };

    fetch(`/returns/process/${orderId}`, {
        method: 'POST',
        body: JSON.stringify(requestData),
        headers: headers,
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                // If it's a validation error, show the validation messages
                if (err.errors) {
                    let errorMessage = 'Validation errors:\n';
                    for (const field in err.errors) {
                        errorMessage += `- ${field}: ${err.errors[field].join(', ')}\n`;
                    }
                    throw new Error(errorMessage);
                }
                throw new Error(err.message || 'Failed to process return');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message with refund amount
            alert('Return processed successfully! Refund amount: $' + (data.refund || '0.00'));
            // Reload the page to show the updated returns list
            location.reload();
        } else {
            alert('Error processing return: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing return: ' + error.message);
    });
}

function viewReturnDetails(returnId) {
    // Implement return details view
    alert('Return details functionality coming soon!');
}
</script>
@endsection