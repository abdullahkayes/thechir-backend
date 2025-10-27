@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h1>Product Trash</h1>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                      <tr>
                        <th>SL</th>
                        <th>Product Name</th>
                        <th>Product Slug</th>
                        <th>Product Image</th>
                        <th>Action</th>
                      </tr>
                      @foreach ($products as $index=>$product)
                          <tr>
                            <td>{{ $index+1 }}</td>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ $product->slug }}</td>
                            <td>
                                <img src="{{ $product->preview }}" alt="">
                            </td>
                            <td>
                                <a data-id="{{ route('product.trash.delete',$product->id) }}" class="btn btn-danger del_btn">Delete</a>
                                <a href="{{ route('product.trash.restore',$product->id) }}" class="btn btn-success">Restore</a>
                            </td>
                          </tr>
                      @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        let del_btn= document.querySelectorAll('.del_btn');
        let delete_btn= Array.from(del_btn);

        delete_btn.map(item=>{
    item.onclick=function(){
        link= item.dataset.id;

        Swal.fire({
  title: "Are you sure?",
  text: "You won't be able to revert this!",
  icon: "warning",
  showCancelButton: true,
  confirmButtonColor: "#3085d6",
  cancelButtonColor: "#d33",
  confirmButtonText: "Yes, delete it!"
}).then((result) => {
  if (result.isConfirmed) {
   window.location.href=link;
  }
});

    }
})
    </script>
@endsection