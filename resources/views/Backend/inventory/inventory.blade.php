@extends('layouts.admin')
@section('content')
@can('Add_size')
<div class="row">
    <div class="col-lg-8">
    <div class="card">
        <div class="card-header">
            <h2>Inventory List</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>SL</th>
                    <th>Color</th>
                    <th>Size</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Discount Price</th>
                    <th>Quaintity</th>
                    <th>Action</th>
                </tr>
                @foreach ($inventores as $index=>$inventory )
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td>
                            <div style=" width: 30px; height: 30px; border-radius: 50%; background-color: {{ $inventory->rel_to_col->color_code }} ;" ></div>
                        </td>
                        <td>{{ $inventory->rel_to_size->size_name }}</td>
                        <td>{{ $inventory->price }}</td>
                        <td>{{ $inventory->discount }}</td>
                        <td>{{ $inventory->discount_price }}</td>
                        <td>{{ $inventory->quaintity }}</td>
                        <td>
                            <a href="{{ route('inventory.delete',$inventory->id) }}" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    </div>
    <div class="col-lg-4">
        <form action="{{ route('inventory.store',$products->id) }}" method="post">
         @csrf
         <div class="card">
            <div class="card-header">
                <h3>Add Inventory</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="color_name" class="form-control" value="{{ $products->product_name }}">
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">Add Color</label>
                            <select name="color_id" id="category">
                                <option value="">Select color</option>
                                @foreach ($colors as $color )
                                    <option value="{{ $color->id }}">{{ $color->color_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-3">
                             <label class="form-label">Add Size</label>
                            <select name="size_id">
                                <option value="">Select Size</option>
                                @foreach ($sizes as $size )
                                    <option value="{{ $size->id }}">{{ $size->size_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="text" name="price" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Discount</label>
                    <input type="text" name="discount" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Quaintity</label>
                    <input type="text" name="quaintity" class="form-control">
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Update Inventory</button>
                </div>
            </div>
        </div>
        </form>

    </div>
</div>
@endcan


@endsection
