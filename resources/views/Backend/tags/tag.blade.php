@extends('layouts.admin');
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3>Tags List</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                 <tr>
                    <th>SL</th>
                    <th>Tag Name</th>
                    <th>Action</th>
                 </tr>
                 @foreach ($tags as $index=>$tag )
                     <tr>
                        <td>{{ $index+1 }}</td>
                        <td>{{ $tag->tag_name }}</td>
                        <td>
                            <a href="{{ route('tag.delete',$tag->id) }}" class="btn btn-danger" >Delete</a>
                        </td>
                     </tr>
                 @endforeach
                </table>
            </div>
        </div>
    </div>  
    <div class="col-lg-4">
    <div class="card">
    <div class="card-header">
        <h3>Add New Tags</h3>
    </div>    
    <div class="card-body">
        <form action="{{ route('tag.add') }}" method="post">
            @csrf
          <div class="my-2 d-flex justify-content-between" >
            <label class="form-label">Add Tags</label>
            <button type="button" id="addbtn" class="badge badge-primary" style="border: 0; font-size:16px;">+Add</button>
          </div>
          <div class="here">
            <div class="mb-3">
                <input type="text" name="tag_name[]" class="form-control">
              </div>
          </div>        
         <div class="mb-3">
            <button type="submit" class="btn btn-primary">Add New Tags</button>
         </div>
        </form>
    </div>    
    </div>    
    </div>    
@endsection
@section('script')
    <script>
        let addbtn =document.querySelector('#addbtn')
        let here =document.querySelector('.here')
        addbtn.onclick=function(){
            let div =document.createElement('div')
            div.classList.add('mb-3')
            let input =document.createElement('input')
            input.setAttribute('type','text')
            input.setAttribute('name','tag_name[]')
            input.classList.add('form-control')
            div.appendChild(input)
            here.appendChild(div)
        }
    </script>
@endsection