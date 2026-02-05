<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BrandsController extends Controller
{
    /**
     * Show the list of brands.
     */
    public function index()
    {
        $brands = Brand::all();
        return view('backend.brands_list', compact('brands'));
    }

    /**
     * Show form to create a new brand
     */
    public function create()
    {
        return view('backend.brand_create');
    }

    /**
     * Store a new brand with uploaded logo image using Intervention Image
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $photo = $request->logo;
        $extension = $photo->extension();
        $file_name = uniqid() . '.' . $extension;

     $manager = new ImageManager(new Driver());
        $image = $manager->read($photo);
        $image->save(public_path('upload/brands/' . $file_name));

        Brand::insert([
            'name' => $request->name,
            'brand_image' => "upload/brands/$file_name",
        ]);

        return redirect()->back()->with('success', 'Brand created successfully!');
    }

    /**
     * Delete a brand by ID and remove its logo file from public upload directory
     */
    public function delete($id)
    {
        $brand = Brand::findOrFail($id);

        if ($brand->brand_image) {
            $path = ltrim(parse_url($brand->brand_image, PHP_URL_PATH), '/');
            $full_path = public_path($path);
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }

        $brand->delete();

        return redirect()->back()->with('success', 'Brand deleted successfully!');
    }
}

