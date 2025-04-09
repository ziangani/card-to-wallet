<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CorporateRoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
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

        // Check if user has any of the required roles
        $hasRole = false;
        foreach ($roles as $role) {
            if ($user->hasCorporateRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            return redirect()->route('corporate.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
