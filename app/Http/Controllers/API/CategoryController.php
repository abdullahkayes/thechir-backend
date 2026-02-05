<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function categories(){
        $categories = Category::all()->map(function ($category) {
            $category->category_image = $this->makeRelativePath($category->category_image);
            return $category;
        });
        return response()->json([
            'categories'=>$categories,
        ]);
    }
    function all_categories(){
        $categories = Category::all()->map(function ($category) {
            $category->category_image = $this->makeRelativePath($category->category_image);
            return $category;
        });
        return response()->json([
            'categories'=>$categories,
        ])->header('Cache-Control', 'public, max-age=3600');
    }

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
    function all_subcategories(){
        $subcategories =Subcategory::all();
        return response()->json([
            'subcategories'=>$subcategories,
        ]);
    }
}
