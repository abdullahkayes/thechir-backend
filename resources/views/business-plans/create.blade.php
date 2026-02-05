
@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Create Business Plan</h1>
    <form action="{{ route('business-plans.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="target_quantity">Target Quantity</label>
            <input type="number" name="target_quantity" id="target_quantity" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="target_amount">Target Amount</label>
            <input type="number" step="0.01" name="target_amount" id="target_amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('business-plans.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
