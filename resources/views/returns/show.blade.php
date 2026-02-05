@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    Return Details - Order #{{ $order->order_id }}
                    @if($isReturned)
                        <span class="badge badge-warning ml-2">
                            <i class="fas fa-undo"></i> Returned
                        </span>
                    @else
                        <span class="badge badge-info ml-2">
                            <i class="fas fa-eye"></i> View Only
                        </span>
                    @endif
                </h1>
                <div>
                    @if(!$isReturned)
                        <a href="{{ route('returns.create', $order) }}" class="btn btn-warning">
                            <i class="fas fa-undo"></i> Process Return
                        </a>
                    @endif
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Returns
                    </a>
                    <a href="{{ route('order') }}?order={{ $order->id }}" class="btn btn-info">
                        <i class="fas fa-shopping-cart"></i> Order Management
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert for returned orders -->
    @if($isReturned)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading"><i class="fas fa-undo"></i> This Order Has Been Returned</h4>
                <p>This order has been processed for return. The items have been returned to inventory and refund has been processed.</p>
                <hr>
                <p class="mb-0">Return processed on: <strong>{{ $order->updated_at ? $order->updated_at->format('M d, Y H:i') : 'N/A' }}</strong></p>
            </div>
        </div>
    </div>
    @endif

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
                            @if($isReturned)
                            <tr>
                                <td><strong>Return Date:</strong></td>
                                <td>{{ $order->updated_at ? $order->updated_at->format('M d, Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Refund Status:</strong></td>
                                <td>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Processed
                                    </span>
                                </td>
                            </tr>
                            @endif
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

    <!-- Order Items -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Order Items
                @if($isReturned)
                    <small class="text-muted">(Returned Items)</small>
                @endif
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="orderItemsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                            @if($isReturned)
                            <th>Return Quantity</th>
                            <th>Return Type</th>
                            <th>Status</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderProducts as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset('upload/product') }}/{{ $item->product->image }}" 
                                             alt="{{ $item->product->product_name }}" 
                                             class="img-thumbnail mr-2" style="width: 50px; height: 50px;">
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $item->product->product_name ?? 'Unknown Product' }}</h6>
                                        <small class="text-muted">{{ $item->product->category->name ?? 'No Category' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->product->sku ?? 'No SKU' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                            @if($isReturned)
                            <td>
                                <span class="badge badge-warning">
                                    <i class="fas fa-undo"></i> {{ $item->quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <i class="fas fa-check-circle"></i> Resellable
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Returned
                                </span>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <th colspan="{{ $isReturned ? 4 : 3 }}">Total:</th>
                            <th>${{ number_format($order->total, 2) }}</th>
                            @if($isReturned)
                            <th colspan="3"></th>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Return Summary (for returned orders) -->
    @if($isReturned)
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Return Summary</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Items Returned:</strong></td>
                                <td>{{ $order->orderProducts->count() }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Quantity:</strong></td>
                                <td>{{ $order->orderProducts->sum('quantity') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Refund Amount:</strong></td>
                                <td class="text-success font-weight-bold">${{ number_format($order->total, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Return Method:</strong></td>
                                <td>Courier Pickup</td>
                            </tr>
                            <tr>
                                <td><strong>Refund Method:</strong></td>
                                <td>Original Payment</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Inventory Impact</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Inventory Updates</h6>
                        <p class="mb-2">The returned items have been added back to inventory:</p>
                        <ul class="mb-0">
                            @foreach($order->orderProducts as $item)
                            <li>{{ $item->product->product_name ?? 'Unknown' }}: +{{ $item->quantity }} units</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle"></i> Financial Impact</h6>
                        <p class="mb-0">COGS entry adjusted and revenue reversed in accounting records.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#orderItemsTable').DataTable({
        "pageLength": 10,
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [{{ $isReturned ? 5 : -1 }}] }
        ]
    });
});
</script>
@endsection