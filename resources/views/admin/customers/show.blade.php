@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Customer Details</h1>
            <a href="{{ route('customers.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Customers
            </a>
        </div>

        <!-- Customer Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ $customer->name }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Email Address:</label>
                            <p class="form-control-static">{{ $customer->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Created At:</label>
                            <p class="form-control-static">{{ $customer->created_at->format('M d, Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Address:</label>
                    <p class="form-control-static">{{ $customer->address ?: 'No address provided' }}</p>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Last Updated:</label>
                    <p class="form-control-static">{{ $customer->updated_at->format('M d, Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
            </div>
            <div class="card-body">
                <div class="btn-group" role="group" aria-label="Customer Actions">
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Customer
                    </a>
                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this customer? This action cannot be undone.')">
                            <i class="fas fa-trash"></i> Delete Customer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Customer Orders (Optional) -->
        @if($customer->orders->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Customer Orders ({{ $customer->orders->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td>
                                    @if($order->orderTracking)
                                        @if($order->orderTracking->status == 0)
                                            <span class="badge badge-secondary">Pending</span>
                                        @elseif($order->orderTracking->status == 1)
                                            <span class="badge badge-warning">Processing</span>
                                        @elseif($order->orderTracking->status == 2)
                                            <span class="badge badge-info">Shipped</span>
                                        @elseif($order->orderTracking->status == 3)
                                            <span class="badge badge-success">Delivered</span>
                                        @endif
                                    @else
                                        <span class="badge badge-secondary">No Status</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('order', $order->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
