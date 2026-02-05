<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BannerDown;
use Carbon\Carbon;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BannerDownController extends Controller
{
    function banner_down(){
        $banner_down = BannerDown::all();
        return view('Backend.Banner.bannerDown', compact('banner_down'));
    }

    function banner_down_store(Request $request){
        $request->validate([
            'banner_id' => 'required',
        ]);

        if($request->hasFile('image')){
            $manager = new ImageManager(new Driver());
            foreach($request->file('image') as $image){
                $extension = $image->getClientOriginalExtension();
                $file_name = uniqid() . '.' . $extension;
                $image->move(public_path('/upload/banner_down/'), $file_name);

                BannerDown::insert([
                    'sub_title' => $request->sub_title,
                    'title' => $request->title,
                    'image' => $file_name,
                    'banner_id' => $request->banner_id,
                    'created_at' => Carbon::now(),
                ]);
            }
        }
        else{
            BannerDown::insert([
                'sub_title' => $request->sub_title,
                'title' => $request->title,
                'banner_id' => $request->banner_id,
                'created_at' => Carbon::now(),
            ]);
        }
        return back();
    }

    function banner_down_delete($id){
        BannerDown::find($id)->delete();
        return back();
    }

    function banner_down_trash(){
        $banner_down = BannerDown::onlyTrashed()->get();
        return view('Backend.Banner.bannerDownTrash', compact('banner_down'));
    }

    function banner_down_restore($id){
        BannerDown::onlyTrashed()->find($id)->restore();
        return back();
    }

    function banner_down_force_delete($id){
        $image = BannerDown::onlyTrashed()->find($id);
        unlink(public_path('/upload/banner_down/'. $image->image));
        BannerDown::onlyTrashed()->find($id)->forceDelete();
        return back();
    }

    // API
    function get_banner_down() {
        $banner_downs = BannerDown::all();
        return response()->json($banner_downs);
    }
}
