<?php

namespace App\Http\Controllers;

use App\Services\CybersourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThreeDSController extends Controller
{


    /**
     * Show the payment form
     */
    public function index()
    {
        $response = <<<EOT
            {
              "requires_action": true,
              "acsUrl": "https://0merchantacsstag.cardinalcommerce.com/MerchantACSWeb/creq.jsp",
              "pareq": "eyJtZXNzYWdlVHlwZSI6IkNSZXEiLCJtZXNzYWdlVmVyc2lvbiI6IjIuMS4wIiwidGhyZWVEU1NlcnZlclRyYW5zSUQiOiJhMDA4MmM0Mi1jZGI0LTQwNWEtYjY3Yi0zYWRiNzQ4MGE2YjAiLCJhY3NUcmFuc0lEIjoiYTg2YWJlNjMtNzA1Ni00ZGZkLWI4OTYtMmMyZDk4MzVlNzVkIiwiY2hhbGxlbmdlV2luZG93U2l6ZSI6IjAyIn0",
              "authenticationTransactionId": "7309047890976731904953"
            }
EOT;
        $data = json_decode($response, true);

        return view('3ds', $data);
    }

}
