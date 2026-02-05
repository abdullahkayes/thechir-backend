@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Product Inventories</h3>
                    <a href="{{ route('product-inventory.create') }}" class="btn btn-primary">Add New Inventory</a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('product-inventory.index') }}" class="mb-3">
                        <div class="input-group">
                            <select name="search" id="search-select" class="form-control">
                                @if($search)
                                    <option value="{{ $search }}" selected>{{ $search }}</option>
                                @endif
                            </select>
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Buy Price</th>
                                    <th>Price</th>
                                    <th>Discount Price</th>
                                    <th>Quantity</th>
                                    <th>Weight (g)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventories as $inventory)
                                <tr>
                                    <td>{{ $inventory->id }}</td>
                                    <td>{{ $inventory->product ? $inventory->product->product_name : 'Product Not Found' }}</td>
                                    <td>{{ $inventory->size ? $inventory->size->size_name : 'N/A' }}</td>
                                    <td>{{ $inventory->color ? $inventory->color->color_name : 'N/A' }}</td>
                                    <td>${{ $inventory->buy_price ?? 'N/A' }}</td>
                                    <td>${{ $inventory->price }}</td>
                                    <td>${{ $inventory->discount_price ?? 'N/A' }}</td>
                                    <td>{{ $inventory->quantity }}</td>
                                    <td>{{ $inventory->weight_grams ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('product-inventory.edit', $inventory) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('product-inventory.destroy', $inventory) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No inventories found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#search-select').select2({
        placeholder: 'Search by product, size, or color...',
        allowClear: true,
        ajax: {
            url: '{{ route("product-inventory.search-suggestions") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item,
                            id: item
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });
});
</script>
@endsection
