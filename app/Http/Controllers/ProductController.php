<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Gallary;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Html\Colors;

class ProductController extends Controller
{
    function product(){
        $category= Category::all();
        $subcategory= Subcategory::all();
        $tag= Tag::all();
        return view('Backend.product.product',compact('category','subcategory','tag'));
    }
    function getSubcategory(Request $request){
        $sub=Subcategory::where('category_id',$request->category_id)->get();
          $str ='<option value="" >Select Category</option>';
            foreach($sub as $subcategory){
                $str .='<option value="'.$subcategory->category_id.'" >'.$subcategory->subcategory_name.'</option>';
            }
            echo $str ;
      }

      function product_store(Request $request){
        $slugs=str_replace('  ', '-',Str::lower($request->product_name).random_int(2000000,10000000));
        $tags=implode(',' , $request->tag_id);

 $photo =$request->preview;
$extension = $photo->extension();
$file_name=uniqid().'.'.$extension;

$manager = new ImageManager(new Driver());
$image = $manager->read($photo);
$image->save(public_path('upload/product/preview/'.$file_name));

        $product_id = Product::insertGetId([
         'sku'=>$request->sku,
         'product_name'=>$request->product_name,
         'category_id'=>$request->category_id,
         'subcategory_id'=>$request->subcategory_id,
         'tag_id'=>$tags,
         'short_desp'=>$request->short_desp,
          'long_desp'=>$request->long_desp,
          'slug'=>$slugs,
          'preview'=>"http://127.0.0.1:8000/upload/product/preview/$file_name",
          'created_at'=>Carbon::now(),
        ]);
   foreach($request->gallary as $gallary){
    $extension2 = $gallary->extension();
    $file_name2=uniqid().'.'.$extension2;

    $manager = new ImageManager(new Driver());
    $image = $manager->read($gallary);
    $image->save(public_path('upload/product/gallary/'.$file_name2));
    Gallary::insert([
       'product_id'=>$product_id,
       'gallary'=>"http://127.0.0.1:8000/upload/product/gallary/$file_name2",
    ]);
}
return back()->with('product','Product Added Successful');
      }
     function product_list(){
        $products =Product::all();
        return view('Backend.product.product_list',compact('products'));
     }
   function product_delete($id){

      Product::find($id)->delete();
      return back();

   }
   function product_trash(){
       $products=Product::onlyTrashed()->get();
       return view('Backend.product.trash',compact('products'));
   }


// ...existing code...
function product_trash_delete($id){
    $product = Product::onlyTrashed()->find($id);
    if (! $product) {
        return back()->with('error', 'Trashed product not found.');
    }

    // remove preview file (extract filename from stored URL/path)
    $preview = $product->preview ?? '';
    $previewFile = $preview ? basename(parse_url($preview, PHP_URL_PATH)) : null;
    if ($previewFile) {
        $previewPath = public_path('upload/product/preview/' . $previewFile);
        if (file_exists($previewPath)) {
            @unlink($previewPath);
        }
    }

    // remove gallery files
    $galleries = Gallary::where('product_id', $id)->get();
    foreach ($galleries as $g) {
        $gFile = $g->gallary ?? '';
        $gFilename = $gFile ? basename(parse_url($gFile, PHP_URL_PATH)) : null;
        if ($gFilename) {
            $gPath = public_path('upload/product/gallary/' . $gFilename);
            if (file_exists($gPath)) {
                @unlink($gPath);
            }
        }
        // optionally remove gallery DB row (forceDelete not needed if cascade or handled elsewhere)
        $g->delete();
    }

    $product->forceDelete();
    return back()->with('success', 'Product permanently deleted.');
}
// ...existing code...

   function product_trash_restore($id){
    Product::onlyTrashed()->find($id)->restore();
    return back();
   }

   function product_view($id){
           $products =Product::find($id)->all();
           return view('Backend.product.view',compact('products'));
   }

}
