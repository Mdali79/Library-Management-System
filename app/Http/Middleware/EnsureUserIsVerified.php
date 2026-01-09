<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user is verified
            if (!$user->is_verified) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'username' => 'Your account is not verified yet. Please wait for an Administrator to approve your registration. You will be able to login once your account is verified.'
                ]);
            }
            
            // Check if registration is approved
            if ($user->registration_status == 'pending') {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'username' => 'Your account is pending approval. Please wait for an Administrator to approve your registration.'
                ]);
            }
            
            if ($user->registration_status == 'rejected') {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'username' => 'Your registration has been rejected. Please contact the administrator for more information.'
                ]);
            }
        }

        return $next($request);
    }
}

