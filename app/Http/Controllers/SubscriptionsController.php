<?php

namespace App\Http\Controllers;

use App\Models\PackageModules;
use App\Models\PurchaseCodes;
use App\Models\Subscriptions;
use App\Models\TemporaryRedirectUrl;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SubscriptionsController extends Controller
{
    /**
     * Subscription payment - FLUTTERWAVE.
     *
     * This endpoint allows you to make subscription payment for a module. You need to authenticate with the authentication endpoint so you cabn have access to the token and client user data.
     * 
     * @authenticated
     * 
     * @header Content-Type application/json
     * 
     * @bodyParam firstname string required client's firstname. Example: john
     * @bodyParam lastname string required client's lastname. Example: Doe
     * @bodyParam email email required client's email. Example: johndoe@example.com
     * @bodyParam currency string required curency of the payment. Example:NGN, USD
     * @bodyParam url string required The url to be redirected to after fully processing the payment and subscription Example: https://www.example.com
     * @bodyParam purchase_code string required The purcahse code for the application. Example: Oygfl-2yigH-VRWX8-dgEshd-RllUa
     * @bodyParam package_id int required The id of the package module. Example: 3 
     * 
     * @response {"message": "success", "redirect":"https://api.flutterwave.com/v3/hosted/pay/f524c11fgffda5556341"}
     * @response status=200 scenario="Final GET response after fully processed payment request" https://example.com?status=sucesss$code=200  
     * 
     * @response status=401 scenario="Client not authenticated" 
     * @response status=422 scenario="Required parameter not found" {"message": "firstname is required"}
     * @response status=401 scenario="Payment failed"
     * @response status=401 scenario="Payment cancelled"
     * @response status=400 scenario="Server error" {"message": "Server error"}
     */
    public function handleFlutterwaveRequest(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|unique:users',
            'currency' => 'required|string',
            'package_id' => 'required|integer',
            'purchase_code' => 'required|string',
            'url' => 'required|url'
        ]);

        if ($validation->fails()) {
            return response([
                'errors' => $validation->errors(),
            ], 422);;
        }

        // Config::set('flutter_final_url.url', $request->url);
        // $_SESSION['url'] = 'ujdfnjhgjgk';
        // setcookie('url', $request->url, time() + 60 * 60 * 24 * 365, '/');
        $package = PackageModules::find($request->package_id);

        try {
            $ref = "ref" . date("Ymd") . rand(100000000, 999999999);
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('FLUTTERWAVE_SECRET_KEY'),
                'Content-Type' => 'application/json'
            ])->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $ref,
                'amount' => $package->amount,
                'currency' => $request->currency,
                'redirect_url' => env('APP_URL') . ':8000/subscribe/flutterwave',
                'meta' => [
                    'data' => json_encode([
                        'id' => $package->id,
                        'name' => $package->name,
                        'purchase_code' => $request->purchase_code,
                        'url' => $request->url
                    ])
                ],
                'customer' => [
                    'email' => $request->email,
                    'name' => $request->firstname . ' ' . $request->lastname,
                ],
                'customizations' => [
                    'title' => 'Dewalthon IT Solutions - Eduthon',
                    'logo' => asset('images/delwathon_logo_light.png')
                ]
            ])->json();

            if ($response['status'] === 'success') {
                TemporaryRedirectUrl::updateOrCreate([
                    'ref' => $ref,
                    'url' => $request->url
                ]);
                return response([
                    'message' => 'success',
                    'redirect' => $response['data']['link']
                ], 200);
            }

            if ($response['status'] === 'failed') {
                return response([
                    'message' => $response['message'],
                ], 401);
            }

            if ($response['status'] === 'cancelled') {
                return response([
                    'message' => $response['message'],
                ], 401);
            }

            if ($response['status'] === 'error') {
                return response([
                    'message' => $response['message'],
                ], 401);
            }
        } catch (\Exception $e) {
            return response([
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function handleFlutterwaveResponse(Request $request)
    {
        if ($request->status === 'completed') {
            $transaction_id = $request->transaction_id;
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('FLUTTERWAVE_SECRET_KEY'),
                'Content-Type' => 'application/json'
            ])->get('https://api.flutterwave.com/v3/transactions/' . $transaction_id . '/verify');

            // Save Transaction record
            $response = json_decode($response->body())->data;
            $datas = json_decode($response->meta->data);

            // Set the status to subscribed
            $purchase_code = PurchaseCodes::where('purchase_code', $datas->purchase_code)->first();
            Subscriptions::updateOrCreate([
                'package_module_id' => $datas->id,
                'purchase_code_id' => $purchase_code->id
            ], [
                'package_module_id' => $datas->id,
                'purchase_code_id' => $purchase_code->id,
                'status' => 'subscribed',
                'interval' => '1 year',
                'subscription_date' => date('Y-m-d', strtotime($response->created_at)),
                'expiry_date' => Carbon::parse($response->created_at)->addYears(1)->subDay(1)->format('Y-m-d')
            ]);

            Transactions::create([
                'package_module_id' => $datas->id,
                'purchase_code_id' => $purchase_code->id,
                'user_id' => $purchase_code->user_id,
                'amount' => $response->amount,
                'payment_type' => $response->payment_type,
                'expiry_date' => Carbon::parse($response->created_at)->addYears(1)->subDay(1)->format('Y-m-d'),
                'ref' => $response->tx_ref
            ]);
            return redirect($datas->url . '?status=sucesss$code=200');
        }

        if ($request->status === 'failed') {
            $url = TemporaryRedirectUrl::where('ref', $request->tx_ref)->first()->url;
            return redirect($url . '?status=failed$code=401');
        }

        if ($request->status === 'cancelled') {
            $url = TemporaryRedirectUrl::where('ref', $request->tx_ref)->first()->url;
            return redirect($url . '?status=cancelled$code=401');
        }
    }

    /**
     * Activate Subscription.
     *
     * This endpoint allows you to activate a module subscribed for. You need to authenticate with the authentication endpoint so you cabn have access to the token.
     * 
     * @authenticated
     * 
     * @header Content-Type application/json
     * 
     * @bodyParam purchase_code string required The purcahse code for the application. Example: Oygfl-2yigH-VRWX8-dgEshd-RllUa
     * @bodyParam package_module_id int required The id of the module to be activated. Example: 3
     * 
     * @response {"message": "successfuly activated"}
     * 
     * @response status=422 scenario="Required parameter not found" {"message": "purchase_code is required"}
     * @response status=401 scenario="Module not subscribed for"
     * @response status=400 scenario="Server error" {"message": "Server error"}
     */

    public function activate(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'purchase_code' => 'required|string',
            'package_module_id' => 'required|int',
        ]);

        if ($validation->fails()) {
            return response([
                'errors' => $validation->errors(),
            ], 422);;
        }

        try {
            $purchase_code = PurchaseCodes::where('purchase_code', $request->purchase_code)->first();
            $sub = Subscriptions::where([
                'package_module_id' => $request->package_module_id,
                'purchase_code_id' => $purchase_code->id
            ])->first();

            if ($sub->status == 'subscribed') {
                $sub->status = 'activated';
                $sub->update();

                return response([
                    'message' => 'successfuly activated',
                ], 200);
            } else {
                return response([
                    'message' => 'Not subscribed for',
                ], 401);;
            }
        } catch (\Exception $e) {
            return response([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
