@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Product Inventory</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('product-inventory.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select name="product_id" id="product_id" class="form-control" required>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="size_id">Size</label>
                            <select name="size_id" id="size_id" class="form-control">
                                <option value="">Select Size</option>
                                @foreach($sizes as $size)
                                <option value="{{ $size->id }}">{{ $size->size_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="color_id">Color</label>
                            <select name="color_id" id="color_id" class="form-control">
                                <option value="">Select Color</option>
                                @foreach($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->color_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="buy_price">Buy Price</label>
                            <input type="number" step="0.01" name="buy_price" id="buy_price" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="discount_price">Discount Price</label>
                            <input type="number" step="0.01" name="discount_price" id="discount_price" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="reseller_price">Reseller Price</label>
                            <input type="number" step="0.01" name="reseller_price" id="reseller_price" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="wholesale_price">Wholesale Price</label>
                            <input type="number" step="0.01" name="wholesale_price" id="wholesale_price" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="distributer_price">Distributer Price</label>
                            <input type="number" step="0.01" name="distributer_price" id="distributer_price" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="amazon_price">Amazon Price</label>
                            <input type="number" step="0.01" name="amazon_price" id="amazon_price" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="quantity">Stock Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="weight_grams">Weight (grams)</label>
                            <input type="number" step="0.01" name="weight_grams" id="weight_grams" class="form-control" placeholder="e.g., 250">
                            <small class="form-text text-muted">Required for accurate shipping cost calculation</small>
                        </div>
                        <div class="form-group">
                            <label for="manufacture_date">Manufacture Date</label>
                            <input type="date" name="manufacture_date" id="manufacture_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" class="form-control">
                            <small class="form-text text-muted">Leave empty if product doesn't expire</small>
                        </div>
                        <div class="form-group">
                            <label for="batch_number">Batch Number</label>
                            <input type="text" name="batch_number" id="batch_number" class="form-control" placeholder="e.g., BATCH-2025-001">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Inventory</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#product_id').select2({
        placeholder: 'Search for a product...',
        ajax: {
            url: '{{ route("product-inventory.product-suggestions") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });
});
</script>
@endsection
