@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4>Edit Your Profile</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('user.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}">
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h1>Update photo</h1>
            </div>
            <div class="card-body">
                @if(session('photo'))
                    <div class="alert alert-success">{{ session('photo') }}</div>
                @endif
                <form action="{{ route('user.photo') }}" method="POST" enctype="multipart/form-data">
                 @csrf
                 <div class="mb-3">
                    <label class="form-label">Photo</label>
                 <input type="file" name="photo" class="form-control" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                 @error('photo')
                     <strong class="text-danger">{{ $message }}</strong>
                 @enderror
                 <img id="blah" width="200" src="{{ asset('upload/user/') }}/{{ Auth::user()->photo ?? 'default.png' }}" >
                 </div>

                 <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                 </div>

                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            @if(session('passs'))
                <div class="alert alert-success">{{ session('passs') }}</div>
            @endif
            <div class="card-header">
                <h4>Password Change</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('user.password') }}" method="post">
                    @csrf
                    <div class="mb-3">
                     <label class="form-label">Current Password</label>
                     <input type="password" name="current_password" class="form-control">
                     @error('current_password')
                         <span class="text text-danger">{{ $message }}</span>
                     @enderror
                     @if(session('pass_err'))
                        <span class="text text-danger">{{ session('pass_err') }}</span>
                     @endif
                    </div>
                    <div class="mb-3">
                     <label class="form-label">New Password</label>
                     <input type="password" name="password" class="form-control">
                     @error('password')
                         <span class="text text-danger">{{ $message }}</span>
                     @enderror
                    </div>
                    <div class="mb-3">
                     <label class="form-label">Confirm Password</label>
                     <input type="password" name="password_confirmation" class="form-control">
                     @error('password_confirmation')
                         <span class="text text-danger">{{ $message }}</span>
                     @enderror
                     @if(session('password_con'))
                        <span class="text text-danger">{{ session('password_con') }}</span>
                     @endif
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary" >Password Change</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


@endsection
