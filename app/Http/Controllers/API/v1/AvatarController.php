<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Traits\FileUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AvatarController extends Controller
{
    use FileUploader;
    public function avatar()
    {
        $validator = Validator::make(\request()->all(), [
            'image'=>'required|image|max:2048'
        ],[]);

        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors(),
                'src'=>null
            ], 422);
        }
        $user = auth()->user();

        $avatar   = $this->fileUpdate($user->avatar, \request()->hasFile('image'), \request()->image, 'files/avatars/');
        $user->update([
           'avatar'=>$avatar
        ]);

        return response()->json([
            'status' => true,
            'errors'=>null,
            'src'=>asset('files/avatars/'.$avatar)
        ], Response::HTTP_CREATED);
    }
}
