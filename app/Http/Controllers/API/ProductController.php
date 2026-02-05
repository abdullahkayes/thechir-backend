<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\Gallary;
use Intervention\Image\Facades\Image;
use App\Models\Category;
use App\Models\Size;

class ProductController extends Controller
{
    /**
     * Make image paths relative by removing old hostnames
     */
    private function makeRelativePath($path) {
        if (!$path) return $path;
        // Remove any hostname if present
        $hostnames = ['http://127.0.0.2:8000', 'http://127.0.0.2:8000'];
        foreach ($hostnames as $hostname) {
            if (str_starts_with($path, $hostname)) {
                return substr($path, strlen($hostname));
            }
        }
        // If it's already relative, return as is
        return $path;
    }

    /**
     * Safely get authenticated user from any guard
     */
    private function getAuthenticatedUser()
    {
        // First try the standard guards
        $guards = ['reseller', 'b2b', 'distributer', 'amazon', 'sanctum'];

        foreach ($guards as $guard) {
            if (auth($guard)->check()) {
                return auth($guard)->user();
            }
        }

        // If guards don't work, manually check the token
        $token = request()->bearerToken();
        if ($token) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
                // Check if it's one of our user types
                if ($user instanceof \App\Models\Reseller ||
                    $user instanceof \App\Models\B2b ||
                    $user instanceof \App\Models\Distributer ||
                    $user instanceof \App\Models\Amazon ||
                    $user instanceof \App\Models\User) {
                    return $user;
                }
            }
        }

        return null;
    }
       function new_products(){
         $products = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->latest()->take(4)->get();

         // Process products to add color/size arrays for frontend compatibility
         $processedProducts = [];
         foreach($products as $product) {
             $inventories = $product->inventories ?? collect();
             $productData = $product->toArray();

             // Make image paths relative
             $productData['preview'] = $this->makeRelativePath($productData['preview']);
             if (isset($productData['rel_to_gal']) && is_array($productData['rel_to_gal'])) {
                 foreach ($productData['rel_to_gal'] as &$gal) {
                     $gal['gallary'] = $this->makeRelativePath($gal['gallary']);
                 }
             }

             if ($inventories->isNotEmpty()) {
                 $colors = $inventories->pluck('color')->unique()->filter()->values();
                 $sizes = $inventories->pluck('size')->unique()->filter()->values();

                 $productData['rel_to_color'] = $colors->toArray();
                 $productData['rel_to_size'] = $sizes->toArray();
                 $productData['inventories'] = $inventories->toArray();
             } else {
                 $productData['rel_to_color'] = [];
                 $productData['rel_to_size'] = [];
                 $productData['inventories'] = [];
             }

             $processedProducts[] = $productData;
         }

         return response()->json(['new_prouducts' => $processedProducts]);
     }

    function getAllProducts(Request $request){
        // Return all products with relationships - let frontend handle pricing
        $products = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->get();

        // If no products in database, return dummy data for testing
        if ($products->isEmpty()) {
            return response()->json([
                [
                    'id' => 1,
                    'product_name' => 'Test Product - No Products in Database',
                    'preview' => '/placeholder.png',
                    'short_desp' => 'This is a test product because no products exist in the database',
                    'price' => 100,
                    'discount_price' => 120,
                    'wholesale_price' => 80,
                    'reseller_price' => 90,
                    'distributer_price' => 85,
                    'amazon_price' => 95,
                    'rel_to_color' => [],
                    'rel_to_size' => [],
                    'inventories' => [],
                    'rel_to_gal' => []
                ]
            ]);
        }

        // Process products to add color/size arrays for frontend compatibility
        $processedProducts = [];
        foreach($products as $product) {
            $inventories = $product->inventories ?? collect();
            $productData = $product->toArray();

            // Make image paths relative
            $productData['preview'] = $this->makeRelativePath($productData['preview']);
            if (isset($productData['rel_to_gal']) && is_array($productData['rel_to_gal'])) {
                foreach ($productData['rel_to_gal'] as &$gal) {
                    $gal['gallary'] = $this->makeRelativePath($gal['gallary']);
                }
            }

            if ($inventories->isNotEmpty()) {
                $colors = $inventories->pluck('color')->unique()->filter()->values();
                $sizes = $inventories->pluck('size')->unique()->filter()->values();

                $productData['rel_to_color'] = $colors->toArray();
                $productData['rel_to_size'] = $sizes->toArray();
                $productData['inventories'] = $inventories->toArray();

                // Add price fields from inventories
                $productData['price'] = $inventories->min('price') ?? 0;
                $productData['discount_price'] = $inventories->min('discount_price') ?? 0;
                $productData['wholesale_price'] = $inventories->min('wholesale_price') ?? 0;
                $productData['reseller_price'] = $inventories->min('reseller_price') ?? 0;
                $productData['distributer_price'] = $inventories->min('distributer_price') ?? 0;
                $productData['amazon_price'] = $inventories->min('amazon_price') ?? 0;
            } else {
                $productData['rel_to_color'] = [];
                $productData['rel_to_size'] = [];
                $productData['inventories'] = [];
                $productData['price'] = 0;
                $productData['discount_price'] = 0;
                $productData['wholesale_price'] = 0;
                $productData['reseller_price'] = 0;
                $productData['distributer_price'] = 0;
                $productData['amazon_price'] = 0;
            }

            $processedProducts[] = $productData;
        }

        return response()->json($processedProducts);
    }

    function searchProducts(Request $request){
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['products' => []]);
        }

        // Search in product name and description, limit results for performance
        $products = Product::with(['rel_to_gal', 'inventories'])
            ->where(function($q) use ($query) {
                $q->where('product_name', 'LIKE', '%' . $query . '%')
                  ->orWhere('short_desp', 'LIKE', '%' . $query . '%')
                  ->orWhere('long_desp', 'LIKE', '%' . $query . '%');
            })
            ->limit(10)
            ->get();

        // Process products to include inventory prices
        $processedProducts = [];
        foreach($products as $product) {
            $productData = $product->toArray();
            $inventories = $product->inventories ?? collect();

            if ($inventories->isNotEmpty()) {
                $productData['price'] = $inventories->min('price') ?? 0;
                $productData['discount_price'] = $inventories->min('discount_price') ?? 0;
                $productData['wholesale_price'] = $inventories->min('wholesale_price') ?? 0;
                $productData['reseller_price'] = $inventories->min('reseller_price') ?? 0;
                $productData['distributer_price'] = $inventories->min('distributer_price') ?? 0;
                $productData['amazon_price'] = $inventories->min('amazon_price') ?? 0;
            } else {
                $productData['price'] = 0;
                $productData['discount_price'] = 0;
                $productData['wholesale_price'] = 0;
                $productData['reseller_price'] = 0;
                $productData['distributer_price'] = 0;
                $productData['amazon_price'] = 0;
            }

            $processedProducts[] = $productData;
        }

        return response()->json(['products' => $processedProducts]);
    }

 function products_detailes(Request $request, $id){
   $product_detailes = Product::with([
       'rel_to_gal',
       'inventories.size',
       'inventories.color'
       ])->find($id);

   if (!$product_detailes) {
       return response()->json(['error' => 'Product not found'], 404);
   }

   // Make image paths relative
   $product_detailes->preview = $this->makeRelativePath($product_detailes->preview);
   if ($product_detailes->rel_to_gal) {
       foreach ($product_detailes->rel_to_gal as $gal) {
           $gal->gallary = $this->makeRelativePath($gal->gallary);
       }
   }

   // Load inventory data
   $inventories = $product_detailes->inventories;

   // Extract unique colors and sizes from inventories
   $colors = $inventories->pluck('color')->unique()->filter()->values();
   $sizes = $inventories->pluck('size')->unique()->filter()->values();

   // Attach to product for frontend compatibility
   $product_detailes->rel_to_color = $colors->toArray();
   $product_detailes->rel_to_size = $sizes->toArray();
   $product_detailes->inventories = $inventories->toArray();

$tags_id =explode(',', $product_detailes->tag_id);
$tags =Tag::whereIn('id', $tags_id)->get();

$related_product = Product::with([
    'rel_to_gal',
    'inventories.size',
    'inventories.color'
])->where('category_id' , $product_detailes->category_id)->where('id','!=',$product_detailes->id)->take(4)->get();

// Process related products for frontend compatibility
foreach ($related_product as $product) {
    // Make image paths relative for related products
    $product->preview = $this->makeRelativePath($product->preview);
    if ($product->rel_to_gal) {
        foreach ($product->rel_to_gal as $gal) {
            $gal->gallary = $this->makeRelativePath($gal->gallary);
        }
    }

    $inventories = $product->inventories;
    $colors = $inventories->pluck('color')->unique()->filter()->values();
    $sizes = $inventories->pluck('size')->unique()->filter()->values();
    $product->rel_to_color = $colors->toArray();
    $product->rel_to_size = $sizes->toArray();
    $product->inventories = $inventories->toArray();
}

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
  $categoriesids = array_map('intval', $request->input('category_ids',[]));
  $brand_ids = array_map('intval', $request->input('brand_ids',[]));
  $minprice =(float)$request->input('min_price',0);
  $maxprice =(float)$request->input('max_price',9999999);

  $query =Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->query();
  if(!empty($categoriesids)){
      $query->whereIn('category_id',$categoriesids);
  }
  if(!empty($brand_ids)){
      $query->whereIn('brand_id',$brand_ids);
  }

  $products = $query->get();

  // Filter products based on price range
  if($minprice > 0 || $maxprice < 1500){
      $products = $products->filter(function($product) use ($minprice, $maxprice) {
          $inventories = $product->inventories;

          // Check price filter - use appropriate price based on user type
          $user = auth('sanctum')->user();
          $hasMatchingPrice = $inventories->contains(function($inventory) use ($minprice, $maxprice, $user) {
            if ($user && $user instanceof \App\Models\Reseller && $user->status === 'approved') {
                $price = $inventory->reseller_price ?? $inventory->discount_price ?? $inventory->price;
            } elseif ($user && $user instanceof \App\Models\B2b && $user->status === 'approved') {
                $price = $inventory->wholesale_price ?? $inventory->discount_price ?? $inventory->price;
            } elseif ($user && $user instanceof \App\Models\Distributer && $user->status === 'approved') {
                $price = $inventory->distributer_price ?? $inventory->discount_price ?? $inventory->price;
            } elseif ($user && $user instanceof \App\Models\Amazon && $user->status === 'approved') {
                $price = $inventory->wholesale_price ?? $inventory->discount_price ?? $inventory->price;
            } else {
                $price = $inventory->discount_price ?? $inventory->price;
            }
            return $price && $price >= $minprice && $price <= $maxprice;
        });
          return $hasMatchingPrice;
      })->values();

      // Attach inventory data for filtered products
      foreach($products as $product) {
          $inventories = $product->inventories;
          $colors = $inventories->pluck('color')->unique()->filter()->values();
          $sizes = $inventories->pluck('size')->unique()->filter()->values();
          $product->rel_to_color = $colors->toArray();
          $product->rel_to_size = $sizes->toArray();
          $product->inventories = $inventories->toArray();
      }
  }


  return response()->json([
      'products'=>$products
  ]);
}
    function products_by_subcategory($id){
        try {
            $products = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->where('subcategory_id', $id)->get();

            // Process products to add color/size arrays for frontend compatibility
            $processedProducts = [];
            foreach($products as $product) {
                $inventories = $product->inventories ?? collect();
                $productData = $product->toArray();

                if ($inventories->isNotEmpty()) {
                    $colors = $inventories->pluck('color')->unique()->filter()->values();
                    $sizes = $inventories->pluck('size')->unique()->filter()->values();
    
                    $productData['rel_to_color'] = $colors->toArray();
                    $productData['rel_to_size'] = $sizes->toArray();
                    $productData['inventories'] = $inventories->toArray();
    
                    // Add price from first inventory
                    $firstInventory = $inventories->first();
                    $productData['price'] = $firstInventory->price ?? 0;
                    $productData['discount_price'] = $firstInventory->discount_price ?? 0;
                } else {
                    $productData['rel_to_color'] = [];
                    $productData['rel_to_size'] = [];
                    $productData['inventories'] = [];
                    $productData['price'] = 0;
                    $productData['discount_price'] = 0;
                }

                $processedProducts[] = $productData;
            }

            return response()->json($processedProducts);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function subcategory_with_products($id){
        $subcategory = Subcategory::find($id);
        $products = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->where('subcategory_id', $id)->get();

        // Process products to add color/size arrays and price fields for frontend compatibility
        $processedProducts = [];
        foreach($products as $product) {
            $inventories = $product->inventories ?? collect();
            $productData = $product->toArray();

            if ($inventories->isNotEmpty()) {
                $colors = $inventories->pluck('color')->unique()->filter()->values();
                $sizes = $inventories->pluck('size')->unique()->filter()->values();

                $productData['rel_to_color'] = $colors->toArray();
                $productData['rel_to_size'] = $sizes->toArray();
                $productData['inventories'] = $inventories->toArray();

               // Extract price information from inventories if not already in product
               if (!isset($productData['price']) || $productData['price'] == 0) {
                   $prices = $inventories->pluck('price')->filter()->unique()->sort();
                   $productData['price'] = $prices->first() ?? 0;
               }
               if (!isset($productData['discount_price']) || $productData['discount_price'] == 0) {
                   $discountPrices = $inventories->pluck('discount_price')->filter()->unique()->sort();
                   $productData['discount_price'] = $discountPrices->first() ?? $productData['price'] ?? 0;
               }
               $productData['display_price'] = $productData['price'];
               $productData['original_price'] = $productData['discount_price'];

               // Extract user-specific prices from inventories
               $wholesalePrices = $inventories->pluck('b2b_price')->filter()->unique()->sort();
               $productData['wholesale_price'] = $wholesalePrices->first() ?? 0;
               $resellerPrices = $inventories->pluck('reseller_price')->filter()->unique()->sort();
               $productData['reseller_price'] = $resellerPrices->first() ?? 0;
               $distributerPrices = $inventories->pluck('distributer_price')->filter()->unique()->sort();
               $productData['distributer_price'] = $distributerPrices->first() ?? 0;
               $amazonPrices = $inventories->pluck('amazon_price')->filter()->unique()->sort();
               $productData['amazon_price'] = $amazonPrices->first() ?? 0;
           } else {
               $productData['rel_to_color'] = [];
               $productData['rel_to_size'] = [];
               $productData['inventories'] = [];
               $productData['price'] = 0;
               $productData['discount_price'] = 0;
               $productData['display_price'] = 0;
               $productData['original_price'] = 0;
               $productData['wholesale_price'] = 0;
               $productData['reseller_price'] = 0;
               $productData['distributer_price'] = 0;
               $productData['amazon_price'] = 0;
           }

            $processedProducts[] = $productData;
        }

        return response()->json([
            'subcategory' => $subcategory,
            'products' => $processedProducts,
        ]);
    }

    function products_by_category($id){
        try {
            $products = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->where('category_id', $id)->get();

            // Process products to add color/size arrays for frontend compatibility
            $processedProducts = [];
            foreach($products as $product) {
                $inventories = $product->inventories ?? collect();
                $productData = $product->toArray();

                if ($inventories->isNotEmpty()) {
                    $colors = $inventories->pluck('color')->unique()->filter()->values();
                    $sizes = $inventories->pluck('size')->unique()->filter()->values();
    
                    $productData['rel_to_color'] = $colors->toArray();
                    $productData['rel_to_size'] = $sizes->toArray();
                    $productData['inventories'] = $inventories->toArray();
    
                    $productData['price'] = $inventories->min('price') ?? 0;
                    $productData['discount_price'] = $inventories->min('discount_price') ?? 0;
                    $productData['wholesale_price'] = $inventories->min('wholesale_price') ?? 0;
                    $productData['reseller_price'] = $inventories->min('reseller_price') ?? 0;
                    $productData['distributer_price'] = $inventories->min('distributer_price') ?? 0;
                    $productData['amazon_price'] = $inventories->min('amazon_price') ?? 0;
                } else {
                    $productData['rel_to_color'] = [];
                    $productData['rel_to_size'] = [];
                    $productData['inventories'] = [];
                    $productData['price'] = 0;
                    $productData['discount_price'] = 0;
                    $productData['wholesale_price'] = 0;
                    $productData['reseller_price'] = 0;
                    $productData['distributer_price'] = 0;
                    $productData['amazon_price'] = 0;
                }

                $processedProducts[] = $productData;
            }

            return response()->json($processedProducts);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function products_by_brand($id){
        try {
            $products = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->where('brand_id', $id)->get();

            // Process products to add color/size arrays for frontend compatibility
            $processedProducts = [];
            foreach($products as $product) {
                $inventories = $product->inventories ?? collect();
                $productData = $product->toArray();

                if ($inventories->isNotEmpty()) {
                    $colors = $inventories->pluck('color')->unique()->filter()->values();
                    $sizes = $inventories->pluck('size')->unique()->filter()->values();

                    $productData['rel_to_color'] = $colors->toArray();
                    $productData['rel_to_size'] = $sizes->toArray();
                    $productData['inventories'] = $inventories->toArray();
                } else {
                    $productData['rel_to_color'] = [];
                    $productData['rel_to_size'] = [];
                    $productData['inventories'] = [];
                }

                $processedProducts[] = $productData;
            }

            return response()->json($processedProducts);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function category_with_products($id){
        $category = Category::find($id);
        $products = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->where('category_id', $id)->get();

        // Process products to add color/size arrays for frontend compatibility
        $processedProducts = [];
        foreach($products as $product) {
            $inventories = $product->inventories ?? collect();
            $productData = $product->toArray();

            if ($inventories->isNotEmpty()) {
                $colors = $inventories->pluck('color')->unique()->filter()->values();
                $sizes = $inventories->pluck('size')->unique()->filter()->values();

                $productData['rel_to_color'] = $colors->toArray();
                $productData['rel_to_size'] = $sizes->toArray();
                $productData['inventories'] = $inventories->toArray();
            } else {
                $productData['rel_to_color'] = [];
                $productData['rel_to_size'] = [];
                $productData['inventories'] = [];
            }

            $processedProducts[] = $productData;
        }

        return response()->json([
            'category' => $category,
            'products' => $processedProducts,
        ]);
    }

    function update(Request $request, $id){
        try {
            $product = Product::findOrFail($id);

            // Validate request data
            $request->validate([
                'product_name' => 'required|string|max:255',
                'sku' => 'required|string|max:255|unique:products,sku,' . $id,
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'nullable|exists:subcategories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'short_desp' => 'nullable|string',
                'long_desp' => 'nullable|string',
                'preview' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gallary.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

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

                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
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

                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $image = $manager->read($gallary);
                    $image->save(public_path('/upload/product/gallary/' . $file_name2));

                    Gallary::create([
                        'product_id' => $id,
                        'gallary' => "/upload/product/gallary/$file_name2",
                    ]);
                }
            }

            // Load updated product with relationships
            $updatedProduct = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->find($id);

            // Attach inventory data
            $inventories = $updatedProduct->inventories;
            $colors = $inventories->pluck('color')->unique()->filter()->values();
            $sizes = $inventories->pluck('size')->unique()->filter()->values();
            $updatedProduct->rel_to_color = $colors->toArray();
            $updatedProduct->rel_to_size = $sizes->toArray();
            $updatedProduct->inventories = $inventories->toArray();

            // Set pricing based on user type
            $user = auth('sanctum')->user();
            if ($user && $user instanceof \App\Models\Reseller && $user->status === 'approved') {
                $updatedProduct->display_price = $inventories->min('reseller_price') ?? $inventories->min('discount_price') ?? $inventories->min('price');
                $updatedProduct->original_price = null;
            } elseif ($user && $user instanceof \App\Models\B2b && $user->status === 'approved') {
                $updatedProduct->display_price = $inventories->min('wholesale_price') ?? $inventories->min('discount_price') ?? $inventories->min('price');
                $updatedProduct->original_price = null;
            } elseif ($user && $user instanceof \App\Models\Distributer && $user->status === 'approved') {
                $updatedProduct->display_price = $inventories->min('distributer_price') ?? $inventories->min('discount_price') ?? $inventories->min('price');
                $updatedProduct->original_price = null;
            } elseif ($user && $user instanceof \App\Models\Amazon && $user->status === 'approved') {
                $updatedProduct->display_price = $inventories->min('wholesale_price') ?? $inventories->min('discount_price') ?? $inventories->min('price');
                $updatedProduct->original_price = null;
            } else {
                $discountPrice = $inventories->min('discount_price');
                $regularPrice = $inventories->min('price');
                $updatedProduct->display_price = $discountPrice ?? $regularPrice;
                $updatedProduct->original_price = $discountPrice ? $regularPrice : null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'product' => $updatedProduct
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ], 500);
        }
    }

    function all_products(){
        // Return all products with relationships - let frontend handle pricing
        $products = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])->get();

        // Process products to add color/size arrays for frontend compatibility
        $processedProducts = [];
        foreach($products as $product) {
            $inventories = $product->inventories ?? collect();
            $productData = $product->toArray();

            // Make image paths relative
            $productData['preview'] = $this->makeRelativePath($productData['preview']);
            if (isset($productData['rel_to_gal']) && is_array($productData['rel_to_gal'])) {
                foreach ($productData['rel_to_gal'] as &$gal) {
                    $gal['gallary'] = $this->makeRelativePath($gal['gallary']);
                }
            }

            if ($inventories->isNotEmpty()) {
                $colors = $inventories->pluck('color')->unique()->filter()->values();
                $sizes = $inventories->pluck('size')->unique()->filter()->values();

                $productData['rel_to_color'] = $colors->toArray();
                $productData['rel_to_size'] = $sizes->toArray();
                $productData['inventories'] = $inventories->toArray();

                // Add price fields from inventories
                $productData['price'] = $inventories->min('price') ?? 0;
                $productData['discount_price'] = $inventories->min('discount_price') ?? 0;
                $productData['wholesale_price'] = $inventories->min('wholesale_price') ?? 0;
                $productData['reseller_price'] = $inventories->min('reseller_price') ?? 0;
                $productData['distributer_price'] = $inventories->min('distributer_price') ?? 0;
                $productData['amazon_price'] = $inventories->min('amazon_price') ?? 0;
            } else {
                $productData['rel_to_color'] = [];
                $productData['rel_to_size'] = [];
                $productData['inventories'] = [];
                $productData['price'] = 0;
                $productData['discount_price'] = 0;
                $productData['wholesale_price'] = 0;
                $productData['reseller_price'] = 0;
                $productData['distributer_price'] = 0;
                $productData['amazon_price'] = 0;
            }

            $processedProducts[] = $productData;
        }

        return response()->json($processedProducts);
    }

    function new_arrivals(){
        $products = Product::with(['rel_to_gal', 'inventories.size', 'inventories.color'])
            ->latest()
            ->take(8)
            ->get();

        // Process products to add color/size arrays for frontend compatibility
        $processedProducts = [];
        foreach($products as $product) {
            $inventories = $product->inventories ?? collect();
            $productData = $product->toArray();

            // Make image paths relative
            $productData['preview'] = $this->makeRelativePath($productData['preview']);
            if (isset($productData['rel_to_gal']) && is_array($productData['rel_to_gal'])) {
                foreach ($productData['rel_to_gal'] as &$gal) {
                    $gal['gallary'] = $this->makeRelativePath($gal['gallary']);
                }
            }

            if ($inventories->isNotEmpty()) {
                $colors = $inventories->pluck('color')->unique()->filter()->values();
                $sizes = $inventories->pluck('size')->unique()->filter()->values();

                $productData['rel_to_color'] = $colors->toArray();
                $productData['rel_to_size'] = $sizes->toArray();
                $productData['inventories'] = $inventories->toArray();

                // Add price fields from inventories
                $productData['price'] = $inventories->min('price') ?? 0;
                $productData['discount_price'] = $inventories->min('discount_price') ?? 0;
                $productData['wholesale_price'] = $inventories->min('wholesale_price') ?? 0;
                $productData['reseller_price'] = $inventories->min('reseller_price') ?? 0;
                $productData['distributer_price'] = $inventories->min('distributer_price') ?? 0;
                $productData['amazon_price'] = $inventories->min('amazon_price') ?? 0;
            } else {
                $productData['rel_to_color'] = [];
                $productData['rel_to_size'] = [];
                $productData['inventories'] = [];
                $productData['price'] = 0;
                $productData['discount_price'] = 0;
                $productData['wholesale_price'] = 0;
                $productData['reseller_price'] = 0;
                $productData['distributer_price'] = 0;
                $productData['amazon_price'] = 0;
            }

            $processedProducts[] = $productData;
        }

        return response()->json([
            'new_arrivals' => $processedProducts
        ]);
    }


    
}

