@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add New Customer Review</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reviews.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rating">Rating <span class="text-danger">*</span></label>
                                    <select class="form-control @error('rating') is-invalid @enderror"
                                            id="rating" name="rating" required>
                                        <option value="">Select Rating</option>
                                        <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                                        <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                                        <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                                        <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                                        <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                                    </select>
                                    @error('rating')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_name">Product Name</label>
                                    <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                           id="product_name" name="product_name" value="{{ old('product_name') }}">
                                    @error('product_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_image">Product Image</label>
                                    <input type="file" class="form-control @error('product_image') is-invalid @enderror"
                                           id="product_image" name="product_image" accept="image/*">
                                    @error('product_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Upload a product image (optional)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="text">Review Text <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('text') is-invalid @enderror"
                                      id="text" name="text" rows="4" required>{{ old('text') }}</textarea>
                            @error('text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Review
                            </button>
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection