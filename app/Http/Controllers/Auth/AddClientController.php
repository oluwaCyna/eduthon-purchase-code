<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PurchaseCodes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AddClientController extends Controller
{
    /**
     * Register new client.
     *
     * This endpoint allows you to add a new client, then generate secret code for the client and a purchase code for the app.
     * 
     * @header Content-Type application/json
     * 
     * @bodyParam firstname string required Client's firstname. Example: john
     * @bodyParam lastname string required Client's lastname. Example: Doe
     * @bodyParam email email required Client's email. Example: johndoe@example.com
     * 
     * @response {
     *  "purchase_code": "Oygfl-2yigH-VRWX8-dgEshd-RllUa",
     *  "secret_key": "eduthon_sk35-vGY1XVZcU88tgues",
     *  "message": "Account created successfully"
     * }
     * @response status=422 scenario="Required parameter not found" {"message": "firstname is required"}
     * @response status=400 scenario="Server error" {"message": "Server error"}
     */
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
            $user->password = Hash::make('eduthon_sk' . $user_id . '-' . $sk);
            $user->secret_key = 'eduthon_sk' . $user_id . '-' . $sk;
            $user->update();

            $pc = Str::random(5) . '-' . Str::random(5) . '-' . Str::random(5) . '-' . Str::random(5) . '-' . Str::random(5);
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
