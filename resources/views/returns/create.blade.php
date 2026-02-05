@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Process Return for Order #{{ $order->order_id }}</h1>
                <div>
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Returns
                    </a>
                    <a href="{{ route('order') }}?order={{ $order->id }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> View Order
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Order ID:</strong></td>
                                <td>{{ $order->order_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Customer:</strong></td>
                                <td>{{ $order->customer->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Order Date:</strong></td>
                                <td>{{ $order->created_at ? $order->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td>${{ number_format($order->total, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Current Status:</strong></td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $order->orderTracking->status == 3 ? 'success' : 
                                        ($order->orderTracking->status == 4 ? 'warning' : 'info')
                                    }}">
                                        {{ $order->orderTracking->status == 3 ? 'Delivered' : 
                                           ($order->orderTracking->status == 4 ? 'Returned' : 'Processing') }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $order->customer->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $order->customer->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $order->customer->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td>{{ $order->customer->address ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Return Items Selection</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('returns.process', $order) }}" method="POST" id="returnForm">
                @csrf
                
                <!-- Order Products -->
                <div class="mb-4">
                    <h5>Select Items to Return</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">
                                        <input type="checkbox" id="selectAll" onchange="toggleAllItems()">
                                    </th>
                                    <th>Product</th>
                                    <th>Quantity Ordered</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                    <th>Return Quantity</th>
                                    <th>Return Type</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderProducts as $item)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="item-checkbox" value="{{ $item->id }}" 
                                               onchange="updateReturnSummary()">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('upload/product') }}/{{ $item->product->image }}" 
                                                     alt="{{ $item->product->product_name }}" 
                                                     class="img-thumbnail mr-2" style="width: 50px; height: 50px;">
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $item->product->product_name ?? 'Unknown Product' }}</h6>
                                                <small class="text-muted">{{ $item->product->sku ?? 'No SKU' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                    <td style="width: 120px;">
                                        <input type="number" class="form-control return-qty" 
                                               name="return_items[{{ $item->id }}][quantity]" 
                                               min="0" max="{{ $item->quantity }}" 
                                               value="0" onchange="validateQuantity(this, {{ $item->quantity }})"
                                               disabled>
                                    </td>
                                    <td style="width: 120px;">
                                        <select class="form-control return-type" 
                                                name="return_items[{{ $item->id }}][type]" disabled>
                                            <option value="resellable">Resellable</option>
                                            <option value="damaged">Damaged</option>
                                        </select>
                                    </td>
                                    <td style="width: 150px;">
                                        <select class="form-control return-reason" 
                                                name="return_items[{{ $item->id }}][reason]" disabled>
                                            <option value="">Select reason</option>
                                            <option value="damaged">Damaged Product</option>
                                            <option value="wrong_item">Wrong Item Received</option>
                                            <option value="not_as_described">Not as Described</option>
                                            <option value="defective">Defective Product</option>
                                            <option value="customer_request">Customer Request</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Return Summary -->
                <div class="row mb-4" id="returnSummary" style="display: none;">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>Return Summary</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Items to Return:</strong> <span id="itemsCount">0</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total Quantity:</strong> <span id="totalQuantity">0</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Refund Amount:</strong> $<span id="refundAmount">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Return Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="returnMethod">Return Method</label>
                            <select class="form-control" name="return_method" required>
                                <option value="">Select return method</option>
                                <option value="courier">Courier Pickup</option>
                                <option value="drop_off">Customer Drop-off</option>
                                <option value="mail">Mail Return</option>
                                <option value="store_return">Store Return</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="refundMethod">Refund Method</label>
                            <select class="form-control" name="refund_method" required>
                                <option value="">Select refund method</option>
                                <option value="original_payment">Original Payment Method</option>
                                <option value="store_credit">Store Credit</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash Refund</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="additionalNotes">Additional Notes</label>
                    <textarea class="form-control" name="additional_notes" rows="3" 
                              placeholder="Any additional information about the return..."></textarea>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="processReturnBtn" disabled>
                            <i class="fas fa-undo"></i> Process Return
                        </button>
                        <a href="{{ route('returns.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function toggleAllItems() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const qtyInputs = document.querySelectorAll('.return-qty');
    const typeSelects = document.querySelectorAll('.return-type');
    const reasonSelects = document.querySelectorAll('.return-reason');
    
    checkboxes.forEach((checkbox, index) => {
        checkbox.checked = selectAll.checked;
        qtyInputs[index].disabled = !selectAll.checked;
        typeSelects[index].disabled = !selectAll.checked;
        reasonSelects[index].disabled = !selectAll.checked;
        
        if (!selectAll.checked) {
            qtyInputs[index].value = 0;
        }
    });
    
    updateReturnSummary();
}

function validateQuantity(input, maxQuantity) {
    if (parseInt(input.value) > maxQuantity) {
        input.value = maxQuantity;
    }
    if (parseInt(input.value) < 0) {
        input.value = 0;
    }
    updateReturnSummary();
}

function updateReturnSummary() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const qtyInputs = document.querySelectorAll('.return-qty');
    const typeSelects = document.querySelectorAll('.return-type');
    
    let itemsCount = 0;
    let totalQuantity = 0;
    let refundAmount = 0;
    
    checkboxes.forEach((checkbox, index) => {
        if (checkbox.checked && parseInt(qtyInputs[index].value) > 0) {
            itemsCount++;
            totalQuantity += parseInt(qtyInputs[index].value);
            
            // Calculate refund amount (simplified - you might want to get actual prices from server)
            const row = checkbox.closest('tr');
            const unitPrice = parseFloat(row.cells[3].textContent.replace('$', '').replace(',', ''));
            refundAmount += parseInt(qtyInputs[index].value) * unitPrice;
        }
    });
    
    // Show/hide summary and enable/disable submit button
    const summary = document.getElementById('returnSummary');
    const submitBtn = document.getElementById('processReturnBtn');
    
    if (itemsCount > 0 && totalQuantity > 0) {
        summary.style.display = 'block';
        document.getElementById('itemsCount').textContent = itemsCount;
        document.getElementById('totalQuantity').textContent = totalQuantity;
        document.getElementById('refundAmount').textContent = refundAmount.toFixed(2);
        submitBtn.disabled = false;
    } else {
        summary.style.display = 'none';
        submitBtn.disabled = true;
    }
}

// Handle individual checkbox changes
document.querySelectorAll('.item-checkbox').forEach((checkbox, index) => {
    checkbox.addEventListener('change', function() {
        const qtyInput = document.querySelectorAll('.return-qty')[index];
        const typeSelect = document.querySelectorAll('.return-type')[index];
        const reasonSelect = document.querySelectorAll('.return-reason')[index];
        
        qtyInput.disabled = !this.checked;
        typeSelect.disabled = !this.checked;
        reasonSelect.disabled = !this.checked;
        
        if (!this.checked) {
            qtyInput.value = 0;
        }
        
        updateReturnSummary();
    });
});

// Form submission
document.getElementById('returnForm').addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const qtyInputs = document.querySelectorAll('.return-qty');
    
    let hasValidItems = false;
    
    checkboxes.forEach((checkbox, index) => {
        if (checkbox.checked && parseInt(qtyInputs[index].value) > 0) {
            hasValidItems = true;
        }
    });
    
    if (!hasValidItems) {
        e.preventDefault();
        alert('Please select at least one item to return with a valid quantity.');
        return false;
    }
});
</script>
@endsection