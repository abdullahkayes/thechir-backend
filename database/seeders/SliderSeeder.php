<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slider;
use App\Models\Brand;

class SliderSeeder extends Seeder
{
    public function run()
    {
        // Get a brand if exists
        $brand = Brand::first();

        Slider::create([
            'slider_image' => '/upload/slider/sample1.jpg',
            'brand_id' => $brand ? $brand->id : null,
        ]);

        Slider::create([
            'slider_image' => '/upload/slider/sample2.jpg',
            'brand_id' => $brand ? $brand->id : null,
        ]);
    }
}
