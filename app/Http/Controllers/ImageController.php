<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Validator;

class ImageController extends Controller
{
    public function imageStore(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg,webp|max:12048',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()->get('*')
            ]);
        }

        $file = $request->file('image') ;
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid().'.'.$extension;

        $storage_file = Storage::disk('productos')->put($filename, file_get_contents($file));
//dd($storage_file);
        $image_path = Storage::disk('productos')->url($filename) ;


        $data = Image::create([
            'name' => $filename,
            'image' => $image_path,
            'folder' => $request->folder,
        ]);

        return response()->json([
            'success' => true,
            'message'=> 'Image created',
            'data' => $data
        ],200);
    }
}
