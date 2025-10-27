@extends('layouts.admin')
@section('content')
@can('Add_color')
 <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4>Color List</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>SL</th>
                            <th>Color Name</th>
                            <th>Color</th>
                            <th>Action</th>
                        </tr>
                        @foreach ($colors as $index=>$color)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $color->color_name }}</td>
                                <td><div class="div" style=" width: 30px; height: 30px; border-radius: 50%;  background: {{ $color->color_code }}"></div></td>
                                 <td>
                                    <a href="{{ route('color.delete',$color->id) }}" class="btn btn-danger">Delete</a>
                                 </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

        </div>
        <div class="col-lg-4">
            <form action="{{ route('color.add') }}" method="post">
             @csrf
             <div class="card">
                <div class="card-header">
                    <h3>Add Color</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Color Name</label>
                        <input type="text" name="color_name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color Code</label>
                        <input type="text" name="color_code" class="form-control">
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Add Color</button>
                    </div>
                </div>
            </div>
            </form>

        </div>
    </div>
@endcan

@endsection
