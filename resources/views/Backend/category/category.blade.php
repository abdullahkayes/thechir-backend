@extends('layouts.admin');
@section('content')

    <div class="row">
        @can('Category_access')
           <div class="col-lg-8">
            <form action="{{ route('category.checked.delete') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <table class="table table-bodered">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input"  id="chkSelectAll">
                                           Check All
                                        <i class="input-frame"></i></label>
                                    </div>
                                </th>
                                <th>SL</th>
                                <th>Category Name</th>
                                <th>Category Image</th>
                                <th>Action</th>
                            </tr>

                            @foreach ($categorys as $index=>$category)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input chkDel" name="category_id[]"
                                            value="{{ $category->id }}" >
                                        <i class="input-frame"></i></label>
                                    </div>
                                   </td>
                                    <td>{{ $index+1 }}</td>
                                    <td>{{ $category->category_name }}</td>
                                    <td>
                                        <img src="{{ $category->category_image }}" alt="">
                                    </td>
                                    @can('Category_delete')
                                     <td>
                                        <a href="{{ route('category.delete',$category->id) }}" class="btn btn-danger">Delete</a>
                                    </td>
                                    @endcan

                                </tr>
                            @endforeach
                        </table>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-danger del_btn d-none">Checked Delete</button>
                          </div>
                    </div>
                </div>
            </form>

        </div>
        @endcan
       @can('Category_add')
      <div class="col-lg-4">
           <div class="card">
            @if(session('category'))
                <div class="alert alert-success">{{ session('category') }}</div>
            @endif
            <div class="card-header">
                <h4>Add New Category</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('category.add') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="category_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Category Iamge</label>
                    <input type="file" name="category_image" class="form-control" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                    <img src="" width="200" id="blah" alt="">
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
                </form>
            </div>
           </div>
        </div>
       @endcan

    </div>
@endsection
@section('script')
    <script>
let del_btn=document.querySelector('.del_btn');
let chkSelectAll=document.querySelector('#chkSelectAll');

  $("#chkSelectAll").on('click', function(){
     $(".chkDel").prop("checked", this.checked );
    if($(".chkDel:checked").length > 0){
    del_btn.classList.remove('d-none')
    }else{
        del_btn.classList.add('d-none')
    }
});
$(".chkDel").on('change', function(){
    if($(".chkDel:checked").length > 0){
    del_btn.classList.remove('d-none')
    }else{
        del_btn.classList.add('d-none')
    }
});

    </script>
    @endsection
