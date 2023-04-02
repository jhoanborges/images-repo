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
     * @authenticated
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

        //$filename = $request->file('image');
        //dd($filename);
        //dd($request->file('image' ));
        //return response()->json($request->all());

        $validator = Validator::make($request->all(), [
            //'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg,webp|max:120048',
            'folder' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()->get('*')
            ]);
        }

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;

        $folder =  $request->folder;

        $storage_file = Storage::disk($folder)->put($filename, file_get_contents($file));

        $image_path = Storage::disk($folder)->url($filename);


        $data = Image::create([
            'name' => $filename,
            'image' => $image_path,
            'folder' => $folder,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Image created',
            'data' => $data
        ], 200);
    }


    /**
     * POST - GET File by Image URL
     *
     *  In order to use this api you must get a valid username and password provided by our team.
     * @param Request $request
     * @authenticated
     * @queryParam imageurl file required Your file.
     */
    #[Response('{
        "success": true,
        "message": "Image retrieved",
        "data": {
            "id": 70,
            "user_id": null,
            "name": "6429302447771.png",
            "image": "http://images-repo.test/imagenes/sliders/6429302447771.png",
            "folder": "sliders",
            "created_at": "2023-04-02T07:35:00.000000Z",
            "updated_at": "2023-04-02T07:35:00.000000Z"
        }
    }', 200)]

    #[Response('{
        "success": false,
        "message": "La imagen no existe en la base de datos."
    }', 404)]
    public function getFileByID(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'image_url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Image URL is null',
                'errors' => $validator->messages()->get('*')
            ]);
        }


        $query = Image::where('image', $request->image_url);
        if (!$query->first()) {
            return response()->json([
                'success' => false,
                'message' => 'La imagen no existe en la base de datos.'
            ]);
        }

        $data = $query->first();

        return response()->json([
            'success' => true,
            'message' => 'Image retrieved',
            'data' => $data
        ], 200);
    }
}
