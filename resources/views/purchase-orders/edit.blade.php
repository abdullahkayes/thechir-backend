@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Purchase Order</h4>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <select name="supplier_id" class="form-control" required>
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $supplier->id == $purchaseOrder->supplier_id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Expected Date</label>
                                    <input type="date" name="expected_date" class="form-control" value="{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('Y-m-d') : '' }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" {{ $purchaseOrder->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $purchaseOrder->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="cancelled" {{ $purchaseOrder->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div id="items-container">
                            <h5>Order Items</h5>
                            @foreach($purchaseOrder->items as $index => $item)
                            <div class="item-row row mb-3">
                                <div class="col-md-4">
                                    <select name="items[{{ $index }}][product_id]" class="form-control product-select" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $product->id == $item->product_id ? 'selected' : '' }}>{{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control" placeholder="Quantity" min="1" value="{{ $item->quantity }}" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control" placeholder="Unit Price" min="0" value="{{ $item->unit_price }}" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.01" class="form-control total-price" value="{{ number_format($item->total_price, 2) }}" readonly>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-item">Remove</button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-item" class="btn btn-info">Add Item</button>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Update Purchase Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
let itemIndex = {{ count($purchaseOrder->items) }};

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const itemRow = document.createElement('div');
    itemRow.className = 'item-row row mb-3';
    itemRow.innerHTML = `
        <div class="col-md-4">
            <select name="items[${itemIndex}][product_id]" class="form-control product-select" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Quantity" min="1" required>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" class="form-control" placeholder="Unit Price" min="0" required>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" class="form-control total-price" readonly>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-item">Remove</button>
        </div>
    `;
    container.appendChild(itemRow);
    itemIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        e.target.closest('.item-row').remove();
    }
});

document.addEventListener('input', function(e) {
    if (e.target.name && e.target.name.includes('[quantity]') || e.target.name.includes('[unit_price]')) {
        const row = e.target.closest('.item-row');
        const quantity = row.querySelector('input[name*="[quantity]"]').value || 0;
        const unitPrice = row.querySelector('input[name*="[unit_price]"]').value || 0;
        const totalPrice = row.querySelector('.total-price');
        totalPrice.value = (quantity * unitPrice).toFixed(2);
    }
});
</script>
@endsection