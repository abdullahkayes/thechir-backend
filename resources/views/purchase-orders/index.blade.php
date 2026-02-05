@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Purchase Orders</h4>
                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">Create Purchase Order</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Supplier</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrders as $po)
                                <tr>
                                    <td>{{ $po->po_number }}</td>
                                    <td>{{ $po->supplier->name }}</td>
                                    <td>{{ $po->order_date }}</td>
                                    <td>
                                        <span class="badge badge-{{ $po->status == 'received' ? 'success' : ($po->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($po->status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($po->total_amount, 2) }}</td>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-info">View</a>
                                        @if($po->status == 'pending')
                                        <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        @endif
                                        @if($po->status == 'approved')
                                        <button class="btn btn-sm btn-success" onclick="receiveOrder({{ $po->id }})">Receive Stock</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $purchaseOrders->links() }}
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
            <form id="receiveForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="receiveItems"></div>
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
    // For simplicity, show a basic receive form
    // In a real application, you might want to fetch the PO details via AJAX
    $('#receiveForm').attr('action', `/purchase-orders/${poId}/receive`);
    let html = '<div class="form-group"><label>Received Date</label><input type="date" name="received_date" class="form-control" required></div>';
    html += '<p class="text-info">Stock will be added to inventory automatically upon submission.</p>';
    html += '<div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="3" placeholder="Optional notes about the receipt"></textarea></div>';

    $('#receiveItems').html(html);
    $('#receiveModal').modal('show');
}
</script>
@endsection
