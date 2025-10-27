@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4>Size List</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>SL</th>
                            <th>Size</th>
                            <th>Action</th>
                        </tr>
                        @foreach ($sizes as $index=>$size)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $size->size_name }}</td>
                                <td>
                                    <a href="{{ route('size.delete',$size->id) }}" class="btn btn-danger"> Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            
        </div>
        <div class="col-lg-4">
            <form action="{{ route('size.add') }}" method="post">
             @csrf
             <div class="card">
                <div class="card-header">
                    <h3>Add Size</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Size Name</label>
                        <input type="text" name="size_name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Add Size</button>
                    </div>
                </div>
            </div>
            </form>
            
        </div>
    </div>
@endsection