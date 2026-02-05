<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use App\Models\Gallary;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\Brand;
use App\Models\Subcategory;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Html\Colors;

class ProductController extends Controller
{
    function product(){
        $category= Category::all();
        $subcategory= Subcategory::all();
        $colors= Color::all();
        $sizes= Size::all();
        $tag= Tag::all();
        $brands= Brand::all();
        return view('Backend.product.product',compact('category','subcategory','tag','colors','sizes','brands'));
    }
function accounts(){
    return view('Backend.Accounts.accounts');
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

        // Handle tags: manual input (comma separated)
        $tag_ids = [];
        if ($request->manual_tag) {
            $manual_tags = explode(',', $request->manual_tag);
            foreach ($manual_tags as $tag_name) {
                $tag_name = trim($tag_name);
                if ($tag_name) {
                    $tag = Tag::firstOrCreate(['tag_name' => $tag_name]);
                    $tag_ids[] = $tag->id;
                }
            }
        }
        $tags = implode(',', $tag_ids);

        // Handle size: manual input
        $size_id = null;
        if ($request->manual_size) {
            $size = Size::firstOrCreate(['size_name' => trim($request->manual_size)]);
            $size_id = $size->id;
        }

 $photo =$request->preview;
$extension = $photo->extension();
$file_name=uniqid().'.'.$extension;

$manager = new ImageManager(new Driver());
$image = $manager->read($photo);
$image->save(public_path('/upload/product/preview/'.$file_name));

        $product = Product::create([
         'sku'=>$request->sku,
         'product_name'=>$request->product_name,
         'category_id'=>$request->category_id,
         'subcategory_id'=>$request->subcategory_id,
         'brand_id'=>$request->brand_id,
         'tag_id'=>$tags,
         'short_desp'=>$request->short_desp,
          'long_desp'=>$request->long_desp,
          'slug'=>$slugs,
          'preview'=>"/upload/product/preview/$file_name",
        ]);
$product_id = $product->id;

// Handle multiple sizes - create inventory records for each selected size
if ($request->has('size_ids') && is_array($request->size_ids)) {
    foreach ($request->size_ids as $size_id) {
        ProductInventory::create([
            'product_id' => $product_id,
            'size_id' => $size_id,
            'color_id' => $request->color_id,
            'buy_price' => $request->buy_price,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'distributer_price' => $request->distributer_price,
            'wholesale_price' => $request->wholesale_price,
            'amazon_price' => $request->amazon_price,
            'reseller_price' => $request->reseller_price,
            'quantity' => $request->quantity,
            'weight_grams' => $request->weight_grams,
            'manufacture_date' => $request->manufacture_date,
            'expiry_date' => $request->expiry_date,
            'batch_number' => $request->batch_number,
        ]);
    }
} else {
    // Fallback to single size if no multiple sizes selected
    ProductInventory::create([
        'product_id' => $product_id,
        'size_id' => $request->size_id,
        'color_id' => $request->color_id,
        'buy_price' => $request->buy_price,
        'price' => $request->price,
        'discount_price' => $request->discount_price,
        'distributer_price' => $request->distributer_price,
        'wholesale_price' => $request->wholesale_price,
        'amazon_price' => $request->amazon_price,
        'reseller_price' => $request->reseller_price,
        'quantity' => $request->quantity,
        'weight_grams' => $request->weight_grams,
        'manufacture_date' => $request->manufacture_date,
        'expiry_date' => $request->expiry_date,
        'batch_number' => $request->batch_number,
    ]);
}
        $product_id = $product->id;
   foreach($request->gallary as $gallary){
    $extension2 = $gallary->extension();
    $file_name2=uniqid().'.'.$extension2;

    $manager = new ImageManager(new Driver());
    $image = $manager->read($gallary);
    $image->save(public_path('/upload/product/gallary/'.$file_name2));
    Gallary::insert([
       'product_id'=>$product_id,
       'gallary'=>"/upload/product/gallary/$file_name2",
    ]);
}
return back()->with('product','Product Added Successful');
      }

