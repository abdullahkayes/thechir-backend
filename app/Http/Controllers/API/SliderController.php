<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Brand;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    /**
     * Make image paths relative by removing old hostnames
     */
    private function makeRelativePath($path) {
        if (!$path) return $path;
        // Remove any hostname if present
        $hostnames = ['http://127.0.0.1:8000', 'http://127.0.0.2:8000'];
        foreach ($hostnames as $hostname) {
            if (str_starts_with($path, $hostname)) {
                return substr($path, strlen($hostname));
            }
        }
        // If it's already relative, return as is
        return $path;
    }

    function slider(){
        $sliders = Slider::all();
        $brands = Brand::all();
        return view('Backend.Banner.slider', compact('sliders', 'brands'));
    }
function slider_add(Request $request){

    $request->validate([
        'brand_id' => 'nullable|exists:brands,id',
    ]);

    if (!is_dir(public_path('/upload/slider'))) {
        mkdir(public_path('/upload/slider'), 0755, true);
    }

    if ($request->hasFile('slider_image')) {
        foreach ($request->slider_image as $photo) {
            $extension = $photo->extension();
            $file_name = uniqid() . '.' . $extension;

            $manager = new ImageManager(new Driver());
            $image = $manager->read($photo);
            $image->save(public_path('/upload/slider/' . $file_name));

            Slider::insert([
                'slider_image' => "/upload/slider/$file_name",
                'brand_id' => $request->brand_id,
            ]);
        }
    }

    return back()->with('slider', 'Sliders Added Successfully');

}

function slider_delete($id){
    $slider = Slider::find($id);
    if($slider){
        // Delete the image file
        $image_path = ltrim(parse_url($slider->slider_image, PHP_URL_PATH), '/');
        if(file_exists(public_path($image_path))){
            unlink(public_path($image_path));
        }
        $slider->delete();
        return back()->with('slider_delete', 'Slider Deleted Successfully');
    }
    return back()->with('error', 'Slider not found');
}

function sliders(){
    $sliders = Slider::all()->map(function ($slider) {
        $slider->slider_image = $this->makeRelativePath($slider->slider_image);
        return $slider;
    });
    return response()->json($sliders)->header('Cache-Control', 'public, max-age=3600');
}
}
