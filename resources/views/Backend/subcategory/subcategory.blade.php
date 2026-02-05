@extends('layouts.admin');
@section('content')
    <div class="row">
        @can('Subcategory_access')
             <div class="mb-3 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Subcategores</h4>
                </div>
               <div class="card-body">
                <div class="row">
                    @foreach ($category as $categor)
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ $categor->category_name }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>SL</th>
                                            <th>Subcategory Name</th>
                                            <th>Action</th>
                                        </tr>
                                        @foreach ($categor->rel_to_subcategory as $index=>$subcategor )
                                            <tr>
                                                <td>{{ $index+1 }}</td>
                                                <td>{{ $subcategor->subcategory_name }}</td>
                                                
                                                @can('Subcategory_delete')
                                                  <td>
                                                    <a href="{{ route('subcategory.delete',$subcategor->id) }}" class="btn btn-danger">Delete</a>
                                                </td>
                                                @endcan

                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
               </div>
            </div>
        </div>
        @endcan
@can('Subcategory_add')
   <div class="col-lg-4">
            <div class="card">
                @if(session('succ'))
                    <div class="alert alert-success">{{ session('succ') }}</div>
                @endif
                <div class="card-header">
                    <h4>Add Subcategory</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('subcategory.add') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <select name="category_id" class="form-control">
                            <option value="">Select Category</option>
                            @foreach ($category as $categor)
                                <option value="{{ $categor->id }}">{{ $categor->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subcategory Name</label>
                        <input type="text" name="subcategory_name" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Add Subcategory</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
@endcan

    </div>
@endsection
