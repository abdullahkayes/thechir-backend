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
                                <label class="form-label">Add Tags</label>
                                <select name="tag_id[]" class="form-control js-example-basic-multiple"  multiple="multiple">
                                    <option value="" >Select Category</option>
                                    @foreach ($tag as $tags )
                                        <option value="{{ $tags->id }}">{{ $tags->tag_name }}</option>
                                    @endforeach
                                </select>
                            </div>
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
