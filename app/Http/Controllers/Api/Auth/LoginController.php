<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'secret_key' => 'required',
        ]);

        if ($validation->fails()) {
            return response([
                'message' => $validation->errors(),
            ], 422);;
        }

        try {
            if (Auth::attempt(['secret_key' => $request->secret_key, 'password' => $request->secret_key])) {
                $user = Auth::user();
                $token = $user->createToken('user')->accessToken;
                return response([
                    'message' => 'success',
                    'token' => $token,
                    'user_id' => $user->id
                ], 200);
            } else {
                return response([
                    'message' => 'Invalid Secret Key',
                ], 401);
            }
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
