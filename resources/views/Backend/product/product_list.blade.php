@extends('layouts.admin')
@section('content')
@can('Product_access')
<div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>Product List</h2>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>SL</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Poduct Image</th>
                            <th>Action</th>
                        </tr>
                           @foreach ($products as $index=>$product )
                               <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>
                                    <img src="{{ $product->preview }}" alt="">
                                </td>
                                <td>
                                    @can('Product_delete')
                                    <a href="{{ route('product.delete',$product->id) }}" class="btn btn-danger">Delete</a>
                                    @endcan
                                    <a href="{{ route('product.view',$product->id) }}" class="btn btn-success">View</a>
                                    <a href="{{ route('inventory',$product->id) }}" class="btn btn-primary">Inventory</a>
                                </td>
                               </tr>
                           @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endcan

@endsection
