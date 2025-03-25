<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Common\Helpers;
use App\Common\GeneralStatus;
use App\Common\MerchantServices;
use App\Models\MerchantApis;

class AuthenticateMerchant
{
    public function handle(Request $request, Closure $next)
    {
        $requestReference = Helpers::generateUUIDV4();

        $validator = validator()->make($request->all(), [
            'merchantApiKey' => 'required|string',
            'merchantApiID' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(Helpers::generateErrorResponse(101, $validator->errors()->first(), $requestReference));
        }

        $apiSecret = $request->merchantApiKey;
        $apiKeyID = $request->merchantApiID;

        $merchantApi = MerchantApis::where('api_key', $apiKeyID)->where('status', GeneralStatus::STATUS_ACTIVE)->first();
        if (!$merchantApi) {
            return response()->json(Helpers::generateErrorResponse(102, "Merchant not found.", $requestReference));
        }

        if (!MerchantServices::authenticateMerchant($merchantApi, $apiSecret)) {
            return response()->json(Helpers::generateErrorResponse(102, "Authentication failed.", $requestReference));
        }

        if($merchantApi->merchant ==  null){
            return response()->json(Helpers::generateErrorResponse(102, "Merchant not found.", $requestReference));
        }
        $request->attributes->add(['merchant' => $merchantApi->merchant]);

        return $next($request);
    }
}