     function product_list(Request $request){
       $search = $request->get('search');
       $products = Product::when($search, function ($query, $search) {
           return $query->where('product_name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%');
       })->get(); // Show all products, no pagination
       return view('Backend.product.product_list', compact('products', 'search'));
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
        $previewPath = public_path('/upload/product/preview/' . $previewFile);
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
            $gPath = public_path('/upload/product/gallary/' . $gFilename);
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

   function product_edit($id){
       $product = Product::with('productInventory', 'rel_to_gal')->findOrFail($id);
       $category = Category::all();
       $subcategory = Subcategory::all();
       $colors = Color::all();
       $sizes = Size::all();
       $tag = Tag::all();
       $brands = Brand::all();
       
       // Get all sizes associated with this product for the edit view
       $selectedSizes = $product->productInventory ? $product->productInventory->pluck('size_id')->toArray() : [];
       
       return view('Backend.product.product_edit', compact('product', 'category', 'subcategory', 'tag', 'colors', 'sizes', 'brands', 'selectedSizes'));
   }

   function product_update(Request $request, $id){
       $product = Product::findOrFail($id);

       // Handle tags: manual input (comma separated)
       $tag_ids = [];
       if ($request->manual_tag) {
           $manual_tags = explode(',', $request->manual_tag);
           foreach ($manual_tags as $tag_name) {
               $tag_name = trim($tag_name);
               if ($tag_name) {
                   $tag = Tag::firstOrCreate(['tag_name' => $tag_name]);
                   $tag_ids[] = $tag->id;
               }
           }
       }
       $tags = implode(',', $tag_ids);

       // Handle preview image update
       $preview_url = $product->preview;
       if ($request->hasFile('preview')) {
           $photo = $request->preview;
           $extension = $photo->extension();
           $file_name = uniqid() . '.' . $extension;

           $manager = new ImageManager(new Driver());
           $image = $manager->read($photo);
           $image->save(public_path('/upload/product/preview/' . $file_name));

           // Delete old preview image
           if ($product->preview) {
               $old_preview = basename(parse_url($product->preview, PHP_URL_PATH));
               $old_path = public_path('/upload/product/preview/' . $old_preview);
               if (file_exists($old_path)) {
                   @unlink($old_path);
               }
           }

           $preview_url = "/upload/product/preview/$file_name";
       }

       // Update product
       $product->update([
           'sku' => $request->sku,
           'product_name' => $request->product_name,
           'category_id' => $request->category_id,
           'subcategory_id' => $request->subcategory_id,
           'brand_id' => $request->brand_id,
           'tag_id' => $tags,
           'short_desp' => $request->short_desp,
           'long_desp' => $request->long_desp,
           'preview' => $preview_url,
       ]);

       // Update product inventory - handle multiple sizes
       if ($request->has('size_ids') && is_array($request->size_ids)) {
           // Delete existing inventory records for this product
           ProductInventory::where('product_id', $id)->delete();
           
           // Create new inventory records for each selected size
           foreach ($request->size_ids as $size_id) {
               ProductInventory::create([
                   'product_id' => $id,
                   'size_id' => $size_id,
                   'color_id' => $request->color_id,
                   'buy_price' => $request->buy_price,
                   'price' => $request->price,
                   'discount_price' => $request->discount_price,
                   'distributer_price' => $request->distributer_price,
                   'wholesale_price' => $request->wholesale_price,
                   'amazon_price' => $request->amazon_price,
                   'reseller_price' => $request->reseller_price,
                   'quantity' => $request->quantity,
                   'weight_grams' => $request->weight_grams,
                   'manufacture_date' => $request->manufacture_date,
                   'expiry_date' => $request->expiry_date,
                   'batch_number' => $request->batch_number,
               ]);
           }
       } else {
           // Fallback to single size if no multiple sizes selected
           $inventory = ProductInventory::where('product_id', $id)->first();
           if ($inventory) {
               $inventory->update([
                   'size_id' => $request->size_id,
                   'color_id' => $request->color_id,
                   'buy_price' => $request->buy_price,
                   'price' => $request->price,
                   'discount_price' => $request->discount_price,
                   'distributer_price' => $request->distributer_price,
                   'wholesale_price' => $request->wholesale_price,
                   'amazon_price' => $request->amazon_price,
                   'reseller_price' => $request->reseller_price,
                   'quantity' => $request->quantity,
                   'weight_grams' => $request->weight_grams,
                   'manufacture_date' => $request->manufacture_date,
                   'expiry_date' => $request->expiry_date,
                   'batch_number' => $request->batch_number,
               ]);
           }
       }

       // Handle gallery images update
       if ($request->hasFile('gallary')) {
           // Delete old gallery images
           $old_galleries = Gallary::where('product_id', $id)->get();
           foreach ($old_galleries as $old_gal) {
               if ($old_gal->gallary) {
                   $old_gal_file = basename(parse_url($old_gal->gallary, PHP_URL_PATH));
                   $old_gal_path = public_path('/upload/product/gallary/' . $old_gal_file);
                   if (file_exists($old_gal_path)) {
                       @unlink($old_gal_path);
                   }
               }
               $old_gal->delete();
           }

           // Add new gallery images
           foreach ($request->gallary as $gallary) {
               $extension2 = $gallary->extension();
               $file_name2 = uniqid() . '.' . $extension2;

               $manager = new ImageManager(new Driver());
               $image = $manager->read($gallary);
               $image->save(public_path('/upload/product/gallary/' . $file_name2));

               Gallary::create([
                   'product_id' => $id,
                   'gallary' => "/upload/product/gallary/$file_name2",
               ]);
           }
       }

       return redirect()->route('product.list')->with('product', 'Product Updated Successfully');
   }

   public function show($slug): View
   {
       $product = Product::where('slug', $slug)->with('seo')->firstOrFail();

       // return blade that renders meta and bootstraps Vue with the product data
       return view('products.show', [
           'product' => $product,
       ]);
   }

  function show_seo(){
    return view('products.show');
  }

  public function seo_dashboard()
  {
      $products = Product::with('seo')->paginate(20); // Paginate to prevent memory issues

      $seoData = $products->map(function ($product) {
          $seo = $product->seo;
          $score = 0;
          $totalFields = 3; // title, description, image

          if ($seo && $seo->title) $score++;
          if ($seo && $seo->description) $score++;
          if ($seo && $seo->image) $score++;

          $percentage = ($score / $totalFields) * 100;

          return [
              'product' => $product,
              'seo' => $seo,
              'score' => $score,
              'percentage' => $percentage,
          ];
      });

      return view('seo.dashboard', compact('seoData', 'products')); // Pass products for pagination
  }
}
