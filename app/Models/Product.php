<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Product extends Model

{

   protected $guarded =['id'];

    use SoftDeletes;

    function rel_to_cat(){
        return $this->belongsTo(Category::class,'category_id');
    }
    function rel_to_sub(){
        return $this->belongsTo(Subcategory::class,'subcategory_id');
    }
    function rel_to_gal(){
        return $this->hasMany(Gallary::class,'product_id','id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function inventories() {
        return $this->hasMany(ProductInventory::class);
    }

    public function productInventory() {
        return $this->hasOne(ProductInventory::class);
    }

    public function stockDetails()
    {
        return $this->hasMany(StockDetail::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function seo()
    {
        return $this->morphOne(Seo::class, 'model');
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($product) {
    //         $product->addSEO();
    //         $seoData = $product->getDynamicSEOData();
    //         $product->seo->title = $seoData->title;
    //         $product->seo->description = $seoData->description;
    //         $product->seo->image = $seoData->image;
    //         $product->seo->author = $seoData->author;
    //         $product->seo->url = $seoData->url;
    //         $product->seo->published_time = $seoData->published_time;
    //         $product->seo->modified_time = $seoData->modified_time;
    //         $product->seo->save();
    //     });
    // }

    // /**
    //  * Optional: dynamically map product to SEOData.
    //  * Adjust fields (name, short_description, image path) to your schema.
    //  */
    // public function getDynamicSEOData(): SEOData
    // {
    //     // image path relative to public path (e.g. 'storage/products/1.jpg')
    //     $imagePath = $this->preview ? 'upload/product/preview/' . basename(parse_url($this->preview, PHP_URL_PATH)) : 'images/default-seo.jpg';

    //     return new SEOData(
    //         title: $this->product_name,
    //         description: $this->short_desp ?? $this->long_desp ?? config('seo.description.fallback'),
    //         author: null,
    //         image: $imagePath,
    //         url: url()->current(),
    //         published_time: $this->created_at ?? Carbon::now(),
    //         modified_time: $this->updated_at ?? Carbon::now(),
    //         // optionally add schema or alternates here
    //     );
    // }
}
