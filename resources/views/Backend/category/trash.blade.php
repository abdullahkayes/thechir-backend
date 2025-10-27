@extends('layouts.admin');
@section('content')
    <div class="row">
        <div class="col-lg-10">
            <form action="{{ route('category.trash.checked') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h4>Category Trash</h4>
                    <div class="card-body">
                       <table class="table table-bordered">
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
                         @foreach ($trashs as $index=>$trash )
                             <tr>
                                <td>
                                    <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input chkDel" name="category_id[]"
                                        value="{{ $trash->id }}" >
                                    <i class="input-frame"></i></label>
                                </div>
                               </td>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $trash->category_name }}</td>
                                 <td>
                                    <img src="{{ $trash->category_image }}" alt="">
                                 </td>
                                 <td>
                                    <a href="{{ route('trash.restore',$trash->id) }}" class="btn btn-success btn-icon">
                                        <i data-feather="rotate-cw"></i>
                                    </a>
                                    <a data-id="{{ route('trash.delete',$trash->id) }}" id="del_btn" class="btn btn-danger btn-icon del_btn">
                                        <i data-feather="trash"></i>
                                    </a>
                                 </td>
                             </tr>
                         @endforeach
                       </table>
                       <div class="mt-3 ">
                        <button type="submit" class="btn btn-danger del_btn2 d-none" name="button1" value="Button 1" >Checked Delete</button>
                        <button type="submit" class="btn btn-success del_btn3 d-none" name="button2" value="Button 2" >Checked Restore</button>
                      </div>
                    </div>
                </div>
            </div>
            </form>
    </div>
@endsection
@section('script')
    <script>

let del_btn= document.querySelectorAll('#del_btn');
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

// check
let del_btn2=document.querySelector('.del_btn2');
let del_btn3=document.querySelector('.del_btn3');
let chkSelectAll=document.querySelector('#chkSelectAll');

  $("#chkSelectAll").on('click', function(){
     $(".chkDel").prop("checked", this.checked );
    if($(".chkDel:checked").length > 0){
    del_btn2.classList.remove('d-none')
    del_btn3.classList.remove('d-none')
    }else{
        del_btn2.classList.add('d-none')
        del_btn3.classList.add('d-none')
    }
});
$(".chkDel").on('change', function(){
    if($(".chkDel:checked").length > 0){
    del_btn2.classList.remove('d-none')
    del_btn3.classList.remove('d-none')
    }else{
        del_btn2.classList.add('d-none')
        del_btn3.classList.add('d-none')
    }
});


    </script>
@endsection