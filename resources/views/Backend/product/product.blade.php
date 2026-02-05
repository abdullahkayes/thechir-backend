@extends('layouts.admin');
@section('content')
@can('Product_add')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h1>Add Products</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('product.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="product_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Product SKU</label>
                                <input type="text" name="sku" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Add Category</label>
                                <select name="category_id" id="category">
                                    <option value="">Select Category</option>
                                    @foreach ($category as $categores )
                                        <option value="{{ $categores->id }}">{{ $categores->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Add Subategory</label>
                                <select name="subcategory_id" id="sub">
                                    <option value="">Select Category</option>
                                    @foreach ($subcategory as $subcategores )
                                        <option value="{{ $subcategores->id }}">{{ $subcategores->subcategory_name }}</option>
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
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Add Tags (comma separated, select or type)</label>
                                <input type="text" name="manual_tag" class="form-control" list="tag_list" placeholder="Enter tags separated by commas">
                                <datalist id="tag_list">
                                    @foreach ($tag as $tags )
                                        <option value="{{ $tags->tag_name }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                            <div class="col-lg-4 mb-3">
                            <label for="buy_price">Buy Price</label>
                            <input type="number" step="0.01" name="buy_price" id="buy_price" class="form-control">
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="price">Regular Price</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="discount_pct">Discount % (of regular price)</label>
                            <input type="number" step="0.01" name="discount_pct" id="discount_pct" class="form-control" placeholder="e.g., 10 for 10%">
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="discount_price">Discount Price</label>
                            <input type="number" step="0.01" name="discount_price" id="discount_price" class="form-control">
                        </div>
                            <div class="col-lg-4 mb-3">
                                <label for="reseller_pct">Reseller % (of base price)</label>
                                <input type="number" step="0.01" name="reseller_pct" id="reseller_pct" class="form-control" placeholder="e.g., 10 for 10%">
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="distributer_pct">Distributer % (of base price)</label>
                                <input type="number" step="0.01" name="distributer_pct" id="distributer_pct" class="form-control" placeholder="e.g., 15 for 15%">
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="wholesaler_pct">Wholesaler % (of base price)</label>
                                <input type="number" step="0.01" name="wholesaler_pct" id="wholesaler_pct" class="form-control" placeholder="e.g., 20 for 20%">
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="amazon_pct">Amazon % (of base price)</label>
                                <input type="number" step="0.01" name="amazon_pct" id="amazon_pct" class="form-control" placeholder="e.g., 25 for 25%">
                            </div>

                            <div class="col-lg-4 mb-3">
                                <label for="reseller_price">Reseller Price</label>
                                <input type="number" step="0.01" name="reseller_price" id="reseller_price" class="form-control">
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="wholesale_price">Wholesale Price</label>
                                <input type="number" step="0.01" name="wholesale_price" id="wholesale_price" class="form-control" required>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="distributer_price">Distributer Price</label>
                                <input type="number" step="0.01" name="distributer_price" id="distributer_price" class="form-control">
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="amazon_price">Amazon Price</label>
                                <input type="number" step="0.01" name="amazon_price" id="amazon_price" class="form-control">
                            </div>
                        <div class="col-lg-4 mb-3">
                            <label for="quantity">Stock Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" required>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="weight_grams">Weight (grams)</label>
                            <input type="number" step="0.01" name="weight_grams" id="weight_grams" class="form-control" placeholder="e.g., 250">
                            <small class="form-text text-muted">Required for accurate shipping cost calculation</small>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="manufacture_date">Manufacture Date</label>
                            <input type="date" name="manufacture_date" id="manufacture_date" class="form-control">
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" class="form-control">
                            <small class="form-text text-muted">Leave empty if product doesn't expire</small>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="batch_number">Batch Number</label>
                            <input type="text" name="batch_number" id="batch_number" class="form-control" placeholder="e.g., BATCH-2025-001">
                        </div>
                             <div class="col-lg-4 mb-3">
                            <label for="size_ids">Sizes</label>
                            <select name="size_ids[]" id="size_ids" class="form-control js-example-basic-multiple" multiple="multiple">
                                @foreach($sizes as $size)
                                <option value="{{ $size->id }}">{{ $size->size_name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple sizes</small>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label for="color_id">Color</label>
                            <select name="color_id" id="color_id" class="form-control">
                                <option value="">Select Color</option>
                                @foreach($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->color_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Short Description</label>
                            <input type="text" name="short_desp" class="form-control">
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label"><h4>Long Description</h4></label>
                                <textarea id="summernote" name="long_desp"></textarea>
                                @error('long_desp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Priview Image</label>
                                <input type="file" name="preview" class="form-control">
                                @error('preview')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                                <img src="" alt="">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Gallary</label>
                                <input type="file" name="gallary[]" class="form-control" multiple>
                                @error('gallary')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                        </div>
                        <div class="m-auto col-lg-3">
                            <button type="submit" class="btn btn-primary" style="width: 100% ; height: 50px;">Add Product</button>
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
        // Auto-calc reseller/distributer/wholesaler prices based on percentage fields
        (function(){
            const priceEl = document.getElementById('price');
            const discountEl = document.getElementById('discount_price');
            const discountPctEl = document.getElementById('discount_pct');

            const resellerPct = document.getElementById('reseller_pct');
            const distributerPct = document.getElementById('distributer_pct');
            const wholesalerPct = document.getElementById('wholesaler_pct');
            const amazonPct = document.getElementById('amazon_pct');

            const resellerPrice = document.getElementById('reseller_price');
            const distributerPrice = document.getElementById('distributer_price');
            const wholesalePrice = document.getElementById('wholesale_price');
            const amazonPrice = document.getElementById('amazon_price');

            function getBasePrice(){
                const disc = parseFloat(discountEl.value);
                const reg = parseFloat(priceEl.value);
                if(!isNaN(disc) && disc > 0) return disc;
                if(!isNaN(reg)) return reg;
                return 0;
            }

            function calcAndSet(pctEl, outEl){
                if(!pctEl || !outEl) return;
                const pct = parseFloat(pctEl.value);
                const base = parseFloat(getBasePrice());
                if(!isNaN(pct) && !isNaN(base)){
                    // Subtract percentage from base price: final = base - (base * pct/100)
                    const discount = (base * (pct/100));
                    const val = base - discount;
                    // Show price with exactly 2 decimal places
                    outEl.value = val.toFixed(2);
                }
            }

            // Listen for pct changes
            [resellerPct, distributerPct, wholesalerPct, amazonPct].forEach(function(el){
                if(!el) return;
                el.addEventListener('input', function(){
                    if(el === resellerPct) calcAndSet(resellerPct, resellerPrice);
                    if(el === distributerPct) calcAndSet(distributerPct, distributerPrice);
                    if(el === wholesalerPct) calcAndSet(wholesalerPct, wholesalePrice);
                    if(el === amazonPct) calcAndSet(amazonPct, amazonPrice);
                });
            });

            // Recalculate when base price or discount changes
            [priceEl, discountEl].forEach(function(el){
                if(!el) return;
                el.addEventListener('input', function(){
                    if(resellerPct && resellerPct.value) calcAndSet(resellerPct, resellerPrice);
                    if(distributerPct && distributerPct.value) calcAndSet(distributerPct, distributerPrice);
                    if(wholesalerPct && wholesalerPct.value) calcAndSet(wholesalerPct, wholesalePrice);
                    if(amazonPct && amazonPct.value) calcAndSet(amazonPct, amazonPrice);
                    if(amazonPct && amazonPct.value) calcAndSet(amazonPct, amazonPrice);
                });
            });

            // Handle discount percentage calculation
            if(discountPctEl && discountEl && priceEl) {
                discountPctEl.addEventListener('input', function(){
                    const pct = parseFloat(discountPctEl.value);
                    const base = parseFloat(priceEl.value);
                    if(!isNaN(pct) && !isNaN(base) && base > 0) {
                        const discount = (base * (pct/100));
                        const val = base - discount;
                        discountEl.value = val.toFixed(2);
                        
                        // Recalculate reseller/distributer/wholesaler prices based on new discount price
                        if(resellerPct && resellerPct.value) calcAndSet(resellerPct, resellerPrice);
                        if(distributerPct && distributerPct.value) calcAndSet(distributerPct, distributerPrice);
                        if(wholesalerPct && wholesalerPct.value) calcAndSet(wholesalerPct, wholesalePrice);
                    }
                });

                // Also update discount percentage when discount price is manually changed
                discountEl.addEventListener('input', function(){
                    const discount = parseFloat(discountEl.value);
                    const base = parseFloat(priceEl.value);
                    if(!isNaN(discount) && !isNaN(base) && base > 0 && discount < base) {
                        const pct = ((base - discount) / base) * 100;
                        discountPctEl.value = pct.toFixed(2);
                    }
                });

                // Update discount percentage when regular price changes
                priceEl.addEventListener('input', function(){
                    const discount = parseFloat(discountEl.value);
                    const base = parseFloat(priceEl.value);
                    if(!isNaN(discount) && !isNaN(base) && base > 0 && discount < base) {
                        const pct = ((base - discount) / base) * 100;
                        discountPctEl.value = pct.toFixed(2);
                    }
                });
            }
        })();
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
