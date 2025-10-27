<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    function new_products(){
        $products=Product::with('rel_to_inventory')->latest()->take(4)->get();
        return response()->json([
          'new_prouducts'=>$products,
        ]);
    }

function all_products(){
    $products =Product::with('rel_to_inventory')->latest()->get();
    return response()->json([
        'all_products'=>$products,
    ]);
}
function products_detailes($id){
    $product_detailes = Product::with([
        'rel_to_inventory.rel_to_col',
        'rel_to_inventory.rel_to_size',
        'rel_to_gal',
        ])->find($id);
$tags_id =explode(',', $product_detailes->tag_id);
$tags =Tag::whereIn('id', $tags_id)->get();

$related_product = Product::with([
    'rel_to_inventory.rel_to_col',
    'rel_to_inventory.rel_to_size',
    'rel_to_gal',
])->where('category_id' , $product_detailes->category_id)->where('id','!=',$product_detailes->id)->get();

return response()->json([
    'product_detailes'=>$product_detailes,
    'tags'=>$tags,
    'related_product'=>$related_product,
]);
}
 function color(){
    $colors=Color::all();
    return response()->json([
        'colors'=>$colors
    ]);
  }

function scerch(Request $request){
$categoriesids =$request->input('category_ids',[]);
$colorsids =$request->input('color_ids',[]);
$minprice =$request->input('min_price',0);
$maxprice =$request->input('max_price',9999999);

$query =Product::query();
if(!empty($categoriesids)){
    $query->whereIn('category_id',$categoriesids);
}

if(!empty($colorsids || $minprice > 0 || $maxprice < 9999999)){
    $query->whereHas('rel_to_inventory', function($q) use ($colorsids,$minprice , $maxprice){
        $q->whereBetween('discount_price',[$minprice,$maxprice]);
        if(!empty($colorsids)){
             $q->whereIn('color_id', $colorsids);
        }
    });
}

$query->with(['rel_to_inventory' => function($q) use ($colorsids,$minprice,$maxprice){
$q->whereBetween('discount_price',[$minprice,$maxprice]);
if(!empty($colorsids)){
    $q->whereIn('color_id',$colorsids);
}
}]);

$products = $query->get();
return response()->json([
    'products'=>$products
]);


























}



}
