<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OptionController extends Controller
{
    public function options()
    {
        $validator = Validator::make(\request()->all(), [
            'unvan'=>['nullable','max:200', new UnvanRule()],
            'tel'=>['nullable','max:50', new TelRule()],
            'email'=>'nullable|email',
            'facebook'=>'nullable|url|max:150',
            'instagram'=>'nullable|url|max:150',
            'youtube'=>'nullable|url|max:150'
        ],[
            'unvan'=>'Ünvan',
            'tel'=>'Telefon',
            'email'=>'E-mail',
            'facebook'=>'Facebook',
            'instagram'=>'İnstagram',
            'youtube'=>'Youtube'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors(),
                'details'=>null
            ], 422);
        }

        foreach (\request()->keys() as $key)
        {
            if ($key != '_token')
            {
                Option::updateOrCreate(
                    ['key'   => $key],
                    [
                        'value' => \request()->post($key)
                    ]
                );
            }
        }
    }
}
