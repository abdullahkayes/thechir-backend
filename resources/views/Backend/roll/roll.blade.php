@extends('layouts.admin');
@section('content')
{{-- @can('Roll_manage') --}}
<div class="row">
    <div class="col-lg-8">
         <div class="card">
            <div class="card-header">
                <h2>Roll Permission List</h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
            <tr>
                <th>SL</th>
                <th>Roll Name</th>
                <th>Permissions</th>
                <th>Action</th>
            </tr>

            @foreach ($rolls as $sl=>$roll)
             <tr>
                <td>{{ $sl+1 }}</td>
                <td>{{ $roll->name }}</td>
                <td class='text-wrap'>
                @foreach ($roll->getPermissionNames() as $permission )
                  <span class="badge badge-primary my-2">{{ $permission }}</span>
                @endforeach
                </td>
             </tr>
            @endforeach
        </table>
            </div>
         </div>
        <div class="card mt-3">
            <div class="card-header">
                <h2>Roll Managers</h2>
            </div>
            <div class="card-body">
               <table class="table table-bordered">
            <tr>
                <th>SL</th>
                <th>User Name</th>
                <th>Roll</th>
                <th>Action</th>
            </tr>

            @foreach ($users as $sl=>$user)
             <tr>
                <td>{{ $sl+1 }}</td>
                <td>{{ $user->name }}</td>
                <td class='text-wrap'>
                @forelse ($user->getRoleNames() as $roll )
                  <span class="badge badge-primary my-2">{{ $roll }}</span>
                  @empty
                   <span class="badge badge-primary my-2"> Roll Not Asign</span>
                @endforelse
                </td>
              <td>
                <a href="{{ route('roll.remove',$user->id) }}" class=" btn btn-danger"> Roll Remove</a>
              </td>
             </tr>
            @endforeach
        </table>
            </div>
        </div>

    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                    <form action="{{ route('permissiom.create') }}" method="post">
             @csrf
             <div class="card">
                 @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="card-header">
                    <h3>Add Permission</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Permission Add</label>
                        <input type="text" name="permission" class="form-control">
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Add Color</button>
                    </div>
                </div>
            </div>
            </form>
            </div>
        </div>
        <div class="card mt-3">
               @if (session('error'))
            <div class="alert alert-success">{{ session('error') }}</div>
                @endif
            <div class="card-header">

                <h3>Add Roll</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('roll.create') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Add Roll</label>
                        <input type="text" name="roll" class="form-control">
                    </div>
                    <div class="mb-3">
                        @foreach ($permissions as $permission)
                        <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="permission{{ $permission->id }}" value="{{ $permission->name }}" name="permission[]">
                  <label class="form-check-label" for="permission{{ $permission->id }}">{{ $permission->name }}</label>
                 </div>
              @endforeach
                    </div>
                   <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Add Roll</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mt-3">
               @if (session('error2'))
            <div class="alert alert-success">{{ session('error2') }}</div>
                @endif
            <div class="card-header">

                <h3>Asign Rolls</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('asign.roll') }}" method="POST">
                    @csrf
                     <div class="mb-3">
                          <select name="roll" id="">
                        <option value="">Select Rolls</option>
                        @foreach ($rolls as $roll)
                         <option value="{{ $roll->name }}">{{ $roll->name }}</option>
              @endforeach
                </select>
                    </div>
                    <div class="mb-3">
                        <select name="user_id" id="">
                        <option value="">Select Users</option>
                        @foreach ($users as $user)
                         <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endforeach
                </select>
                </div>

                <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Asign Roll</button>
                    </div>
                </form>

            </div>
            </div>
        </div>
    </div>


{{-- @endcan --}}
    @endsection

<style>
    .form-check .form-check-label {
    min-height: 18px;
    display: block;
     margin-left: 1px !important;
    font-size: 0.875rem;
    line-height: 1.5;
}
</style>
