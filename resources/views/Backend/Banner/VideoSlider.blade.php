be @extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h1> {{ isset($videoSlider) ? 'Edit' : 'Add' }} Video Slider </h1>
              <a href="{{ route('videoSlider.trash') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-trash"></i> View Trash
              </a>
            </div>
            <div class="card-body">
                <form action="{{ isset($videoSlider) ? route('videoSlider.update', $videoSlider->id) : route('videoSlider.add') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Search & Select Product</label>
                        <input type="text" id="product_search" class="form-control" placeholder="Search products..." style="margin-bottom: 10px;" value="{{ isset($videoSlider) ? $videoSlider->name : '' }}">
                        <input type="hidden" name="product_id" id="product_id_hidden" value="{{ isset($videoSlider) ? $videoSlider->product_id : '' }}">
                        <select id="product_select_dropdown" class="form-select" style="display: none;">
                            <option value="">Select a product</option>
                        </select>
                        <div id="product_list" class="list-group" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                        @error('product_id')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Product Name</label>
                        <input type="text" name="name" id="product_name" class="form-control" placeholder="Enter product name" value="{{ isset($videoSlider) ? $videoSlider->name : '' }}" required>
                        @error('name')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Price</label>
                        <input type="number" name="price" id="product_price" class="form-control" placeholder="Enter price" step="0.01" min="0" value="{{ isset($videoSlider) ? $videoSlider->price : '' }}" required>
                        @error('price')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Discount Price</label>
                        <input type="number" name="discount_price" id="product_discount_price" class="form-control" placeholder="Enter discount price" step="0.01" min="0" value="{{ isset($videoSlider) && $videoSlider->inventories ? $videoSlider->inventories->discount_price : '' }}" required>
                        @error('discount_price')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Wholesale Price</label>
                        <input type="number" name="wholesale_price" id="wholesale_price" class="form-control" placeholder="Enter wholesale price" step="0.01" min="0" value="{{ isset($videoSlider) && $videoSlider->inventories ? $videoSlider->inventories->wholesale_price : '' }}" required>
                        @error('wholesale_price')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Reseller Price</label>
                        <input type="number" name="reseller_price" id="reseller_price" class="form-control" placeholder="Enter reseller price" step="0.01" min="0" value="{{ isset($videoSlider) && $videoSlider->inventories ? $videoSlider->inventories->reseller_price : '' }}" required>
                        @error('reseller_price')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Distributer Price</label>
                        <input type="number" name="distributer_price" id="distributer_price" class="form-control" placeholder="Enter distributer price" step="0.01" min="0" value="{{ isset($videoSlider) && $videoSlider->inventories ? $videoSlider->inventories->distributer_price : '' }}" required>
                        @error('distributer_price')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Amazon Price</label>
                        <input type="number" name="amazon_price" id="amazon_price" class="form-control" placeholder="Enter amazon price" step="0.01" min="0" value="{{ isset($videoSlider) && $videoSlider->inventories ? $videoSlider->inventories->amazon_price : '' }}" required>
                        @error('amazon_price')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Video</label>
                        <div class="upload-area" style="position: relative; border: 2px dashed #e9ecef; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: #f8f9fa;">
                            <input type="file" name="video" id="video_input" class="form-control" accept="video/*" {{ isset($videoSlider) ? '' : 'required' }} onchange="previewVideo()" style="opacity: 0; position: absolute; width: 100%; height: 100%; top: 0; left: 0; cursor: pointer;">
                            <i data-feather="video" style="width: 32px; height: 32px; color: #bdc3c7; margin-bottom: 8px; display: block;"></i>
                            <p style="color: #7f8c8d; font-size: 13px; margin: 0;">{{ isset($videoSlider) ? 'Click to change Video' : 'Click to upload Video' }}</p>
                        </div>
                        @error('video')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Thumbnail Image</label>
                        <div class="upload-area" style="position: relative; border: 2px dashed #e9ecef; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: #f8f9fa;">
                            <input type="file" name="thumbnail" id="thumbnail_input" class="form-control" accept="image/*" onchange="document.getElementById('thumbnail_preview').src = window.URL.createObjectURL(this.files[0]); updateFullPreview()" {{ isset($videoSlider) ? '' : 'required' }} style="opacity: 0; position: absolute; width: 100%; height: 100%; top: 0; left: 0; cursor: pointer;">
                            <i data-feather="image" style="width: 32px; height: 32px; color: #bdc3c7; margin-bottom: 8px; display: block;"></i>
                            <p style="color: #7f8c8d; font-size: 13px; margin: 0;">{{ isset($videoSlider) ? 'Click to change Thumbnail image' : 'Click to upload Thumbnail image' }}</p>
                        </div>
                        @error('thumbnail')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                        <div style="margin-top: 15px; text-align: center;">
                            <img src="{{ isset($videoSlider) ? asset($videoSlider->thumbnail) : '' }}" id="thumbnail_preview" alt="" style="max-width: 100%; height: auto; border-radius: 8px; max-height: 200px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #2c3e50; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block;">Product Image</label>
                        <div class="upload-area" style="position: relative; border: 2px dashed #e9ecef; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: #f8f9fa;" onmouseover="this.style.borderColor='#667eea'; this.style.backgroundColor='#f0f3f7';" onmouseout="this.style.borderColor='#e9ecef'; this.style.backgroundColor='#f8f9fa';">
                            <input type="file" name="product_image" id="product_image_input" class="form-control" accept="image/*" onchange="document.getElementById('product_preview').src = window.URL.createObjectURL(this.files[0]); updateFullPreview()" {{ isset($videoSlider) ? '' : 'required' }} style="opacity: 0; position: absolute; width: 100%; height: 100%; top: 0; left: 0; cursor: pointer;">
                            <i data-feather="image" style="width: 32px; height: 32px; color: #bdc3c7; margin-bottom: 8px; display: block;"></i>
                            <p style="color: #7f8c8d; font-size: 13px; margin: 0;">{{ isset($videoSlider) ? 'Click to change Product image' : 'Click to upload Product image' }}</p>
                        </div>
                        @error('product_image')
                            <span style="color: #e74c3c; font-size: 12px; font-weight: 600; display: block; margin-top: 6px;">{{ $message }}</span>
                        @enderror
                        <div style="margin-top: 15px; text-align: center;">
                            <img src="{{ isset($videoSlider) ? asset($videoSlider->product_image) : '' }}" id="product_preview" alt="" style="max-width: 100%; height: auto; border-radius: 8px; max-height: 200px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">{{ isset($videoSlider) ? 'Update' : 'Add' }} Video Slider</button>
                    </div>
                </form>
                @if(session('video_slider'))
                    <div class="alert alert-success mt-3">{{ session('video_slider') }}</div>
                @endif
                @if(session('video_slider_delete'))
                    <div class="alert alert-success mt-3">{{ session('video_slider_delete') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h1>Preview Selected Items</h1>
            </div>
            <div class="card-body">
                <div id="preview_section" style="display: {{ isset($videoSlider) ? 'block' : 'none' }};">
                    <h5>Video Preview:</h5>
                    <video id="video_preview" controls style="width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);" {{ isset($videoSlider) ? 'src="' . asset($videoSlider->video) . '"' : '' }}></video>
                    <br><br>
                    <h5>Thumbnail Preview:</h5>
                    <img id="thumbnail_preview_full" src="{{ isset($videoSlider) ? asset($videoSlider->thumbnail) : '' }}" alt="Thumbnail" style="width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <br><br>
                    <h5>Product Image Preview:</h5>
                    <img id="product_preview_full" src="{{ isset($videoSlider) ? asset($videoSlider->product_image) : '' }}" alt="Product Image" style="width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                </div>
                <p id="no_preview" style="text-align: center; color: #7f8c8d; {{ isset($videoSlider) ? 'display: none;' : '' }}">No items selected yet.</p>
            </div>
        </div>
    </div>
</div>
<script>
const isEditing = @json(isset($videoSlider));
document.addEventListener('DOMContentLoaded', function() {
    const productSearch = document.getElementById('product_search');
    const productList = document.getElementById('product_list');
    const productSelectDropdown = document.getElementById('product_select_dropdown');
    const productNameInput = document.getElementById('product_name');
    const productPriceInput = document.getElementById('product_price');
    const productDiscountPriceInput = document.getElementById('product_discount_price');
    let products = [];
    let productsLoaded = false;

    // Fetch products from API with cache buster and faster timeout
    fetch('/api/all_products?t=' + Date.now())
        .then(response => response.json())
        .then(data => {
            products = data.data || data;
            productsLoaded = true;
        })
        .catch(error => console.error('Error fetching products:', error));
    
    let searchTimeout;
    productSearch.addEventListener('input', function() {
        if (!productsLoaded) return;
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const query = this.value.toLowerCase();
            productList.innerHTML = '';
            productSelectDropdown.innerHTML = '<option value="">Select a product</option>';

            if (query.length > 2) { // Only search after 3 characters for better performance
                const filteredProducts = products.filter(product =>
                    product.product_name.toLowerCase().includes(query)
                );

                if (filteredProducts.length > 0) {
                    productList.style.display = 'block';
                    productSelectDropdown.style.display = 'none';

                    filteredProducts.slice(0, 10).forEach(product => { // Limit to 10 results for fast rendering
                        const item = document.createElement('a');
                        item.href = '#';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">${product.product_name}</h5>
                                <small>$${product.price || product.display_price || 0}</small>
                            </div>
                            <p class="mb-1">${product.short_desp || ''}</p>
                        `;
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            selectProduct(product);
                        });
                        productList.appendChild(item);
                    });
                } else {
                    productList.style.display = 'none';
                    productSelectDropdown.style.display = 'block';
                }
            } else {
                productList.style.display = 'none';
                productSelectDropdown.style.display = 'none';
            }
        }, 200); // Reduced from 300ms to 200ms
    });

    function selectProduct(product) {
        let productIdInput = document.querySelector('input[name="product_id"]');
        if (!productIdInput) {
            productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = 'product_id';
            document.querySelector('form').appendChild(productIdInput);
        }
        productIdInput.value = product.id;

        productNameInput.value = product.product_name;
        productPriceInput.value = product.price || product.display_price || 0;
        productDiscountPriceInput.value = product.discount_price || product.original_price || product.price || product.display_price || 0;

        const wholesalePriceInput = document.getElementById('wholesale_price');
        wholesalePriceInput.value = product.wholesale_price || 0;
        const resellerPriceInput = document.getElementById('reseller_price');
        resellerPriceInput.value = product.reseller_price || 0;
        const distributerPriceInput = document.getElementById('distributer_price');
        distributerPriceInput.value = product.distributer_price || 0;
        const amazonPriceInput = document.getElementById('amazon_price');
        amazonPriceInput.value = product.amazon_price || 0;

        if (product.preview) {
            document.getElementById('thumbnail_preview').src = product.preview;
            document.getElementById('product_preview').src = product.preview;
            updateFullPreview();
        }

        productSearch.value = product.product_name;
        productList.style.display = 'none';
    }
});

function previewVideo() {
    const videoInput = document.getElementById('video_input');
    const videoPreview = document.getElementById('video_preview');

    if (videoInput.files && videoInput.files[0]) {
        const videoUrl = URL.createObjectURL(videoInput.files[0]);
        videoPreview.src = videoUrl;
        updateFullPreview();
    }
}

function updateFullPreview() {
    const videoInput = document.getElementById('video_input');
    const thumbnailInput = document.getElementById('thumbnail_input');
    const productInput = document.getElementById('product_image_input');
    const previewSection = document.getElementById('preview_section');
    const noPreview = document.getElementById('no_preview');
    const videoPreview = document.getElementById('video_preview');
    const thumbnailPreview = document.getElementById('thumbnail_preview_full');
    const productPreview = document.getElementById('product_preview_full');

    let hasContent = isEditing;

    if (videoInput.files && videoInput.files[0]) {
        const videoUrl = URL.createObjectURL(videoInput.files[0]);
        videoPreview.src = videoUrl;
        hasContent = true;
    }

    if (thumbnailInput.files && thumbnailInput.files[0]) {
        const imgUrl = URL.createObjectURL(thumbnailInput.files[0]);
        thumbnailPreview.src = imgUrl;
        hasContent = true;
    }

    if (productInput.files && productInput.files[0]) {
        const imgUrl = URL.createObjectURL(productInput.files[0]);
        productPreview.src = imgUrl;
        hasContent = true;
    }

    if (hasContent) {
        previewSection.style.display = 'block';
        noPreview.style.display = 'none';
    } else {
        previewSection.style.display = 'none';
        noPreview.style.display = 'block';
    }
}
</script>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; overflow: hidden;">
            <div class="card-header text-white" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: none;">
                <h1 class="mb-0" style="font-weight: 700; font-size: 1.8rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    <i class="fas fa-video me-2"></i>Video Slider Collection
                </h1>
                <p class="mb-0 mt-1 opacity-75" style="font-size: 0.9rem;">Premium video sliders with stunning visuals</p>
            </div>
            <div class="card-body" style="background: #f8f9fa;">
                @if($videoSliders->count() > 0)
                    <div class="row g-4">
                        @foreach($videoSliders as $videoSlider)
                            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm premium-card" style="border-radius: 12px; overflow: hidden; transition: all 0.3s ease; background: white;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.08)';">
                                    <div class="position-relative">
                                        <video controls poster="{{ $videoSlider->thumbnail }}" preload="none" autoplay muted style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px 12px 0 0;">
                                            <source src="{{ $videoSlider->video }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-danger fs-6 px-2 py-1" style="border-radius: 20px; font-weight: 600;">ID: {{ $videoSlider->id }}</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <img src="{{ $videoSlider->thumbnail }}" alt="Thumbnail" class="rounded-circle mb-2" loading="lazy" style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #e9ecef; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                                    <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 600;">THUMBNAIL</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <img src="{{ $videoSlider->product_image }}" alt="Product Image" class="rounded-circle mb-2" loading="lazy" style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #e9ecef; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
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
                                            <a href="{{ route('videoSlider.edit', $videoSlider->id) }}" class="btn btn-outline-primary btn-sm px-3 py-2 me-2" style="border-radius: 25px; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#007bff'; this.style.color='white'; this.style.borderColor='#007bff';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#007bff'; this.style.borderColor='#007bff';">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('videoSlider.delete', $videoSlider->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this video slider?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-3 py-2 delete-btn" style="border-radius: 25px; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#dc3545'; this.style.color='white'; this.style.borderColor='#dc3545';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#dc3545'; this.style.borderColor='#dc3545';">
                                                    <i class="fas fa-trash-alt me-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $videoSliders->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div style="font-size: 4rem; color: #dee2e6; margin-bottom: 1rem;">
                            <i class="fas fa-video-slash"></i>
                        </div>
                        <h3 class="text-muted mb-3" style="font-weight: 300;">No Video Sliders Found</h3>
                        <p class="text-muted" style="font-size: 1.1rem;">Start creating your premium video slider collection above</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

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

.premium-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
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

.delete-btn:hover {
    background-color: #dc3545 !important;
    color: white !important;
    border-color: #dc3545 !important;
}

.upload-area:hover {
    border-color: #667eea;
    background-color: #f0f3f7;
}

</style>

@endsection
