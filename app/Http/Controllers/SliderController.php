<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    function slider(){
        $sliders = Slider::all();
        return view('backend.slider.index', compact('sliders'));
    }

    function slider_add(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path('upload/slider'), $imageName);

        Slider::create([
            'image' => "http://127.0.0.1:8000/upload/slider/$imageName",
        ]);

        return back()->with('success', 'Slider added successfully');
    }

    function slider_delete($id){
        $slider = Slider::find($id);
        if ($slider) {
            // Delete the image file
            $imagePath = public_path('upload/slider/' . basename($slider->image));
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $slider->delete();
            return back()->with('success', 'Slider deleted successfully');
        }
        return back()->with('error', 'Slider not found');
    }
}