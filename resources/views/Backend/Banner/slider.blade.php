@extends('layouts.admin')

@section('content')
<div class="container mt-4">

    {{-- Success Messages --}}
    @if(session('slider'))
        <div class="alert alert-success">{{ session('slider') }}</div>
    @endif
    @if(session('slider_delete'))
        <div class="alert alert-danger">{{ session('slider_delete') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    <!-- =======================
         Add Slider Section
    ======================== -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Add Banner Slider</h5>
        </div>
        <div class="card-body">
            <form action="{{ url('/slider/add') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Select Slider Images</label>
                    <input type="file" name="slider_image[]" class="form-control" multiple required>
                    <small class="text-muted">You can upload multiple images at once.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Brand (Optional)</label>
                    <select name="brand_id" class="form-control">
                        <option value="">No Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Upload Slider</button>
            </form>
        </div>
    </div>

    <!-- =======================
         Slider List Section
    ======================== -->
    <div class="card">
        <div class="card-header">
            <h5>All Sliders</h5>
        </div>

        <div class="card-body">
            @if($sliders->count() > 0)
                <div class="row">

                    @foreach($sliders as $slider)
                        <div class="col-md-3 mb-4">
                            <div class="card shadow-sm">
                                
                                <img src="{{ $slider->slider_image }}" class="card-img-top" style="height:170px; object-fit:cover;">

                                <div class="card-body text-center">
                                    <a href="{{ route('slider.delete', $slider->id) }}" 
                                       onclick="return confirm('Are you sure you want to delete this slider?')" 
                                       class="btn btn-danger btn-sm">
                                        Delete
                                    </a>
                                </div>

                            </div>
                        </div>
                    @endforeach

                </div>
            @else
                <p class="text-center text-muted">No slider images available.</p>
            @endif
        </div>
    </div>

</div>
@endsection
