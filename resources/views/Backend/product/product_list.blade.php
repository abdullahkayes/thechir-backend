@extends('layouts.admin')
@section('content')
@can('Product_access')
 <div class="row">
         <div class="col-lg-12">
             <div class="card">
                 <div class="card-header">
                     <h2>Product List</h2>
                     <form method="GET" action="{{ route('product.list') }}" class="mt-3">
                         <div class="input-group">
                             <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by product name or SKU">
                             <div class="input-group-append">
                                 <button class="btn btn-primary" type="submit">Search</button>
                             </div>
                         </div>
                     </form>
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
                                    @can('Product_add')
                                    <a href="{{ route('product.edit',$product->id) }}" class="btn btn-warning">Edit</a>
                                    @endcan
                                    @can('Product_delete')
                                    <a href="{{ route('product.delete',$product->id) }}" class="btn btn-danger">Delete</a>
                                    @endcan
                                    <a href="{{ route('product.view',$product->id) }}" class="btn btn-success">View</a>
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
