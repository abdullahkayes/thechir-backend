@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; overflow: hidden;">
            <div class="card-header text-white" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: none;">
                <h1 class="mb-0" style="font-weight: 700; font-size: 1.8rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    <i class="fas fa-trash me-2"></i>Video Slider Trash
                </h1>
                <p class="mb-0 mt-1 opacity-75" style="font-size: 0.9rem;">Permanently delete or restore trashed video sliders</p>
            </div>
            <div class="card-body" style="background: #f8f9fa;">
                @if($trashes->count() > 0)
                    <div class="row g-4">
                        @foreach($trashes as $videoSlider)
                            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm premium-card" style="border-radius: 12px; overflow: hidden; transition: all 0.3s ease; background: white;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.08)';">
                                    <div class="position-relative">
                                        <video controls style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px 12px 0 0;">
                                            <source src="{{ $videoSlider->video }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-secondary fs-6 px-2 py-1" style="border-radius: 20px; font-weight: 600;">ID: {{ $videoSlider->id }}</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <img src="{{ $videoSlider->thumbnail }}" alt="Thumbnail" class="rounded-circle mb-2" style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #e9ecef; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                                    <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 600;">THUMBNAIL</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <img src="{{ $videoSlider->product_image }}" alt="Product Image" class="rounded-circle mb-2" style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #e9ecef; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                                    <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 600;">PRODUCT</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-section mb-3 text-center">
                                            <div class="d-flex justify-content-center align-items-center mb-2">
                                                <div class="price-tag me-3" style="background: linear-gradient(45deg, #28a745, #20c997); color: white; padding: 8px 16px; border-radius: 25px; font-weight: 700; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                                                    ${{ $videoSlider->price }}
                                                </div>
                                                @if($videoSlider->discount_price < $videoSlider->price)
                                                    <div class="discount-tag" style="background: linear-gradient(45deg, #dc3545, #fd7e14); color: white; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);">
                                                        ${{ $videoSlider->discount_price }}
                                                    </div>
                                                @endif
                                            </div>
                                            <small class="text-muted" style="font-size: 0.8rem;">Regular • Discount Price</small>
                                        </div>
                                        <div class="action-section text-center">
                                            <form action="{{ route('videoSlider.trash.restore', $videoSlider->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to restore this video slider?')">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm px-3 py-2 me-2" style="border-radius: 25px; font-weight: 600;">
                                                    <i class="fas fa-undo me-1"></i>Restore
                                                </button>
                                            </form>
                                            <form action="{{ route('videoSlider.trash.delete', $videoSlider->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to permanently delete this video slider? This action cannot be undone.')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm px-3 py-2" style="border-radius: 25px; font-weight: 600;">
                                                    <i class="fas fa-times me-1"></i>Delete Forever
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div style="font-size: 4rem; color: #dee2e6; margin-bottom: 1rem;">
                            <i class="fas fa-trash"></i>
                        </div>
                        <h3 class="text-muted mb-3" style="font-weight: 300;">Trash is Empty</h3>
                        <p class="text-muted" style="font-size: 1.1rem;">No trashed video sliders found</p>
                        <a href="{{ route('videoSlider') }}" class="btn btn-primary">Back to Video Sliders</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(session('restore'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Restored!',
            text: '{{ session('restore') }}',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
@endif

@if(session('force_delete'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Permanently Deleted!',
            text: '{{ session('force_delete') }}',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
@endif

<style>
.premium-card {
    position: relative;
}

.premium-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
    background-size: 300% 300%;
    animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.price-tag {
    position: relative;
}

.price-tag::after {
    content: '';
    position: absolute;
    top: 50%;
    right: -8px;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-left: 8px solid #20c997;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
}

.badge {
    font-size: 0.75rem !important;
}
</style>

@endsection
