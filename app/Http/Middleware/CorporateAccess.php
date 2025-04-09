<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CorporateAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user is a corporate user
        if ($user->user_type !== 'corporate' || !$user->company_id) {
            return redirect()->route('dashboard')->with('error', 'You do not have access to the corporate section.');
        }

        // User is a corporate user, proceed
        return $next($request);
    }
}
