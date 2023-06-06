<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthenticateController extends Controller
{
    /**
     * Authenticate.
     *
     * This endpoint allows you to authenticate a user.
     * 
     * @header Content-Type application/json
     * 
     * @bodyParam secret_key string required The secret key of the client. Example: eduthon_sk35-vGY1XVZcU88tgues
     * 
     * @response {
     *  "message": "success",
     *  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiMWU4YWYwMDM0YTIwODlmOTU3ZTAwNmUwMDAxM2M3NjUzNTNkZTEwNzQ4NjE4ZmI0YzhhODU4ZjYxZjQxNWE4NTEyZGI2YzUzNTQxMjI4OGQiLCJpYXQiOjE2ODYwNTc1MDguOTYxMDI1LCJuYmYiOjE2ODYwNTc1MDguOTYxMDI4LCJleHAiOjE3MTc2Nzk5MDguOTQ2MDg1LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.oY8nKLaDuxm7khkD_HOkkUfsyW2Q-vtPSipZjF-TsbVs82k8ptzXPhYBpQYS5RiET-xWhfOUjwwzJm2zk3hazLgrkYehm2b0uD2TZd__p5nLcQ_mYaPYg1JPQxJISD-wSUeY7cn0L95Cng-1UIq9dYM3D3NzzDd5o7_qAL5NBiw86YnFm8YbsKfYrPkr929SjC0mXA2TBD35QOuLgrLpsyZwe3ZWTOI3LSX4cORNUAUg3Af8IpDfDhmU7LGcAegZD8ORDVzCPIoLQsA2wEN---RxpTjPL1pRMBGOByLOI04KnrrmyIOJCo11EaNZ9wB9w_Vmynins86k7c_o4Bm70x6TG8ZXOi2Ao4tD7CBCUo5jB_1RBGK9GAWL6_EuINvS0OmuQq1AkTcSEYv1-Y4FnAQ9z_F7LSkjpg_Q1fSIwfMK5eJtv86aItwUbYgOAPizRSLsjk882JEu-JmV3RxTwFAufnPPWlYbarlRDLKjzG30JDjO2B3qiP5I3lz4RK2KSm0o3WsYIFvq-Ur80ejf-L8SeJJpplR4oLdCYpaPrGJKBN5bvVL3062pnWDN2WVxuhLAhxl2ZNisqj3i0BAhkaEnklVHgeRkLbtEAZ8SYARqYmmeyWJZ9s1W4gxqkro65GGm-aL7uLLrjJj7ltdbzsMFM7Kg0U4izkW3UCSJyUw",
     *  "user_id": 1
     *  }
     * @response status=422 scenario="Required parameter not found" {"message": "secret_key is required"}
     * @response status=401 scenario="Wrong secret key was used" {"message": "Invalid Secret Key"}
     * @response status=400 scenario="Server error" {"message": "Server error"}
     */
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
