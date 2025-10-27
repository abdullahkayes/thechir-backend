@extends('layouts.admin')
@section('content')
<div class="col-lg-8">
  <form action="" method="POST">
    @csrf
    <div class="card">
      <div class="card-header">
        <h1>Subcategory Trash List</h1>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <tr>
            <th>
              <div class="form-check">
                  <label class="form-check-label">
                      <input type="checkbox" class="form-check-input" id="chkSelectAll">
                     Check All
                  <i class="input-frame"></i></label>
              </div>
          </th>
              <th>SL</th>
              <th>Subcategory Name</th>
              <th>Subcategory Image</th>
              <th>Action</th>
          </tr>
          @foreach ($subactegor as $index=>$subs)
          <tr>
            <td>
              <div class="form-check">
                  <label class="form-check-label">
                      <input type="checkbox" class="form-check-input chkDel" name="subcategory_id[]" value="{{ $subs->id }}">                                  
                  <i class="input-frame"></i></label>
              </div>
          </td>
              <td>{{ $index+1 }}</td>
              <td>{{ $subs->subcategory_name }}</td>
              <td>
                <img src="{{ asset('upload/subcategory') }}/{{ $subs->subcategory_image }}" alt="">
              </td>
              <td>
                  <a href="{{ route('subcategory.trash.restore',$subs->id) }}" title="Restore" type="button" class="btn btn-success btn-icon">
                      <i data-feather="rotate-cw"></i>
                  </a>
                  <a data-id="{{ route('subcategory.trash.delete',$subs->id) }}"  class="btn btn-danger btn-icon del">
                      <i data-feather="trash"></i>
                  </a>
              </td>
          </tr>
          @endforeach
      </table>
      <div class="mt-3">
        <button type="submit" class="btn btn-danger del_btn2 d-none"  name="button1" value="Button 1">Checked Delete</button>
      <button type="submit" class="btn btn-success del_btn3 d-none" name="button2" value="Button 2">Checked Restore</button>
  </div>
      </div>
    </div>
  </form>
 
    

</div>
@endsection

@section('script')
<script>
let del=document.querySelectorAll('.del')
let delete1= Array.from(del)
   delete1.map(item =>{
   item.onclick = function(){
  link= item.dataset.id;

  const swalWithBootstrapButtons = Swal.mixin({
  customClass: {
    confirmButton: "btn btn-success",
    cancelButton: "btn btn-danger"
  },
  buttonsStyling: false
});
swalWithBootstrapButtons.fire({
  title: "Are you sure?",
  text: "You won't be able to revert this!",
  icon: "warning",
  showCancelButton: true,
  confirmButtonText: "Yes, delete it!",
  cancelButtonText: "No, cancel!",
  reverseButtons: true
}).then((result) => {
  if (result.isConfirmed) {
      window.location.href=link
  } else if (
    /* Read more about handling dismissals below */
    result.dismiss === Swal.DismissReason.cancel
  ) {
    swalWithBootstrapButtons.fire({
      title: "Cancelled",
      text: "Your imaginary file is safe :)",
      icon: "error"
    });
  }
});

}
})
// checked section
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
</script>

@endsection