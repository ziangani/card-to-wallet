<?php

namespace App\Common;

use App\Models\Applications;
use Illuminate\Support\Facades\URL;
use App\Models\BotUserFud;
use App\Models\Emails;
use App\Models\Logs\ApiLogs;
use App\Models\MerchantApplications;
use App\Models\Merchants;
use App\Models\OnboardingApplications;
use App\Models\PaymentRequests;
use App\Models\PerformanceLogs;
use App\Models\SmsNotifications;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Sentry\Laravel\Integration;

class Helpers
{
    public static function buildApiResponse($statusCode, $statusDescription)
    {
        return [
            'statusCode' => $statusCode,
            'statusDescription' => $statusDescription
        ];
    }

    public static function logApiRequest($request, $response, $request_time, $response_time, $entity_state, $new_state, $reference, $source_reference, $request_status, $request_type): void
    {
        try {
            $log = new \App\Models\ApiLogs();
            $log->request = json_encode($request);
            $log->response = json_encode($response);
            $log->request_time = $request_time;
            $log->response_time = $response_time;
            $log->source_ip = request()->ip();
            $log->entity_state = json_encode($entity_state);
            $log->new_state = json_encode($new_state);
            $log->reference = $reference;
            $log->source_reference = $source_reference;
            $log->request_status = $request_status;
            $log->request_type = $request_type;
            $log->save();
        } catch (\Exception $e) {
//            throw new \Exception('something went wrong: ' . $e->getMessage());
        }
    }

