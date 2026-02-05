@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Supplier Details</h1>
                <div>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Supplier
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Suppliers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Information -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Supplier Name:</strong></td>
                                <td>{{ $supplier->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Contact Person:</strong></td>
                                <td>{{ $supplier->contact_person ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $supplier->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $supplier->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Terms:</strong></td>
                                <td>{{ $supplier->payment_terms ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $supplier->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($supplier->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created At:</strong></td>
                                <td>{{ $supplier->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Updated At:</strong></td>
                                <td>{{ $supplier->updated_at->format('M d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Address & Notes</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label"><strong>Address:</strong></label>
                        <p>{{ $supplier->address ?? 'No address provided' }}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><strong>Notes:</strong></label>
                        <p>{{ $supplier->notes ?? 'No notes available' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Orders History -->
    @if($supplier->purchaseOrders->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Purchase Orders History ({{ $supplier->purchaseOrders->count() }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="purchaseOrdersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Expected Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplier->purchaseOrders as $purchaseOrder)
                        <tr>
                            <td>{{ $purchaseOrder->po_number }}</td>
                            <td>{{ $purchaseOrder->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge badge-{{ 
                                    $purchaseOrder->status === 'pending' ? 'warning' : 
                                    ($purchaseOrder->status === 'approved' ? 'info' : 
                                    ($purchaseOrder->status === 'received' ? 'success' : 'danger'))
                                }}">
                                    {{ ucfirst($purchaseOrder->status) }}
                                </span>
                            </td>
                            <td>{{ $purchaseOrder->items->count() }}</td>
                            <td>${{ number_format($purchaseOrder->total_amount, 2) }}</td>
                            <td>{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
            </div>
    </div>
    @else
    <div class="card shadow mb-4">
        <div class="card-body text-center">
            <h5 class="text-muted">No purchase orders found for this supplier</h5>
            <p>This supplier hasn't been used in any purchase orders yet.</p>
        </div>
    </div>
    @endif
</div>
@endsection