<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletProvider;

class HomeController extends Controller
{
    /**
     * Show the welcome page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

    /**
     * Show the terms and conditions page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function terms()
    {
        return view('terms');
    }

    /**
     * Show the privacy policy page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function privacy()
    {
        return view('privacy');
    }

    /**
     * Show the about us page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Show the contact us page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Process the contact form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        // In a real application, this would send an email or create a contact record
        // For now, we'll just redirect back with a success message

        return back()->with('success', 'Your message has been sent. We will get back to you shortly.');
    }
}
