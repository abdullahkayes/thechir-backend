@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">📱 QR Payment Management</h1>
            <p class="text-muted">Manage Venmo and Cash App payment submissions</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingPayments->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedPayments->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">⏳ Pending Payment Approvals</h6>
                </div>
                <div class="card-body">
                    @if($pendingPayments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Transaction ID</th>
                                    <th>Screenshot</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingPayments as $payment)
                                <tr>
                                    <td>#{{ $payment->id }}</td>
                                    <td>
                                        <strong>{{ $payment->customer_name }}</strong><br>
                                        <small class="text-muted">{{ $payment->customer_email }}</small>
                                    </td>
                                    <td>
                                        @if($payment->payment_type === 'venmo')
                                        <span class="badge bg-blue text-white">Venmo</span>
                                        @else
                                        <span class="badge bg-green text-white">Cash App</span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold">${{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                                    <td>
                                        @if($payment->screenshot_path)
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#screenshotModal{{ $payment->id }}">
                                            <i class="fas fa-image"></i> View
                                        </button>
                                        @else
                                        <span class="text-muted">No screenshot</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal{{ $payment->id }}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $payment->id }}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted">No pending payments to review!</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approved Payments -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">✅ Recently Approved Payments</h6>
                </div>
                <div class="card-body">
                    @if($approvedPayments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Approved Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedPayments as $payment)
                                <tr>
                                    <td>#{{ $payment->id }}</td>
                                    <td>{{ $payment->customer_name }}</td>
                                    <td>
                                        @if($payment->payment_type === 'venmo')
                                        <span class="badge bg-blue text-white">Venmo</span>
                                        @else
                                        <span class="badge bg-green text-white">Cash App</span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold">${{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->approved_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center">No approved payments yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Screenshot Modals -->
@foreach($pendingPayments as $payment)
@if($payment->screenshot_path)
<div class="modal fade" id="screenshotModal{{ $payment->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Screenshot - Order #{{ $payment->id }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ Storage::url($payment->screenshot_path) }}" alt="Payment Screenshot" class="img-fluid" style="max-width: 100%;">
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Approve Modals -->
@foreach($pendingPayments as $payment)
<div class="modal fade" id="approveModal{{ $payment->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success">Approve Payment #{{ $payment->id }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin.qr-payment.approve', $payment->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve this payment?</p>
                    <table class="table table-sm">
                        <tr>
                            <th>Customer:</th>
                            <td>{{ $payment->customer_name }}</td>
                        </tr>
                        <tr>
                            <th>Amount:</th>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td>{{ ucfirst($payment->payment_type) }}</td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <label>Admin Notes (optional):</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve & Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Reject Modals -->
@foreach($pendingPayments as $payment)
<div class="modal fade" id="rejectModal{{ $payment->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Reject Payment #{{ $payment->id }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin.qr-payment.reject', $payment->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-danger">Are you sure you want to reject this payment?</p>
                    <div class="form-group">
                        <label>Reason for rejection (required):</label>
                        <textarea name="admin_notes" class="form-control" rows="3" required placeholder="Please provide a reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
