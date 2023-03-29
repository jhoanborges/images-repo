<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Validator;

class ImageController extends Controller
{

        /**
     * POST - Upload Image
     *
     *  In order to use this api you must get a valid username and password provided by our team.
     * @param Request $request
     * @return User
     * @bodyParam image file required Your file.
     * @bodyParam folder string required Your folders name. Example: productos
     */
    #[Response('{
        "success": true,
        "message": "Image created",
        "data": {
            "name": "6423edf5dddc4.png",
            "image": "http://images-repo.test/imagenes/productos/6423edf5dddc4.png",
            "folder": "productos",
            "updated_at": "2023-03-29T07:51:17.000000Z",
            "created_at": "2023-03-29T07:51:17.000000Z",
            "id": 20
        }
    }', 200)]

    #[Response('{
        "success": false,
        "errors": {
            "image": [
                "The image field must be a file of type: webp."
            ]
        }
    }', 404)]
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

        $folder =  $request->folder;

        $storage_file = Storage::disk($folder)->put($filename, file_get_contents($file));

        $image_path = Storage::disk($folder)->url($filename) ;


        $data = Image::create([
            'name' => $filename,
            'image' => $image_path,
            'folder' => $folder,
        ]);

        return response()->json([
            'success' => true,
            'message'=> 'Image created',
            'data' => $data
        ],200);
    }
}
