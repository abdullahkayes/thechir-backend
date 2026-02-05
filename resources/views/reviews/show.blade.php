@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Review Details</h6>
                    <div>
                        <a href="{{ route('admin.reviews.edit', $review) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="review-card mb-4" style="border: 1px solid #eee; border-radius: 8px; padding: 20px;">
                                <div class="reviewer-info" style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div class="initials-badge" style="width: 50px; height: 50px; background-color: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; color: #333; margin-right: 15px;">
                                        {{ substr($review->name, 0, 1) }}
                                    </div>
                                    <div class="name-and-stars">
                                        <span class="reviewer-name" style="font-weight: bold; display: block; font-size: 18px;">{{ $review->name }}</span>
                                        <div class="stars" style="font-size: 16px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <span style="margin-left: 10px; color: #666;">({{ $review->rating }}/5)</span>
                                        </div>
                                    </div>
                                </div>

                                @if($review->product_name)
                                <div class="product-info mb-3">
                                    <strong>Product:</strong> {{ $review->product_name }}
                                </div>
                                @endif

                                <div class="review-text" style="font-size: 16px; line-height: 1.6; color: #555; margin-bottom: 15px;">
                                    {{ $review->text }}
                                </div>

                                <div class="review-metadata">
                                    <span class="badge badge-primary" style="font-size: 14px; padding: 8px 12px;">{{ $review->rating }}/5 Rating</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            @if($review->product_image)
                            <div class="card">
                                <div class="card-header">
                                    <h6>Product Image</h6>
                                </div>
                                <div class="card-body text-center">
                                    <img src="{{ asset($review->product_image) }}" alt="Product Image" class="img-fluid" style="max-height: 200px; object-fit: contain;">
                                </div>
                            </div>
                            @endif

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6>Review Information</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Review ID:</strong> #{{ $review->id }}</p>
                                    <p><strong>Created:</strong> {{ $review->created_at->format('M d, Y \a\t H:i') }}</p>
                                    <p><strong>Last Updated:</strong> {{ $review->updated_at->format('M d, Y \a\t H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection