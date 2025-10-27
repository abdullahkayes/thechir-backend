<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RollController extends Controller
{
    function roll_manager(){
        $permissions=Permission::all();
        $rolls =Role::all();
        $users =User::all();
        return view('Backend.roll.roll', compact('permissions', 'rolls','users'));
    }
    function permission_create(Request $request){
      Permission::create(['name' => $request->permission]);
      return back()->with('success', 'Permission added successfully');
    }
    function roll_create(Request $request){
     $role = Role::create(['name' => $request->roll]);
     $role->givePermissionTo($request->permission);

      return back()->with('error', 'Roll added successfully');
    }
    function asign_roll(Request $request){
      $user= User::find($request->user_id);
      $user->assignRole($request->roll);
      return back()->with('error2', 'Roll assign successfully');
    }
  function roll_remove($id){
    DB::table('model_has_roles')->where('model_id',$id)->delete();
    return back();
  }


}
