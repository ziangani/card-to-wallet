<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class WebSiteController extends Controller
{
    public function index()
    {
        // Simple data for the homepage
        $data = [
            'title' => 'Welcome to ' . config('app.name'),
            'subtitle' => 'Your trusted payment processing solution',
            'description' => 'We provide secure and reliable payment processing services for businesses of all sizes.',
            'buttonText' => 'Make a Payment',
            'buttonUrl' => '/tpm/DEFAULT/'
        ];
        
        return view('welcome', $data);
    }
}
