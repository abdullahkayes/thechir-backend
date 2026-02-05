@extends('layouts.admin')

@section('content')
<div class="container-fluid" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 20px 0;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                <div class="card-header bg-gradient-primary text-white border-0 py-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tachometer-alt fa-2x me-3"></i>
                        <h3 class="mb-0 fw-bold">Reseller, B2B & Distributer Dashboard</h3>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Statistics Cards - Responsive Grid -->
                    <div class="row g-4 mb-5">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card text-white h-100 shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-user-clock fa-3x mb-3 opacity-75"></i>
                                    <h6 class="card-title fw-bold mb-2">Pending Reseller Approvals</h6>
                                    <h1 class="display-5 fw-bold mb-0">{{ $pendingResellers->count() }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card text-white h-100 shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-building fa-3x mb-3 opacity-75"></i>
                                    <h6 class="card-title fw-bold mb-2">Pending B2B Approvals</h6>
                                    <h1 class="display-5 fw-bold mb-0">{{ $pendingB2b->count() }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card text-white h-100 shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-users fa-3x mb-3 opacity-75"></i>
                                    <h6 class="card-title fw-bold mb-2">Total Resellers</h6>
                                    <h1 class="display-5 fw-bold mb-0">{{ $resellers->count() }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card text-white h-100 shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-handshake fa-3x mb-3 opacity-75"></i>
                                    <h6 class="card-title fw-bold mb-2">Total B2B</h6>
                                    <h1 class="display-5 fw-bold mb-0">{{ $b2bs->count() }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card text-white h-100 shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-truck fa-3x mb-3 opacity-75"></i>
                                    <h6 class="card-title fw-bold mb-2">Pending Distributer Approvals</h6>
                                    <h1 class="display-5 fw-bold mb-0">{{ $pendingDistributers->count() }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card text-white h-100 shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-cogs fa-3x mb-3 opacity-75"></i>
                                    <h6 class="card-title fw-bold mb-2">Total Distributers</h6>
                                    <h1 class="display-5 fw-bold mb-0 text-dark">{{ $distributers->count() }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card text-white h-100 shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-dollar-sign fa-3x mb-3 opacity-75"></i>
                                    <h6 class="card-title fw-bold mb-2">Pending Payouts</h6>
                                    <h1 class="display-5 fw-bold mb-0">{{ $pendingPayoutRequests->count() }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card text-white h-100 shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-box fa-3x mb-3 opacity-75"></i>
                                    <h6 class="card-title fw-bold mb-2">Pending Amazon Approvals</h6>
                                    <h1 class="display-5 fw-bold mb-0">{{ $pendingAmazon->count() }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card text-white h-100 shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-amazon fa-3x mb-3 opacity-75"></i>
                                    <h6 class="card-title fw-bold mb-2">Total Amazon Users</h6>
                                    <h1 class="display-5 fw-bold mb-0">{{ $amazons->count() }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Approvals Section - Responsive -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <h4 class="mb-4 text-primary fw-bold"><i class="fas fa-clipboard-check me-2"></i>Pending Approvals</h4>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-user-clock me-2"></i>Pending Reseller Approvals</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="d-none d-sm-table-cell">Name</th>
                                                    <th>Email</th>
                                                    <th class="d-none d-md-table-cell">Phone</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pendingResellers as $reseller)
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">{{ $reseller->name }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="d-sm-none fw-bold">{{ $reseller->name }}</span>
                                                            <small>{{ $reseller->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ $reseller->phone }}</td>
                                                    <td>
                                                        <div class="btn-group-vertical btn-group-sm d-block d-sm-inline-block" role="group">
                                                            <form action="{{ route('admin.approve.reseller', $reseller->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm w-100 mb-1">Approve</button>
                                                            </form>
                                                            <form action="{{ route('admin.reject.reseller', $reseller->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-sm w-100">Reject</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">No pending resellers</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-building me-2"></i>Pending B2B Approvals</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="d-none d-sm-table-cell">Name</th>
                                                    <th>Email</th>
                                                    <th class="d-none d-md-table-cell">Business</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pendingB2b as $b2b)
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">{{ $b2b->name }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="d-sm-none fw-bold">{{ $b2b->name }}</span>
                                                            <small>{{ $b2b->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ Str::limit($b2b->business_name, 15) }}</td>
                                                    <td>
                                                        <div class="btn-group-vertical btn-group-sm d-block d-sm-inline-block" role="group">
                                                            <form action="{{ route('admin.approve.b2b', $b2b->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm w-100 mb-1">Approve</button>
                                                            </form>
                                                            <form action="{{ route('admin.reject.b2b', $b2b->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-sm w-100">Reject</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">No pending B2B users</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-truck me-2"></i>Pending Distributer Approvals</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="d-none d-sm-table-cell">Name</th>
                                                    <th>Email</th>
                                                    <th class="d-none d-md-table-cell">Company</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pendingDistributers as $distributer)
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">{{ $distributer->name }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="d-sm-none fw-bold">{{ $distributer->name }}</span>
                                                            <small>{{ $distributer->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ Str::limit($distributer->company_name, 15) }}</td>
                                                    <td>
                                                        <div class="btn-group-vertical btn-group-sm d-block d-sm-inline-block" role="group">
                                                            <form action="{{ route('admin.approve.distributer', $distributer->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm w-100 mb-1">Approve</button>
                                                            </form>
                                                            <form action="{{ route('admin.reject.distributer', $distributer->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-sm w-100">Reject</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">No pending distributers</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-box me-2"></i>Pending Amazon Approvals</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="d-none d-sm-table-cell">Name</th>
                                                    <th>Email</th>
                                                    <th class="d-none d-md-table-cell">Business</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pendingAmazon as $amazon)
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">{{ $amazon->amazon_name }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="d-sm-none fw-bold">{{ $amazon->amazon_name }}</span>
                                                            <small>{{ $amazon->amazon_email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ Str::limit($amazon->business_name, 15) }}</td>
                                                    <td>
                                                        <div class="btn-group-vertical btn-group-sm d-block d-sm-inline-block" role="group">
                                                            <form action="{{ route('admin.approve.amazon', $amazon->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm w-100 mb-1">Approve</button>
                                                            </form>
                                                            <form action="{{ route('admin.reject.amazon', $amazon->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-sm w-100">Reject</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">No pending Amazon users</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Payout Requests -->
                        <div class="col-12">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-dollar-sign me-2"></i>Pending Payout Requests</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="d-none d-sm-table-cell">Reseller</th>
                                                    <th>Email</th>
                                                    <th>Amount</th>
                                                    <th class="d-none d-md-table-cell">Requested</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pendingPayoutRequests as $request)
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">{{ $request->reseller->name }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="d-sm-none fw-bold">{{ $request->reseller->name }}</span>
                                                            <small>{{ $request->reseller->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">${{ number_format($request->amount, 2) }}</strong>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ $request->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <div class="btn-group-vertical btn-group-sm d-block d-sm-inline-block" role="group">
                                                            <form action="{{ route('admin.approve.payout', $request->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm w-100 mb-1">Approve</button>
                                                            </form>
                                                            <form action="{{ route('admin.reject.payout', $request->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-sm w-100">Reject</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">No pending payout requests</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Payment Updates -->
                        <div class="col-12">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-credit-card me-2"></i>Pending Payment Updates</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Amazon Seller</th>
                                                    <th>Amount</th>
                                                    <th class="d-none d-md-table-cell">Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pendingPaymentUpdates ?? [] as $update)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $update->order->order_id ?? 'N/A' }}</strong>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="d-sm-none fw-bold">{{ $update->amazon->business_name ?? 'N/A' }}</span>
                                                            <small>{{ $update->amazon->email ?? 'N/A' }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">${{ number_format($update->order->total ?? 0, 2) }}</strong>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ $update->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <form action="{{ route('admin.approve.payment.update', $update->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm w-100">Approve</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">No pending payment updates</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Activity Overview - Responsive -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <h4 class="mb-4 text-primary fw-bold"><i class="fas fa-chart-line me-2"></i>User Activity Overview</h4>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-users me-2"></i>Approved Resellers</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="d-none d-sm-table-cell">Name</th>
                                                    <th>Email</th>
                                                    <th class="d-none d-md-table-cell">Orders</th>
                                                    <th>Commissions</th>
                                                    <th>Payout</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($resellers->where('status', 'approved') as $reseller)
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">{{ $reseller->name }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="d-sm-none fw-bold">{{ $reseller->name }}</span>
                                                            <small>{{ $reseller->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ $reseller->orders->count() }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $reseller->commissions->count() }}</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">No approved resellers</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-handshake me-2"></i>Approved B2B Users</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="d-none d-sm-table-cell">Name</th>
                                                    <th>Email</th>
                                                    <th class="d-none d-md-table-cell">Business</th>
                                                    <th>Orders</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($b2bs->where('status', 'approved') as $b2b)
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">{{ $b2b->name }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="d-sm-none fw-bold">{{ $b2b->name }}</span>
                                                            <small>{{ $b2b->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ Str::limit($b2b->business_name, 12) }}</td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $b2b->orders->count() }}</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">No approved B2B users</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-cogs me-2"></i>Approved Distributers</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="d-none d-sm-table-cell">Name</th>
                                                    <th>Email</th>
                                                    <th class="d-none d-md-table-cell">Company</th>
                                                    <th>Orders</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($distributers->where('status', 'approved') as $distributer)
                                                <tr>
                                                    <td class="d-none d-sm-table-cell">{{ $distributer->name }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="d-sm-none fw-bold">{{ $distributer->name }}</span>
                                                            <small>{{ $distributer->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ Str::limit($distributer->company_name, 12) }}</td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $distributer->orders->count() }}</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">No approved distributers</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Detailed B2B Users Table - Responsive -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <h5 class="mb-0 fw-bold"><i class="fas fa-building me-2"></i>All B2B Users Details</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="d-none d-lg-table-cell">ID</th>
                                                    <th>Name</th>
                                                    <th class="d-none d-md-table-cell">Email</th>
                                                    <th class="d-none d-lg-table-cell">Business</th>
                                                    <th class="d-none d-xl-table-cell">EIN</th>
                                                    <th class="d-none d-lg-table-cell">Address</th>
                                                    <th>Status</th>
                                                    <th class="d-none d-md-table-cell">Certificate</th>
                                                    <th class="d-none d-sm-table-cell">Registered</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($allB2bUsers as $b2b)
                                                <tr>
                                                    <td class="d-none d-lg-table-cell">{{ $b2b->id }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <strong>{{ $b2b->name }}</strong>
                                                            <small class="d-md-none text-muted">{{ $b2b->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ $b2b->email }}</td>
                                                    <td class="d-none d-lg-table-cell">{{ Str::limit($b2b->business_name, 20) }}</td>
                                                    <td class="d-none d-xl-table-cell">{{ $b2b->ein }}</td>
                                                    <td class="d-none d-lg-table-cell">
                                                        <small title="{{ $b2b->shipping_address }}">
                                                            {{ Str::limit($b2b->shipping_address, 25) }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $b2b->status == 'approved' ? 'success' : ($b2b->status == 'pending' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($b2b->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        @if($b2b->resale_certificate_path)
                                                            <div class="btn-group btn-group-sm d-flex" role="group">
                                                                <a href="{{ route('admin.view.resale.certificate', $b2b->id) }}"
                                                                   class="btn btn-outline-info btn-sm flex-fill"
                                                                   target="_blank"
                                                                   title="View Certificate">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                                <a href="{{ route('admin.download.resale.certificate', $b2b->id) }}"
                                                                   class="btn btn-outline-success btn-sm flex-fill"
                                                                   title="Download Certificate">
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
                                                            </div>
                                                        @else
                                                            <span class="text-muted small">No cert</span>
                                                        @endif
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">{{ $b2b->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        @if($b2b->status == 'pending')
                                                            <div class="btn-group btn-group-sm d-flex" role="group">
                                                                <form action="{{ route('admin.approve.b2b', $b2b->id) }}" method="POST" class="flex-fill">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                                                        <i class="fas fa-check"></i> Approve
                                                                    </button>
                                                                </form>
                                                                <form action="{{ route('admin.reject.b2b', $b2b->id) }}" method="POST" class="flex-fill">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                                                        <i class="fas fa-times"></i> Reject
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @elseif($b2b->status == 'approved')
                                                            <span class="text-success"><i class="fas fa-check"></i> Approved</span>
                                                        @else
                                                            <span class="text-danger"><i class="fas fa-times"></i> Rejected</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-users fa-2x mb-2"></i>
                                                            <br>No B2B users found
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Distributer Users Table - Responsive -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-4" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                    <h5 class="mb-0 fw-bold"><i class="fas fa-truck me-2"></i>All Distributer Users Details</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="d-none d-lg-table-cell">ID</th>
                                                    <th>Name</th>
                                                    <th class="d-none d-md-table-cell">Email</th>
                                                    <th class="d-none d-lg-table-cell">Company</th>
                                                    <th class="d-none d-xl-table-cell">License</th>
                                                    <th class="d-none d-lg-table-cell">Address</th>
                                                    <th>Status</th>
                                                    <th class="d-none d-sm-table-cell">Registered</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($allDistributerUsers as $distributer)
                                                <tr>
                                                    <td class="d-none d-lg-table-cell">{{ $distributer->id }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <strong>{{ $distributer->name }}</strong>
                                                            <small class="d-md-none text-muted">{{ $distributer->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ $distributer->email }}</td>
                                                    <td class="d-none d-lg-table-cell">{{ Str::limit($distributer->company_name, 20) }}</td>
                                                    <td class="d-none d-xl-table-cell">{{ $distributer->license_number }}</td>
                                                    <td class="d-none d-lg-table-cell">
                                                        <small title="{{ $distributer->address }}">
                                                            {{ Str::limit($distributer->address, 25) }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $distributer->status == 'approved' ? 'success' : ($distributer->status == 'pending' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($distributer->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">{{ $distributer->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        @if($distributer->status == 'pending')
                                                            <div class="btn-group btn-group-sm d-flex" role="group">
                                                                <form action="{{ route('admin.approve.distributer', $distributer->id) }}" method="POST" class="flex-fill">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                                                        <i class="fas fa-check"></i> Approve
                                                                    </button>
                                                                </form>
                                                                <form action="{{ route('admin.reject.distributer', $distributer->id) }}" method="POST" class="flex-fill">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                                                        <i class="fas fa-times"></i> Reject
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @elseif($distributer->status == 'approved')
                                                            <span class="text-success"><i class="fas fa-check"></i> Approved</span>
                                                        @else
                                                            <span class="text-danger"><i class="fas fa-times"></i> Rejected</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-truck fa-2x mb-2"></i>
                                                            <br>No distributer users found
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Overview - Responsive -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <h4 class="mb-4 text-primary fw-bold"><i class="fas fa-shopping-cart me-2"></i>Recent Orders by User Type</h4>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-users me-2"></i>Reseller Orders</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th class="d-none d-sm-table-cell">Reseller</th>
                                                    <th>Total</th>
                                                    <th class="d-none d-md-table-cell">Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($resellerOrders->take(10) as $order)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $order->order_id }}</strong>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <small>{{ $order->reseller ? Str::limit($order->reseller->name, 15) : 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">${{ number_format($order->total, 2) }}</strong>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        <small>{{ $order->created_at->format('M d') }}</small>
                                                    </td>
                                                    <td>
                                                        @if($order->amazon_id)
                                                            @if($order->payment_status == 'paid')
                                                                <span class="badge bg-success small">Paid</span>
                                                            @elseif($order->payment_status == 'pending')
                                                                <span class="badge bg-warning small">Pending</span>
                                                            @else
                                                                <span class="badge bg-secondary small">{{ ucfirst($order->payment_status ?: 'pending') }}</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-{{ $order->status == 1 ? 'warning' : ($order->status == 2 ? 'info' : 'success') }} small">
                                                                {{ $order->status == 1 ? 'Pending' : ($order->status == 2 ? 'Processing' : 'Completed') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.download.invoice', $order->order_id) }}"
                                                           class="btn btn-outline-primary btn-sm"
                                                           target="_blank"
                                                           title="Download Invoice">
                                                            <i class="fas fa-download"></i> Invoice
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-3">
                                                        <div class="text-muted">
                                                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                                            <br>No reseller orders
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-building me-2"></i>B2B Orders</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th class="d-none d-sm-table-cell">Business</th>
                                                    <th>Total</th>
                                                    <th class="d-none d-md-table-cell">Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($b2bOrders->take(10) as $order)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $order->order_id }}</strong>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <small>{{ $order->b2b ? Str::limit($order->b2b->business_name, 15) : 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">${{ number_format($order->total, 2) }}</strong>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        <small>{{ $order->created_at->format('M d') }}</small>
                                                    </td>
                                                    <td>
                                                        @if($order->amazon_id)
                                                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }} small">
                                                                {{ ucfirst($order->payment_status ?: 'pending') }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-{{ $order->status == 1 ? 'warning' : ($order->status == 2 ? 'info' : 'success') }} small">
                                                                {{ $order->status == 1 ? 'Pending' : ($order->status == 2 ? 'Processing' : 'Completed') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.download.invoice', $order->order_id) }}"
                                                           class="btn btn-outline-primary btn-sm"
                                                           target="_blank"
                                                           title="Download Invoice">
                                                            <i class="fas fa-download"></i> Invoice
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-3">
                                                        <div class="text-muted">
                                                            <i class="fas fa-building fa-2x mb-2"></i>
                                                            <br>No B2B orders
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-truck me-2"></i>Distributer Orders</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th class="d-none d-sm-table-cell">Company</th>
                                                    <th>Total</th>
                                                    <th class="d-none d-md-table-cell">Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($distributerOrders->take(10) as $order)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $order->order_id }}</strong>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <small>{{ $order->distributer ? Str::limit($order->distributer->company_name, 15) : 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">${{ number_format($order->total, 2) }}</strong>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        <small>{{ $order->created_at->format('M d') }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $order->status == 1 ? 'warning' : ($order->status == 2 ? 'info' : 'success') }} small">
                                                            {{ $order->status == 1 ? 'Pending' : ($order->status == 2 ? 'Processing' : 'Completed') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.download.invoice', $order->order_id) }}"
                                                           class="btn btn-outline-primary btn-sm"
                                                           target="_blank"
                                                           title="Download Invoice">
                                                            <i class="fas fa-download"></i> Invoice
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-3">
                                                        <div class="text-muted">
                                                            <i class="fas fa-truck fa-2x mb-2"></i>
                                                            <br>No distributer orders
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

              

                    <!-- Amazon Orders Section - Responsive -->
                    <div class="row g-4 mb-5">
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-box me-2"></i>Amazon Orders</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th class="d-none d-sm-table-cell">Business</th>
                                                    <th>Total</th>
                                                    <th class="d-none d-md-table-cell">Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($amazonOrders->take(10) as $order)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $order->order_id }}</strong>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <small>{{ $order->amazon ? Str::limit($order->amazon->business_name, 15) : 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">${{ number_format($order->total, 2) }}</strong>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        <small>{{ $order->created_at->format('M d') }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $order->status == 1 ? 'warning' : ($order->status == 2 ? 'info' : 'success') }} small">
                                                            {{ $order->status == 1 ? 'Pending' : ($order->status == 2 ? 'Processing' : 'Completed') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.download.invoice', $order->order_id) }}"
                                                           class="btn btn-outline-primary btn-sm"
                                                           target="_blank"
                                                           title="Download Invoice">
                                                            <i class="fas fa-download"></i> Invoice
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-3">
                                                        <div class="text-muted">
                                                            <i class="fas fa-box fa-2x mb-2"></i>
                                                            <br>No Amazon orders
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Amazon Users Table - Responsive -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-header text-white border-0 py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <h5 class="mb-0 fw-bold"><i class="fas fa-amazon me-2"></i>All Amazon Users Details</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="d-none d-lg-table-cell">ID</th>
                                                    <th>Name</th>
                                                    <th class="d-none d-md-table-cell">Email</th>
                                                    <th class="d-none d-lg-table-cell">Business</th>
                                                    <th class="d-none d-xl-table-cell">EIN</th>
                                                    <th class="d-none d-lg-table-cell">Address</th>
                                                    <th>Status</th>
                                                    <th class="d-none d-sm-table-cell">Registered</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($allAmazonUsers as $amazon)
                                                <tr>
                                                    <td class="d-none d-lg-table-cell">{{ $amazon->id }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <strong>{{ $amazon->name }}</strong>
                                                            <small class="d-md-none text-muted">{{ $amazon->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ $amazon->email }}</td>
                                                    <td class="d-none d-lg-table-cell">{{ Str::limit($amazon->business_name, 20) }}</td>
                                                    <td class="d-none d-xl-table-cell">{{ $amazon->ein }}</td>
                                                    <td class="d-none d-lg-table-cell">
                                                        <small title="{{ $amazon->shipping_address }}">
                                                            {{ Str::limit($amazon->shipping_address, 25) }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $amazon->status == 'approved' ? 'success' : ($amazon->status == 'pending' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($amazon->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">{{ $amazon->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        @if($amazon->status == 'pending')
                                                            <div class="btn-group btn-group-sm d-flex" role="group">
                                                                <form action="{{ route('admin.approve.amazon', $amazon->id) }}" method="POST" class="flex-fill">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                                                        <i class="fas fa-check"></i> Approve
                                                                    </button>
                                                                </form>
                                                                <form action="{{ route('admin.reject.amazon', $amazon->id) }}" method="POST" class="flex-fill">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                                                        <i class="fas fa-times"></i> Reject
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @elseif($amazon->status == 'approved')
                                                            <span class="text-success"><i class="fas fa-check"></i> Approved</span>
                                                        @else
                                                            <span class="text-danger"><i class="fas fa-times"></i> Rejected</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-box fa-2x mb-2"></i>
                                                            <br>No Amazon users found
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approved Amazon Users Section - Responsive -->
                    <div class="col-12 col-lg-4">
                        <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
                            <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-amazon me-2"></i>Approved Amazon Users</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="d-none d-sm-table-cell">Name</th>
                                                <th>Email</th>
                                                <th class="d-none d-md-table-cell">Business</th>
                                                <th>Orders</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($amazons->where('status', 'approved') as $amazon)
                                            <tr>
                                                <td class="d-none d-sm-table-cell">{{ $amazon->name }}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="d-sm-none fw-bold">{{ $amazon->name }}</span>
                                                        <small>{{ $amazon->email }}</small>
                                                    </div>
                                                </td>
                                                <td class="d-none d-md-table-cell">{{ Str::limit($amazon->business_name, 12) }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $amazon->orders->count() }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">No approved Amazon users</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Downloads Section - Responsive -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <h4 class="mb-4 text-primary fw-bold"><i class="fas fa-download me-2"></i>Report Downloads</h4>
                            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                                <div class="card-body p-4">
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-6 col-md-4">
                                            <a href="{{ route('analytics.export') }}" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-chart-bar me-2"></i>
                                                <span>Analytics Report</span>
                                            </a>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4">
                                            <a href="{{ route('admin.export.commission.report') }}" class="btn btn-secondary w-100 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-dollar-sign me-2"></i>
                                                <span>Commission Report</span>
                                            </a>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4">
                                            <a href="{{ route('admin.export.buy.report') }}" class="btn btn-info w-100 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-shopping-cart me-2"></i>
                                                <span>Buy Report</span>
                                            </a>
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