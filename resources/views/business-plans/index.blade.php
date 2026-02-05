@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Business Plans</h1>
    <a href="{{ route('business-plans.create') }}" class="btn btn-primary">Create New Business Plan</a>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Target Quantity</th>
                    <th>Actual Quantity</th>
                    <th class="d-none d-lg-table-cell">Remaining Quantity</th>
                    <th>Target Amount</th>
                    <th>Actual Amount</th>
                    <th class="d-none d-lg-table-cell">Remaining Amount</th>
                    <th class="d-none d-md-table-cell">Start Date</th>
                    <th class="d-none d-md-table-cell">End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plansWithAchievements as $plan)
                <tr>
                    <td>{{ $plan->product->product_name ?? 'N/A' }}</td>
                    <td>{{ $plan->target_quantity }}</td>
                    <td>{{ $plan->actual_quantity }}</td>
                    <td class="d-none d-lg-table-cell">{{ $plan->remaining_quantity }}</td>
                    <td>{{ $plan->target_amount }}</td>
                    <td>{{ $plan->actual_amount }}</td>
                    <td class="d-none d-lg-table-cell">{{ $plan->remaining_amount }}</td>
                    <td class="d-none d-md-table-cell">{{ $plan->start_date }}</td>
                    <td class="d-none d-md-table-cell">{{ $plan->end_date }}</td>
                    <td>
                        <a href="{{ route('business-plans.show', $plan) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('business-plans.edit', $plan) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('business-plans.destroy', $plan) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
