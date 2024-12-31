<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyRhombusCaptcha
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip captcha check for the captcha verification route itself
        if ($request->is('verify-captcha') || $request->is('login')) {
            return $next($request);
        }

        // If user is authenticated, proceed without captcha check
        if (Auth::check()) {
            return $next($request);
        }
        
        // For guest users, verify captcha
        if (!session('captcha_solved')) {
            return redirect()->to('/');
        }

        return $next($request);
    }
}