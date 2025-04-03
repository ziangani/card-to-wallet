<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Authentication is handled in routes
    }

    /**
     * Show the support page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('support.index');
    }

    /**
     * Submit a support request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
            'transaction_id' => 'nullable|string|exists:transactions,uuid',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        
        // In a real application, this would create a support ticket
        // For now, we'll just redirect back with a success message
        
        return back()->with('success', 'Your support request has been submitted. We will get back to you shortly.');
    }

    /**
     * Show the FAQ page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function faq()
    {
        return view('support.faq');
    }
}
