
@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h2>Add New Brand</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
          <ul class="mb-0">
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif

    <form action="{{ route('brand.store') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Brand Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">Brand Logo</label>
            <input type="file" id="logo" name="logo" class="form-control" required accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Add Brand</button>
        <a href="{{ url('brand/list') }}" class="btn btn-primary"> Brand List </a>

</div>
@endsection
