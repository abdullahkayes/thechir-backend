<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SubcategoryController extends Controller
{
function subcategory(){
    $category=Category::all();
    $subcategory = Subcategory::all();
    return view('backend.subcategory.subcategory',compact('category','subcategory'));
}
function subcategory_add(Request $request){
    Subcategory::insert([
      'category_id'=>$request->category_id,
      'subcategory_name'=>$request->subcategory_name,
    ]);
    return back()->with('succ','Subcategory Added Successfull');
}
function subcategory_delete($id){
Subcategory::find($id)->delete();
return back();
}

function subcategory_trash(){
    $subactegor= Subcategory::onlyTrashed()->get();
    return view('Backend.subcategory.trash',compact('subactegor'));
}
function subcategory_trash_delete($id){
      $subcatgeory=Subcategory::onlyTrashed()->find($id);
      $delete_form= public_path( '/upload/subcategory/'.$subcatgeory->subcategory_image);
      unlink($delete_form);

   Subcategory::onlyTrashed()->find($id)->forceDelete();
   return back();
}
function subcategory_trash_restore($id){
Subcategory::onlyTrashed()->find($id)->restore();
return back();
}











}
