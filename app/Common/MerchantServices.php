<?php

namespace App\Common;

use App\Models\MerchantApis;
use App\Models\Merchants;
use Illuminate\Database\Eloquent\Model;

class MerchantServices
{

    public static function createMerchantApiKeys(Model $merchant)
    {
        try {
            $key = new MerchantApis();
            $key->merchant_id = $merchant->id;
            $key->api_key = Helpers::generateUUIDV4();
            $key->api_secret = Helpers::generateRandomHashM1();
            $key->api_type = MerchantApis::KEY_TYPE_DEFAULT;
            $key->created_by = auth()->id();
            $key->save();
        } catch (\Exception $e) {
            Logger::logError("Could not create merchant api keys: " . $e->getMessage(), Logger::LOG_TYPE_MERCHANTS);
            throw new \Exception("Could not create merchant api keys" . $e->getMessage());
        }
    }

    public static function authenticateMerchant(MerchantApis $merchantApi, string $apiSecret)
    {
        return $merchantApi->api_secret === $apiSecret;
    }


}
