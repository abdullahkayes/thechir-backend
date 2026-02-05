@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3>Banner Down List</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>sl</th>
                            <th>Banner ID</th>
                            <th>Sub Title</th>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($banner_down as $sl=>$banner)
                            <tr>
                                <td>{{$sl+1}}</td>
                                <td>{{$banner->banner_id}}</td>
                                <td>{{$banner->sub_title}}</td>
                                <td>{{$banner->title}}</td>
                                <td><img width="200" src="{{asset('upload/banner_down')}}/{{$banner->image}}" alt=""></td>
                                <td>
                                    <a href="{{route('banner.down.delete', $banner->id)}}" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        @for ($i = 1; $i <= 3; $i++)
        <div class="card">
            <div class="card-header">
                <h3>Add Banner {{$i}}</h3>
            </div>
            <div class="card-body">
                <form action="{{route('banner.down.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="banner_id" value="{{$i}}">
                    <div class="mb-3">
                        <label>Sub Title</label>
                        <input type="text" name="sub_title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Image</label>
                        <input type="file" name="image[]" class="form-control" multiple>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Add Banner</button>
                    </div>
                </form>
            </div>
        </div>
        @endfor
    </div>
</div>
@endsection