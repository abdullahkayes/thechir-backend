<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function category(){
        $categorys =Category::all();
       return view('Backend.category.category',compact('categorys'));
    }

    function category_add(Request $request){

        $photo =$request->category_image;
        $extension =$photo->extension();
        $file_name =uniqid().'.'.$extension;

        $manager = new ImageManager(new Driver());
        $image = $manager->read($photo);
        $image->save(public_path('upload/category/'.$file_name));

       Category::insert([
          'category_name'=>$request->category_name,
          'category_image'=>"http://127.0.0.1:8000/upload/category/$file_name",
       ]);
        return back()->with('category','Category Add Successful');


    }

    function category_delete($id){
   Category::find($id)->delete();
   return back();
    }

    function category_checked_delete(Request $request){

        foreach($request->category_id as $category){
        Category::find($category)->delete();
        }
        return back();
     }

     function category_trash(){
        $trashs= Category::onlyTrashed()->get();
        return view('Backend.category.trash',compact('trashs'));
     }
     function trash_restore($id){
       Category::onlyTrashed()->find($id)->restore();
       return back();
     }
     function trash_delete($id){
      $category=Category::onlyTrashed()->find($id);
      $delete_form=public_path('upload/category/'.$category->category_image);
      unlink($delete_form);

      $subs =Subcategory::where('category_id',$id)->get();
         foreach($subs as $subcategory){
           Subcategory::where('category_id', $subcategory->category_id)->update([
           'category_id'=>13,
           ]);
         }

      Category::onlyTrashed()->find($id)->forceDelete();
      return back()->with('errr','Category Deleted Parmenetly');
      }
     function category_trash_checked(Request $request){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
           if (isset($_POST['button1'])) {
              foreach($request->category_id as $category){
                 Category::onlyTrashed()->find($category)->forceDelete();
                 }
                 return back();
           } elseif (isset($_POST['button2'])) {
              foreach($request->category_id as $category2){
                 Category::onlyTrashed()->find($category2)->restore();
                 }
                 return back();
           }
       }

     }

}
