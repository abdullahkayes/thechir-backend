<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SliderVideo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDO;

class VideoSliderController extends Controller
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

    function videoSlider(){
        $videoSliders = SliderVideo::select('id', 'product_id', 'name', 'price', 'discount_price', 'video', 'thumbnail', 'product_image', 'created_at')
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('Backend.Banner.VideoSlider', compact('videoSliders'));
    }

    function videoSlider_add(Request $request) {
        $uploadDir = public_path('/upload/video_slider');
        $basePath = '/upload/video_slider/';

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $paths = ['video' => null, 'thumbnail' => null, 'product_image' => null];
        $filesToProcess = [];
        
        foreach ($paths as $key => &$path) {
            if ($request->hasFile($key)) {
                $filesToProcess[] = $key;
            }
        }
        unset($path);

        foreach ($filesToProcess as $key) {
            $file = $request->file($key);
            $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            $fileName = uniqid() . '.' . $ext;
            $destPath = $uploadDir . '/' . $fileName;
            
            copy($file->getPathname(), $destPath);
            $paths[$key] = $basePath . $fileName;
        }

        $now = Carbon::now()->toDateTimeString();

        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $pdo->setAttribute(PDO::ATTR_PERSISTENT, false);
        $pdo->setAttribute(PDO::MYSQL_ATTR_DIRECT_QUERY, 1);
        
        $stmt = $pdo->prepare("
            INSERT INTO slider_videos (
                video, thumbnail, product_image, name, price, discount_price, 
                wholesale_price, reseller_price, distributer_price, amazon_price, 
                product_id, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");
        
        $stmt->execute([
            $paths['video'], $paths['thumbnail'], $paths['product_image'],
            $request->name, $request->price, $request->discount_price,
            $request->wholesale_price, $request->reseller_price, $request->distributer_price,
            $request->amazon_price, $request->product_id, $now, $now
        ]);

        return redirect()->back()
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->with('video_slider', 'Video Slider Added Successfully');
    }

    public function videoSlider_edit($id){
        $videoSlider = SliderVideo::find($id);
        if(!$videoSlider){
            return back()->with('error', 'Video Slider not found');
        }
        $videoSliders = SliderVideo::select('id', 'product_id', 'name', 'price', 'discount_price', 'video', 'thumbnail', 'product_image', 'created_at')
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('Backend.Banner.VideoSlider', compact('videoSliders', 'videoSlider'));
    }

    public function videoSlider_update(Request $request, $id){
        $videoSlider = SliderVideo::find($id);
        if(!$videoSlider){
            return back()->with('error', 'Video Slider not found');
        }

        $uploadDir = public_path('/upload/video_slider');
        $basePath = '/upload/video_slider/';

        $updates = [];
        $bindings = [];

        // Optimize file handling with direct disk operations
        $filesToProcess = [];
        foreach (['video', 'thumbnail', 'product_image'] as $field) {
            if ($request->hasFile($field)) {
                $filesToProcess[] = $field;
            }
        }

        foreach ($filesToProcess as $field) {
            $file = $request->file($field);
            $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            $fileName = uniqid() . '.' . $ext;
            $destPath = $uploadDir . '/' . $fileName;
            
            // Use fast file copy instead of move
            copy($file->getPathname(), $destPath);
            
            $updates[] = "$field = ?";
            $bindings[] = $basePath . $fileName;

            if($videoSlider->$field){
                $oldPath = public_path($videoSlider->$field);
                if(file_exists($oldPath)){
                    unlink($oldPath);
                }
            }
        }

        // Only update fields that have actually changed
        $updateFields = [
            'name' => 'name',
            'price' => 'price',
            'discount_price' => 'discount_price',
            'wholesale_price' => 'wholesale_price',
            'reseller_price' => 'reseller_price',
            'distributer_price' => 'distributer_price',
            'amazon_price' => 'amazon_price',
            'product_id' => 'product_id'
        ];

        foreach ($updateFields as $inputKey => $dbKey) {
            if ($request->filled($inputKey) && $request->input($inputKey) != $videoSlider->$dbKey) {
                $updates[] = "$dbKey = ?";
                $bindings[] = $request->input($inputKey);
            }
        }

        $updates[] = "updated_at = ?";
        $bindings[] = Carbon::now()->toDateTimeString();
        $bindings[] = $id;

        if (!empty($updates)) {
            // Use raw SQL with optimized connection settings
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $pdo->setAttribute(PDO::ATTR_PERSISTENT, false);
            
            $stmt = $pdo->prepare("UPDATE slider_videos SET " . implode(', ', $updates) . " WHERE id = ?");
            $stmt->execute($bindings);
        }

        return redirect()->back()
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->with('video_slider', 'Video Slider Updated Successfully');
    }

    public function videoSlider_delete($id){
        $videoSlider = SliderVideo::find($id);
        if($videoSlider){
            $videoSlider->delete();
            return back()->with('video_slider_delete', 'Video Slider Moved to Trash Successfully');
        }
        return back()->with('error', 'Video Slider not found');
    }

    public function videoSlider_trash(){
        $trashes = SliderVideo::onlyTrashed()->get();
        return view('Backend.Banner.VideoSliderTrash', compact('trashes'));
    }

    public function videoSlider_trash_restore($id){
        SliderVideo::onlyTrashed()->find($id)->restore();
        return back()->with('restore', 'Video Slider Restored Successfully');
    }

    public function videoSlider_trash_delete($id){
        $videoSlider = SliderVideo::onlyTrashed()->find($id);
        if($videoSlider){
            // Delete files
            $files = [$videoSlider->video, $videoSlider->thumbnail, $videoSlider->product_image];
            foreach($files as $file){
                if($file){
                    $file_path = ltrim(parse_url($file, PHP_URL_PATH), '/');
                    if(file_exists(public_path($file_path))){
                        unlink(public_path($file_path));
                    }
                }
            }
            $videoSlider->forceDelete();
            return back()->with('force_delete', 'Video Slider Permanently Deleted');
        }
        return back()->with('error', 'Video Slider not found');
    }

    function videos(){
        $videos = SliderVideo::all()->map(function ($video) {
            $video->thumbnail = $this->makeRelativePath($video->thumbnail);
            $video->product_image = $this->makeRelativePath($video->product_image);

            // Add inventories to the video object for pricing calculation
            $video->inventories = [
                (object)[
                    'price' => $video->price,
                    'discount_price' => $video->discount_price,
                    'wholesale_price' => $video->wholesale_price,
                    'reseller_price' => $video->reseller_price,
                    'distributer_price' => $video->distributer_price,
                    'amazon_price' => $video->amazon_price,
                ]
            ];

            return $video;
        });
        return response()->json($videos)->header('Cache-Control', 'public, max-age=3600');
    }
}
