
@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Business Plan Details</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Product: {{ $businessPlan->product->product_name ?? 'N/A' }}</h5>
            <p class="card-text">Target Quantity: {{ $businessPlan->target_quantity }}</p>
            <p class="card-text">Actual Quantity: {{ $businessPlan->actual_quantity }}</p>
            <p class="card-text">Remaining Quantity: {{ $businessPlan->remaining_quantity }}</p>
            <p class="card-text">Target Amount: {{ $businessPlan->target_amount }}</p>
            <p class="card-text">Actual Amount: {{ $businessPlan->actual_amount }}</p>
            <p class="card-text">Remaining Amount: {{ $businessPlan->remaining_amount }}</p>
            <p class="card-text">Start Date: {{ $businessPlan->start_date }}</p>
            <p class="card-text">End Date: {{ $businessPlan->end_date }}</p>
        </div>
    </div>
    <a href="{{ route('business-plans.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
