<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PurchaseCodes;
use App\Models\Subscriptions;
use Exception;
use Illuminate\Http\Request;

class VerifyPurchaseCodeController extends Controller
{
    /**
     * Verify client's purchase code.
     *
     * This endpoint allows you to verify the purchase code inputed by the client and also checjk if the has been used on some other application.
     * 
     * @authenticated
     * 
     * @header Content-Type application/json
     * 
     * @bodyParam purchase_code string required The purcahse code for the application. Example: Oygfl-2yigH-VRWX8-dgEshd-RllUa
     * @bodyParam client_id int required The secret key of the client. Example: 3
     * 
     * @response {"message": "success"}
     * 
     * @response status=401 scenario="Client not authenticated" {"message": "Failed, No purchase code for this account"}
     * @response status=401 scenario="Purchase code already used on another application" {"message": "Failed, This code has been used for another application"}
     * @response status=401 scenario="Invalid purchase code" {"message": "Failed, Invalid purchase code"}
     * @response status=400 scenario="Server error" {"message": "Server error"}
     */
    public function verify(Request $request)
    {
        try {
            $purchaseCode = PurchaseCodes::where('user_id', $request->client_id)->first();
            if (empty($purchaseCode)) {
                return response([
                    'message' => 'Failed, No purchase code for this account.',
                ], 401);
            }

            if ($purchaseCode->purchase_code === $request->purchase_code) {
                if ($purchaseCode->url === null) {
                    $purchaseCode->url = $request->url;
                    $purchaseCode->update();

                    //We have 5 core modules that are free
                    for ($i=1; $i < 6; $i++) { 
                    Subscriptions::updateOrCreate([
                        'package_module_id' => $i,
                        'purchase_code_id' => $purchaseCode->id
                    ], [
                        'package_module_id' => $i,
                        'purchase_code_id' => $purchaseCode->id,
                        'status' => 2,
                        'interval' => null,
                        'subscription_date' => date('Y-m-d', strtotime(now())),
                        'expiry_date' => null
                    ]);
                    }

                    return response([
                        'message' => 'success',
                    ], 200);
                } elseif ($purchaseCode->url === $request->url) {
                    //We have 5 core modules that are free
                    for ($i=1; $i < 6; $i++) { 
                        Subscriptions::updateOrCreate([
                            'package_module_id' => $i,
                            'purchase_code_id' => $purchaseCode->id
                        ], [
                            'package_module_id' => $i,
                            'purchase_code_id' => $purchaseCode->id,
                            'status' => 2,
                            'interval' => null,
                            'subscription_date' => date('Y-m-d', strtotime(now())),
                            'expiry_date' => null
                        ]);
                        }

                    return response([
                        'message' => 'success',
                    ], 200);
                } else {
                    return response([
                        'message' => 'Failed, This code has been used for another application',
                    ], 401);
                }
            } else {
                return response([
                    'message' => 'Failed, Invalid purchase code',
                ], 401);
            }
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
