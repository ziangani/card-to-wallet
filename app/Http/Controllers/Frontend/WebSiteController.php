<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class WebSiteController extends Controller
{
    public function index()
    {
        $homepageData = json_decode(file_get_contents(resource_path('js/homepage.json')), true);
        $header = $homepageData['header'] ?? [];
        $hero = $homepageData['hero'] ?? [];
        $services = $homepageData['services'] ?? [];
        $cta = $homepageData['cta'] ?? [];
        $whatWeOffer = $homepageData['cta']['whatWeOffer'] ?? [];
        $usage = $homepageData['cta']['usage'] ?? [];
        $price = $homepageData['price'] ?? [];
        $data = [
            'header' => $header,
            'hero' => $hero,
            'services' => $services,
            'cta' => $cta,
            'whatWeOffer' => $whatWeOffer,
            'usage' => $usage,
            'price' => $price
        ];
        return view('welcome', $data);
    }
}
