<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        $validator = Validator::make(\request()->all(), [
            'name' => 'required|max:40',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:30',
            'confirm_password' => 'required|same:password',
            'terms' => 'boolean'
        ],[
            "terms.in"=>"Oops,you must agree to our Terms and Conditions before being able to register"
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors(),
                'details'=>null
            ], 422);
        }

        $user = User::create([
            'name'=>\request()->name,
            'email'=>\request()->email,
            'password'=>bcrypt(\request()->password),
        ]);
        $accessToken =  $user->createToken('Registration')->accessToken;

        return response()->json([
            'status' => true,
            'errors'=>null,
            'details'=>[
                'name'=>\request()->name,
                'email'=>\request()->email,
                'token'=>$accessToken
            ]
        ],Response::HTTP_CREATED);
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        $validator = Validator::make(\request()->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors(),
                'details'=>null
            ], 422);
        }

        if(Auth::attempt(['email' => \request()->email, 'password' => \request()->password]))
        {
            $user           = \auth()->user();
            $accessToken    =  $user->createToken('Login')-> accessToken;

            return response()->json([
                'status' => true,
                'errors'=>null,
                'details'=>[
                    'name'=>$user->name,
                    'email'=>$user->email,
                    'token'=>$accessToken,
                ]
            ],Response::HTTP_OK);
        }
        else
        {
            return response()->json([
                'status' => false,
                'errors'=>['Unauthorised'],
                'details'=>null
            ],Response::HTTP_FORBIDDEN);
        }
    }


    public function logout ()
    {
        if (\request()->user())
        {
            \request()->user()->tokens()->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'You have been successfully logged out!'
        ],Response::HTTP_OK);
    }
}
