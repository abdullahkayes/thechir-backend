@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Purchase Order Details</h4>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Back</a>
                    @if($purchaseOrder->status == 'pending')
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning">Edit</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>PO Number:</th>
                                    <td>{{ $purchaseOrder->po_number ?? $purchaseOrder->order_number }}</td>
                                </tr>
                                <tr>
                                    <th>Supplier:</th>
                                    <td>{{ $purchaseOrder->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <th>Order Date:</th>
                                    <td>{{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('Y-m-d') : $purchaseOrder->created_at->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>Expected Date:</th>
                                    <td>{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $purchaseOrder->status == 'received' ? 'success' : ($purchaseOrder->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($purchaseOrder->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td>${{ number_format($purchaseOrder->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Supplier Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $purchaseOrder->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $purchaseOrder->supplier->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $purchaseOrder->supplier->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>{{ $purchaseOrder->supplier->address ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h5>Order Items</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                    <th>Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td>{{ $item->product->product_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->total_price, 2) }}</td>
                                    <td>{{ $item->received_quantity ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($purchaseOrder->status == 'approved')
                    <div class="mt-4">
                        <button class="btn btn-success" onclick="receiveOrder({{ $purchaseOrder->id }})">Receive Stock</button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receive Stock Modal -->
<div class="modal fade" id="receiveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Receive Stock</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="receiveForm" action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Received Date</label>
                        <input type="date" name="received_date" class="form-control" required>
                    </div>
                    <div id="receiveItems">
                        @foreach($purchaseOrder->items as $item)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>{{ $item->product->product_name }}</label>
                                <input type="hidden" name="items[{{ $item->id }}][received_quantity]" value="{{ $item->quantity }}">
                            </div>
                            <div class="col-md-3">
                                <label>Received Quantity</label>
                                <input type="number" name="items[{{ $item->id }}][received_quantity]" class="form-control" placeholder="Received Quantity" max="{{ $item->quantity }}" required>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Receive Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function receiveOrder(poId) {
    $('#receiveModal').modal('show');
}
</script>
@endsection