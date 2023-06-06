<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PurchaseCodes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function client(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|unique:users',
        ]);

        if ($validation->fails()) {
            return response([
                'errors' => $validation->errors(),
            ], 422);;
        }

        try {
            $sk = Str::random(16);
            $user = User::create([
                'firstname' =>  $request->firstname,
                'lastname' =>  $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($sk),
                'secret_key' => $sk,
            ]);
            $user_id = $user->id;
            $user->password = Hash::make('edsk'.$user_id.'-'.$sk);
            $user->secret_key = 'eduthon_sk'.$user_id.'-'.$sk;
            $user->update();

            $pc = Str::random(5).'-'.Str::random(5).'-'.Str::random(5).'-'.Str::random(5).'-'.Str::random(5);
            $PurchaseCodes = PurchaseCodes::create([
                'user_id' => $user_id,
                'purchase_code' => $pc
            ]);

            return response([
                'purchase_code' => $PurchaseCodes->purchase_code,
                'secret_key' => $user->secret_key,
                'message' => 'Account created successfully'
            ], 200);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
