<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    function tag(){
        $tags=Tag::all();
        return view('backend.tags.tag',compact('tags'));
    }
    function tag_add (Request $request){
      foreach($request->tag_name as $tag){
     Tag::insert([
        'tag_name'=>$tag,
     ]);
      }
      return back();
    }
    function tag_delete($id){
        Tag::find($id)->delete();
        return back();
    }
    
}
