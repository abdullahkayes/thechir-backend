<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Gallary;
use App\Models\ProductInventory;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Color;
use App\Models\Size;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Create sample categories if not exist
        $category1 = Category::firstOrCreate(['category_name' => 'Electronics']);
        $category2 = Category::firstOrCreate(['category_name' => 'Clothing']);

        // Create sample subcategories
        $subcategory1 = Subcategory::firstOrCreate(['subcategory_name' => 'Smartphones', 'category_id' => $category1->id]);
        $subcategory2 = Subcategory::firstOrCreate(['subcategory_name' => 'T-Shirts', 'category_id' => $category2->id]);

        // Create sample colors
        $color1 = Color::firstOrCreate(['color_name' => 'Black']);
        $color2 = Color::firstOrCreate(['color_name' => 'White']);

        // Create sample sizes
        $size1 = Size::firstOrCreate(['size_name' => 'M']);
        $size2 = Size::firstOrCreate(['size_name' => 'L']);

        // Create sample products
        $product1 = Product::create([
            'product_name' => 'Sample Smartphone',
            'subtitle' => 'Latest model',
            'short_desp' => 'A great smartphone',
            'long_desp' => 'Detailed description of the smartphone',
            'preview' => '/upload/product/preview/smartphone.jpg',
            'category_id' => $category1->id,
            'subcategory_id' => $subcategory1->id,
            'tag_id' => '1,2',
            'price' => 500,
            'discount_price' => 450,
        ]);

        $product2 = Product::create([
            'product_name' => 'Sample T-Shirt',
            'subtitle' => 'Comfortable wear',
            'short_desp' => 'A nice t-shirt',
            'long_desp' => 'Detailed description of the t-shirt',
            'preview' => '/upload/product/preview/tshirt.jpg',
            'category_id' => $category2->id,
            'subcategory_id' => $subcategory2->id,
            'tag_id' => '3,4',
            'price' => 20,
            'discount_price' => 15,
        ]);

        // Create galleries
        Gallary::create([
            'product_id' => $product1->id,
            'photo_name' => 'smartphone1.jpg',
            'photo' => '/upload/product/gallery/smartphone1.jpg',
        ]);

        Gallary::create([
            'product_id' => $product2->id,
            'photo_name' => 'tshirt1.jpg',
            'photo' => '/upload/product/gallery/tshirt1.jpg',
        ]);

        // Create inventories
        ProductInventory::create([
            'product_id' => $product1->id,
            'color_id' => $color1->id,
            'size_id' => $size1->id,
            'price' => 500,
            'discount_price' => 450,
            'quantity' => 10,
        ]);

        ProductInventory::create([
            'product_id' => $product2->id,
            'color_id' => $color2->id,
            'size_id' => $size2->id,
            'price' => 20,
            'discount_price' => 15,
            'quantity' => 20,
        ]);
    }
}
