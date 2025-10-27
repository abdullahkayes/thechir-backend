<?php

namespace App\Http\Controllers;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use App\Models\User;
use Illuminate\Http\Request;

class BackendController extends Controller
{
    function users(){
        $users= User::all();
        return view('backend.users.user',compact('users'));
    }
    function users_delete($id){
        User::find($id)->delete();
        return back();
    }

    function user_edit(){
        return view('backend.users.edit');
    }

   function user_update( Request $request){
    User::find(Auth::id())->update([
  'name'=>$request->name,
  'email'=>$request->email,
    ]);
    return back();
   }

   function user_photo(Request $request){
  
      $photo= $request->photo;
      $extension = $photo->extension();
      $file_name =uniqid().'.'.$extension;
    
      if(Auth::user()->photo != null){
         $delete_from= public_path('upload/user/'.Auth::user()->photo);
         unlink($delete_from);
      }
      $manager = new ImageManager(new Driver());
      $image = $manager->read($photo);
      $image->scale(width: 300);
      $image->save(public_path('upload/user/'.$file_name));
      
      User::find(Auth::id())->update([
      'photo'=>$file_name,
      ]);
      return back()->with('photo',' Photo Changed Successfully');
    }

    function user_password(Request $request){
$request->validate([
'current_password'=>'required',
'password'=>['required',Password::min(8)
->letters()
->mixedCase()
->numbers()
->symbols()],
'password_confirmation'=>'required',
]);

if(password_verify($request->current_password , Auth::user()->password)){
if($request->password == $request->password_confirmation){
User::find(Auth::id())->update([
'password'=>$request->password
]);
return back()->with('passs','Password Update Successful');
}
else{
    return back()->with('password_con','Confirm Password Dose Not Match with New Password ');
}
}
else{
    return back()->with('pass_err','Current Password Dose Not Match');
}
 }

















}