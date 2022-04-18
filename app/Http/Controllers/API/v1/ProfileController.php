<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function profile()
    {
        $validator = Validator::make(\request()->all(), [
            'name' => 'required|max:40',
            'old_password' => 'required',
            'new_password' => 'required|min:8|max:30'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors(),
                'details'=>null
            ], 422);
        }

        $user = auth()->user();
        if (!Hash::check(request()->old_password, $user->password))
        {
            return response()->json([
                'status' => false,
                'errors'=>[
                    "old_password"=>"The old password field is incorrect."
                ],
                'details'=>null
            ], 422);
        }

        $user->update([
            'name'=>request()->name,
            'password'=>bcrypt(request()->new_password)
        ]);

        return response()->json([
            'status' => true,
            'errors'=>null,
            'details'=>[
                'name'=>$user->name,
                'email'=>$user->email
            ]
        ],Response::HTTP_CREATED);
    }
}
