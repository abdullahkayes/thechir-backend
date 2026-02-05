@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3>Banner Down Trash List</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>sl</th>
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
                                <td>{{$banner->sub_title}}</td>
                                <td>{{$banner->title}}</td>
                                <td><img width="200" src="{{asset('upload/banner_down')}}/{{$banner->image}}" alt=""></td>
                                <td>
                                    <a href="{{route('banner.down.restore', $banner->id)}}" class="btn btn-success">Restore</a>
                                    <a href="{{route('banner.down.force.delete', $banner->id)}}" class="btn btn-danger">Permanent Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection