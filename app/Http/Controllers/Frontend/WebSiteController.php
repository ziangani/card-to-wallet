<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class WebSiteController extends Controller
{
    public function index()
    {
        // Data for the card-to-wallet homepage
        $data = [
            'title' => 'Fund your mobile wallet instantly',
            'subtitle' => 'Secure, fast transfers from your card to mobile money',
            'description' => 'Experience quick and secure transfers from your bank card to any mobile wallet in Zambia with transparent fees and no hidden charges.',
            'buttonText' => 'Get Started',
            'buttonUrl' => '/register'
        ];
        
        return view('welcome', $data);
    }
}
