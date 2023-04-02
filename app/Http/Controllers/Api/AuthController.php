<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Knuckles\Scribe\Attributes\Response;


class AuthController extends Controller
{
    /**
     * POST - Register User
     * @param Request $request
     * @return User
     * @hideFromAPIDocumentation
     */
    public function createUser(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                //'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * POST - Login User
     *
     *  In order to use this api you must get a valid username and password provided by our team.
     * @param Request $request
     * @return User
     * @bodyParam email string required Your api username. Example: jhoan.borges@hexaguun.com
     * @bodyParam password string required Your api password. Example: 12345678
     */
    #[Response('{
        "status": true,
        "message": "User Logged In Successfully",
        "token": "10|UoxWVWcsZRblw1kAv3UHLOg1oD1enTfD1z45x3TbMm"
    }', 200)]
    #[Response('{
        "status": false,
        "message": "Email & Password does not match with our record."
    }', 404)]
    public function loginUser(Request $request)
    {
        //return response()->json($request->all());

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }


            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }




    /**
     * GET - Get logged in user's information
     * @param Request $request
     * @return User
     * @authenticated
     */
    #[Response('{
        "id": 1,
        "name": "Jhoan",
        "email": "jhoan.borges@senter.mx",
        "email_verified_at": null,
        "created_at": "2022-12-19T22:51:20.000000Z",
        "updated_at": "2022-12-19T22:51:20.000000Z",
        "odoo_account_id": 13
    }', 200)]
    #[Response('{
        "message": "Unauthenticated."
    }', 401)]
    public function getUserInformation(Request $request)
    {
        return $request->user();
    }
}
