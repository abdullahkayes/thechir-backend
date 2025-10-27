@extends('layouts.admin');
@section('content')
@can('Coupon_access')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            @if (session('error'))
            <div style="color: red; font:16px">{{ session('error') }}</div>
       @endif
            <div class="card-header"><h4>Coupon List</h4></div>
            <div class="card-body">
                <table class="table table-bordered">
              <tr>
                <th>SL</th>
                <th>Coupon Name</th>
                <th>Amount</th>
                <th>Validity</th>
                <th>Action</th>
              </tr>
              @foreach ($coupons as $index=>$coupon )
        <tr>
            <td>{{ $index+1 }}</td>
            <td>{{ $coupon->coupon }}</td>
            <td>{{ $coupon->amount }}</td>
            <td>{{ $coupon->validity }}</td>
            <td><a href="{{ route('coupon.delete',$coupon->id) }}" class="btn btn-danger">Delete</a></td>
        </tr>
              @endforeach
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h4>Add Coupon</h4></div>
            <div class="card-body">
                @if (session('success'))
                     <div style="color: green">{{ session('success') }}</div>
                @endif
                <form action="{{ route('coupon.add') }}" method="POST">
                 @csrf
                    <div class="mb-3">
                       <label class="form-label">Coupon</label>
                       <input type="text" name="coupon" class="form-control">
                    </div>
                    <div class="mb-3">
                       <label class="form-label">Amount</label>
                       <input type="text" name="amount" class="form-control">
                    </div>
                    <div class="mb-3">
                       <label class="form-label">Validity</label>
                       <input type="date" name="validity" class="form-control">
                    </div>
                    <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Add Coupon</button>
                    </div>
                  </form>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection
