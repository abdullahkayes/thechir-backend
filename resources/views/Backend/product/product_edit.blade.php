@extends('layouts.admin');
@section('content')
@can('Product_add')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h1>Edit Product</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('product.update', $product->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="product_name" class="form-control" value="{{ $product->product_name }}" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Product SKU</label>
                                <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" required>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Add Category</label>
                                <select name="category_id" id="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    @foreach ($category as $categores )
                                        <option value="{{ $categores->id }}" @if($product->category_id == $categores->id) selected @endif>{{ $categores->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Add Subategory</label>
                                <select name="subcategory_id" id="sub" class="form-control">
                                    <option value="">Select Subcategory</option>
                                    @foreach ($subcategory as $subcategores )
                                        <option value="{{ $subcategores->id }}" @if($product->subcategory_id == $subcategores->id) selected @endif>{{ $subcategores->subcategory_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Add Brand</label>
                                <select name="brand_id" class="form-control">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" @if($product->brand_id == $brand->id) selected @endif>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Add Tags (comma separated, select or type)</label>
                                <input type="text" name="manual_tag" class="form-control" list="tag_list" value="@if($product->tag_id){{ str_replace(',', ', ', $product->tag_id) }}@else{{ '' }}@endif" placeholder="Enter tags separated by commas">
                                <datalist id="tag_list">
                                    @foreach ($tag as $tags )
                                        <option value="{{ $tags->tag_name }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                            <div class="col-lg-4 mb-3">
                            <label for="buy_price">Buy Price</label>
                            <input type="number" step="0.01" name="buy_price" id="buy_price" class="form-control" value="@if($product->productInventory){{ $product->productInventory->buy_price }}@else{{ '' }}@endif">
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="price">Regular Price</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" value="@if($product->productInventory){{ $product->productInventory->price }}@else{{ '' }}@endif" required>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="discount_price">Discount Price</label>
                            <input type="number" step="0.01" name="discount_price" id="discount_price" class="form-control" value="@if($product->productInventory){{ $product->productInventory->discount_price }}@else{{ '' }}@endif">
                        </div>
                            <div class="col-lg-4 mb-3">
                            <label for="reseller_price">Reseller Price</label>
                            <input type="number" step="0.01" name="reseller_price" id="reseller_price" class="form-control" value="@if($product->productInventory){{ $product->productInventory->reseller_price }}@else{{ '' }}@endif">
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="wholesale_price">Wholesale Price</label>
                            <input type="number" step="0.01" name="wholesale_price" id="wholesale_price" class="form-control" value="@if($product->productInventory){{ $product->productInventory->wholesale_price }}@else{{ '' }}@endif" required>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="distributer_price">Distributer Price</label>
                            <input type="number" step="0.01" name="distributer_price" id="distributer_price" class="form-control" value="@if($product->productInventory){{ $product->productInventory->distributer_price }}@else{{ '' }}@endif">
                        </div>
                         <div class="col-lg-4 mb-3">
                             <label for="amazon_price">Amazon Price</label>
                             <input type="number" step="0.01" name="amazon_price" id="amazon_price" class="form-control" value="@if(isset($product->productInventory)){{ $product->productInventory->amazon_price }}@else{{ '' }}@endif">
                         </div>
                        <div class="col-lg-4 mb-3">
                            <label for="quantity">Stock Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" value="@if($product->productInventory){{ $product->productInventory->quantity }}@else{{ '' }}@endif" required>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="weight_grams">Weight (grams)</label>
                            <input type="number" step="0.01" name="weight_grams" id="weight_grams" class="form-control" value="@if($product->productInventory){{ $product->productInventory->weight_grams }}@else{{ '' }}@endif" placeholder="e.g., 250">
                            <small class="form-text text-muted">Required for accurate shipping cost calculation</small>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="manufacture_date">Manufacture Date</label>
                            <input type="date" name="manufacture_date" id="manufacture_date" class="form-control" value="@if($product->productInventory && $product->productInventory->manufacture_date){{ $product->productInventory->manufacture_date->format('Y-m-d') }}@else{{ '' }}@endif">
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="@if($product->productInventory && $product->productInventory->expiry_date){{ $product->productInventory->expiry_date->format('Y-m-d') }}@else{{ '' }}@endif">
                            <small class="form-text text-muted">Leave empty if product doesn't expire</small>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="batch_number">Batch Number</label>
                            <input type="text" name="batch_number" id="batch_number" class="form-control" value="@if($product->productInventory){{ $product->productInventory->batch_number }}@else{{ '' }}@endif" placeholder="e.g., BATCH-2025-001">
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="size_ids">Sizes</label>
                            <select name="size_ids[]" id="size_ids" class="form-control js-example-basic-multiple" multiple="multiple">
                                @foreach($sizes as $size)
                                <option value="{{ $size->id }}" @if(in_array($size->id, $selectedSizes ?? [])) selected @endif>{{ $size->size_name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple sizes</small>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="color_id">Color</label>
                            <select name="color_id" id="color_id" class="form-control">
                                <option value="">Select Color</option>
                                @foreach($colors as $color)
                                <option value="{{ $color->id }}" @if($product->productInventory && $product->productInventory->color_id == $color->id) selected @endif>{{ $color->color_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Short Description</label>
                            <input type="text" name="short_desp" class="form-control" value="{{ $product->short_desp }}">
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label"><h4>Long Description</h4></label>
                                <textarea id="summernote" name="long_desp">{{ $product->long_desp }}</textarea>
                                @error('long_desp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Preview Image</label>
                                <input type="file" name="preview" class="form-control">
                                @if($product->preview)
                                    <img src="{{ $product->preview }}" alt="Current Preview" style="max-width: 200px; margin-top: 10px;">
                                    <small class="text-muted">Leave empty to keep current image</small>
                                @endif
                                @error('preview')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Gallery Images</label>
                                <input type="file" name="gallary[]" class="form-control" multiple>
                                @if($product->rel_to_gal && $product->rel_to_gal->count() > 0)
                                    <div class="mt-2">
                                        <small class="text-muted">Current gallery images:</small>
                                        <div class="d-flex flex-wrap">
                                            @foreach($product->rel_to_gal as $gal)
                                                <img src="{{ $gal->gallary }}" alt="Gallery" style="max-width: 100px; margin: 5px;">
                                            @endforeach
                                        </div>
                                        <small class="text-muted">Upload new images to replace all current gallery images</small>
                                    </div>
                                @endif
                                @error('gallary')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                        </div>
                        <div class="m-auto col-lg-3">
                            <button type="submit" class="btn btn-primary" style="width: 100% ; height: 50px;">Update Product</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endcan

@endsection
@section('script')
    <script>
         $('#summernote').summernote();
         $('.js-example-basic-multiple').select2();
    </script>
    <script>
        let category =document.querySelector('#category')
let sub =document.querySelector('#sub')

category.onchange=function(){
    let category_id= category.options[category.selectedIndex].value

    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
   });
       $.ajax({
            url:'/getSubcategory',
            type:'POST',
            data:{'category_id':category_id },
            success: function (data) {
             sub.innerHTML=data ;
            }
        });
}
    </script>
@endsection
<style>
    .note-editor .note-toolbar, .note-popover .popover-content {
    margin: 0;
    padding: 0 0 20px 5px;
    background: #ddd;
}
.note-editor.note-airframe .note-editing-area .note-editable, .note-editor.note-frame .note-editing-area .note-editable {
    overflow: auto;
    padding: 150px !important;
    word-wrap: break-word;
}
</style>
