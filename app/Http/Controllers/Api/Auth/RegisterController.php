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
            'secret_key' => 'required|unique:users',
        ]);

        if ($validation->fails()) {
            return response([
                'errors' => $validation->errors(),
            ], 422);;
        }

        try {
            $user = User::create([
                'firstname' =>  $request->firstname,
                'lastname' =>  $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->secret_key),
                'secret_key' => $request->secret_key,
            ]);

            $PurchaseCodes = PurchaseCodes::create([
                'user_id' => $user->id,
                'purchase_code' => base64_encode(Str::random(12))
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
