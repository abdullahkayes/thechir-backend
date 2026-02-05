<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
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

    /**
     * Display a listing of the brands.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $brands = Brand::all()->map(function ($brand) {
            $brand->logo = $this->makeRelativePath($brand->logo);
            return $brand;
        });
        return response()->json($brands);
    }
}
