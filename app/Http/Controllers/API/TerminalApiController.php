<?php

namespace App\Http\Controllers\API;

use App\Common\ErrorCodes;
use App\Common\GeneralStatus;
use App\Common\Helpers;
use App\Http\Controllers\Controller;
use App\Models\MerchantApis;
use App\Models\Terminals;
use App\Models\Merchants;
use App\Models\TerminalHeartbeat;
use Illuminate\Http\Request;

class TerminalApiController extends Controller
{
    public function initializeParameters(Request $request): array
    {
        $requestReference = Helpers::generateUUIDV4();

        $validator = validator()->make($request->all(), [
            'sn' => 'required|string',
        ]);
        $serial = $request->sn;

        if ($validator->fails()) {
            return Helpers::generateErrorResponse(101, $validator->errors()->first(), $requestReference);
        }

        $terminal = Terminals::where('serial_number', $serial)->first();

        if (!$terminal) {
            return Helpers::generateErrorResponse(103, "Terminal not found", $requestReference);
        }

        $merchant = Merchants::find($terminal->merchant_id);
        $apis = MerchantApis::where('merchant_id', $merchant->id)->first();

        if (!$merchant) {
            return Helpers::generateErrorResponse(103, "Merchant not found", $requestReference);
        }

        if ($terminal->status == Terminals::STATUS_UPLOADED) {
            $terminal->date_activated = now();
            $terminal->status = Terminals::STATUS_ACTIVATED;
            $terminal->save();
        }

        $data = [
            'api_key' => $apis->api_key ?? null,
            'api_secret' => $apis->api_secret ?? null,
            'merchant_name' => $merchant->name,
            'merchant_code' => $merchant->code,
            'terminal_id' => $terminal->terminal_id,
        ];

        return Helpers::generateSuccessResponse(100, "Terminal parameters initialized successfully", $data, $requestReference);
    }

    public function heartbeat(Request $request)
    {
        $requestReference = Helpers::generateUUIDV4();

        $validator = validator()->make($request->all(), [
            'sn' => 'required|string',
            'location' => 'required|string',
            'battery_health' => 'required|integer',
            'transactions_count' => 'required|integer',
            'misc' => 'sometimes|json',
        ]);

        if ($validator->fails()) {
            return Helpers::generateErrorResponse(101, $validator->errors()->first(), $requestReference);
        }

        $serial = $request->sn;
        $terminal = Terminals::where('serial_number', $serial)->first();

        if (!$terminal) {
            return Helpers::generateErrorResponse(103, "Terminal not found", $requestReference);
        }
        $misc = $request->misc;
        $heartbeat = TerminalHeartbeat::create([
            'terminal_id' => $terminal->id,
            'location' => $request->location,
            'battery_health' => $request->battery_health,
            'transactions_count' => $request->transactions_count,
            'misc' => $misc,
        ]);

        $data = [
            'heartbeat_id' => $heartbeat->id,
        ];

        return Helpers::generateSuccessResponse(100, "Heartbeat data received successfully", $data, $requestReference);
    }
}
