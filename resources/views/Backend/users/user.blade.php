@extends('layouts.admin')
@section('content')
@can('User_assess')
<div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h1>User List</h1>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                       <tr>
                        <th>SL</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Photo</th>
                        <th>Action</th>
                       </tr>
                       @foreach ($users as $index=>$user)
                       <tr>
                        <td>{{ $index+1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <img src="{{ asset('upload/user') }}/{{ $user->photo }}" alt="">

                        </td>
                        @can('Users_delete')
                             <td>
                            <a href='{{ route('users.delete',$user->id) }}' type="button" class="btn btn-danger btn-icon">
                                <i data-feather="trash"></i>
                            </a>
                        </td>
                        @endcan

                       </tr>
                       @endforeach

                    </table>
                </div>
            </div>
        </div>

    </div>
@endcan

@endsection