    public static function generateUUID(): string
    {
        // Generate a random 16-byte binary string
        $data = random_bytes(16);

        // Set the version (4) and variant (10) bits
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40); // Version 4
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80); // Variant 10

        // Convert binary to a hexadecimal string
        $uuid = bin2hex($data);

        // Format the UUID as per the standard (8-4-4-12)
        // $formatted_uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);
        $formatted_uuid = preg_replace('/^(.{8})(.{4})(.{4})(.{4})(.{12})$/', '$1-$2-$3-$4-$5', $uuid);


        return $formatted_uuid;
    }

    public static function timeAgo($timestamp): string
    {
        $current_time = time();
        $time_diff = $current_time - strtotime($timestamp);

        $seconds = $time_diff;
        $minutes = $seconds / 60;
        $hours = $minutes / 60;
        $days = $hours / 24;
        $weeks = $days / 7;
        $months = $days / 30;
        $years = $days / 365;

        if ($seconds < 60) {
            return $seconds . " secs ago";
        } elseif ($minutes < 60) {
            return round($minutes) . " minutes ago";
        } elseif ($hours < 24) {
            return round($hours) . " hours ago";
        } elseif ($days < 7) {
            return round($days) . " days ago";
        } elseif ($weeks < 4) {
            return round($weeks) . " weeks ago";
        } elseif ($months < 12) {
            return round($months) . " months ago";
        } else {
            return round($years) . " years ago";
        }
    }

    public static function generateRandomHashM1(): string
    {
        return md5(openssl_random_pseudo_bytes(32));
    }

    public static function isValidZambianMobileNumber($mobile): bool
    {
        $zambian_mobile_regex = '/^(?:\+?26)?0[97][567]\d{7}$/';
        return preg_match($zambian_mobile_regex, $mobile);
    }

    public static function parseFloat(mixed $int): float
    {
        return floatval(str_replace(',', '', $int));
    }


    public static function generateUploadReference(Merchants $merchant, MerchantApplications $merchant_app_id): string
    {
        $merchant_id = $merchant->id;
        $merchant_app_id = $merchant_app_id->id;
        $date = date('YmdHis');
        $random = rand(1000, 9999);
        return $merchant_id . '-' . $merchant_app_id . '-' . $date . $random;
    }

    public static function slugify(string $string): string
    {
        $string = strtolower($string);
        $string = str_replace(' ', '-', $string);
        return $string;
    }

    public static function generateSKU(): string
    {
        $date = date('YmdHis');
        $random = rand(1000, 9999);
        return $date . $random;
    }

    public static function generatePassword(int $length): string
    {
        $chars = '23456789abcdefghkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    public static function LogPerformance($type, $action, $description, $source_id, $source_type, $user_ip, $user_agent, $reference_1 = null, $reference_2 = null, $reference_3 = null, $details = null): void
    {
        try {
            //set session id
            $session_id = session()->getId();
            $log = new PerformanceLogs();
            $log->type = $type;
            $log->action = $action;
            $log->description = $description;
            $log->source_id = $source_id;
            $log->source_type = $source_type;
            $log->reference_1 = $reference_1;
            $log->reference_2 = $reference_2;
            $log->reference_3 = $reference_3;
            $log->user_ip = $user_ip;
            $log->user_agent = $user_agent;
            $log->session_id = $session_id;
            $log->details = json_encode($details);
            $log->save();
        } catch (\Exception $e) {
        }
    }

    public static function getUserIp(): ?string
    {
        return request()->ip();
    }

    public static function pluralize($text, $count): string
    {
        if (str_ends_with($text, 's'))
            $text = substr($text, 0, -1);
        return $count . (($count == 1) ? (" $text") : (" ${text}s"));
    }

    public static function validateNumberVsNetwork(mixed $item_id, $serviceProvider): bool|int
    {
        switch ($serviceProvider) {
            case 'MTN':
                //should match for 096 or 076
                return str_starts_with($item_id, '096') || str_starts_with($item_id, '076');
            case 'Airtel':
                //should match for 097 or 077
                return str_starts_with($item_id, '097') || str_starts_with($item_id, '077');
            case 'Zamtel':
                //should match for 095 or 075
                return str_starts_with($item_id, '095') || str_starts_with($item_id, '075');
            default:
                return false;
        }
    }

    public static function determineMobileNetwork($mobileNumber): string
    {
        if (str_starts_with($mobileNumber, '096') || str_starts_with($mobileNumber, '076')) {
            return 'MTN';
        } elseif (str_starts_with($mobileNumber, '097') || str_starts_with($mobileNumber, '077')) {
            return 'Airtel';
        } elseif (str_starts_with($mobileNumber, '095') || str_starts_with($mobileNumber, '075')) {
            return 'Zamtel';
        } else {
            return 'Unknown';
        }
    }

    public static function generateApplicationRef(): string
    {
        while (true) {
            $ref = 'OR-' . date('Y-md') . '-' . rand(10, 99);
            if (!OnboardingApplications::where('reference', $ref)->exists()) {
                return $ref;
            }
        }
    }

    public static function getAppName(): string
    {
        return config('app.name');
    }

    public static function imageToBase64($path): string
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public static function getSenderId(): string
    {
        return 'TechPay';
    }

    public static function processFormField(int|string $field, mixed $value): string
    {
        if (str_starts_with($field, 'date'))
            return date('Y-m-d', strtotime($value));

        return $value;
    }

    public static function generateErrorResponse(int $int, string $string, string $request_reference): array
    {
        return [
            'responsecode' => $int,
            'responsemessage' => $string,
            'requestreference' => $request_reference
        ];
    }

    public static function generateSuccessResponse(int $int, string $string, array $data, string $request_reference)
    {
        return [
            'responsecode' => $int,
            'responsemessage' => $string,
            'requestreference' => $request_reference,
            'data' => $data,
        ];
    }

    public static function getPaymentEnvironment()
    {
        return config('app.payment_environment');
    }

    public static function getServiceProvider(mixed $mobile)
    {
        $firstThreeDigits = substr($mobile, 0, 3);

        if ($firstThreeDigits === "097" || $firstThreeDigits === "077") {
            $serviceprovider = "Airtel Money";
        } elseif ($firstThreeDigits === "096" || $firstThreeDigits === "076") {
            $serviceprovider = "MTN Money";
        } elseif ($firstThreeDigits === "095" || $firstThreeDigits === "075") {
            $serviceprovider = "Zampay";
        } else {
            $serviceprovider = "Unknown"; // Handle unknown service provider
        }
        return $serviceprovider;
    }

    public static function reportException(\Exception $e): void
    {
        try {
            $message = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTraceAsString();
            $log = "Message: $message\nFile: $file\nLine: $line\nTrace: $trace";
            Log::error($log);
        } catch (\Exception $e) {

        }
        try {
            Integration::captureUnhandledException($e);
        } catch (\Exception $e) {
        }
    }

    public static function logError(\Exception $e): void
    {
        try {
            $message = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTraceAsString();
            $log = "Message: $message\nFile: $file\nLine: $line\nTrace: $trace";
            Log::error($log);
        } catch (\Exception $e) {

        }
    }

    public static function generatePaymentToken(): string
    {
        do {
            //generate a random 7 character string seperated in 2 parts by a dash
            $token = strtoupper(substr(Helpers::generateRandomHashM1(), 0, 4) . '' . substr(Helpers::generateRandomHashM1(), 0, 4));
        } while (PaymentRequests::where('token', $token)->exists());
        return $token;
    }

    public static function resetUserPassword(User $user)
    {

        $password = Helpers::generatePassword(6, true);

        $first_name = $user->first_name;
        $last_name = $user->surname;

        $user->password = Hash::make($password);
        $user->save();

        $sms_text = "Dear $first_name $last_name,\n"
            . "Welcome to the Techpay\nFind below your login details\n"
            . "Username: {$user->email}\n"
            . "Password: {$password}\n"
            . "URL: " . url('/tpadmin') . "\n"
            . "Thank you.";

        $sms = new SmsNotifications();
        $sms->message = $sms_text;
        $sms->mobile = $user->mobile;
        $sms->status = GeneralStatus::STATUS_PENDING;
        $sms->sender = self::getSenderId();
        $sms->save();

        $data = [
            'name' => $first_name,
            'password' => $password,
            'auth_id' => $user->email,
            'url' => url('/tpadmin')
        ];
        $email = new Emails();
        $email->subject = 'Techpay System Login';
        $email->from = config('mail.from.address');
        $email->email = $user->email;
        $email->message = view('emails.login', $data)->render();
        $email->view = 'emails.login';
        $email->data = $data;
        $email->save();

    }

    private function formatCurrency($amount, $currency = 'K'): string
    {
        return $currency . number_format($amount, 0);
    }

    public static function createApiKey(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    public static function diffInSeconds($from, $to): string
    {
        return date_diff(DateTime::createFromFormat('Y-m-d H:i:s', $from), DateTime::createFromFormat('Y-m-d H:i:s', $to))->format('%s');
    }

    public static function createBasicLog($channel, $message, $reference): void
    {
        try {
            Log::channel($channel)->info($reference . ' | ' . $message);
        } catch (\Exception $e) {

        }

    }

    public static function getNetwork(mixed $mobile): string
    {
        if (strlen($mobile) == 10) {
            $prefix = substr($mobile, 0, 3);
            if (in_array($prefix, ['096', '076'])) {
                return 'MTN';
            } elseif (in_array($prefix, ['097', '077'])) {
                return 'AIRTEL';
            } elseif (in_array($prefix, ['095', '075'])) {
                return 'ZAMTEL';
            } else {
                return 'UNKNOWN';
            }
        } else {
            return 'UNKNOWN';
        }
    }

    public static function generateUUIDV4(): string
    {

        $str = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );

        return substr($str, 0, 30);
    }

    public static function generatePaymentUUID(): string
    {
        return substr(self::generateUUIDV4(), 0, 20);
    }

    public static function sendEmailVerificationNotification(User $user): void
    {
        // Generate a signed URL for email verification
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );
        
        $data = [
            'name' => $user->first_name,
            'verification_url' => $verificationUrl,
            'url' => url('/')
        ];

        $email = new Emails();
        $email->subject = 'Verify Your Email Address';
        $email->from = config('mail.from.address');
        $email->email = $user->email;
        $email->message = view('emails.verify-email', $data)->render();
        $email->view = 'emails.verify-email';
        $email->data = $data;
        $email->save();
    }
}
