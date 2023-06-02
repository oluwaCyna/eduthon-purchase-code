<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseCodes;
use Exception;
use Illuminate\Http\Request;

class VerifyCode extends Controller
{
    /**
     * Verify purchase code.
     *
     * @return \Illuminate\Http\Response
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
                return response([
                    'message' => 'success',
                ], 200);
            } elseif ($purchaseCode->url === $request->url) {
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