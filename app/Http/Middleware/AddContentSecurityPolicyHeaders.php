<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddContentSecurityPolicyHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Content Security Policy (CSP)
        $response->headers->set('Content-Security-Policy', "
            default-src 'self';
            script-src 'self' 'unsafe-inline' 'unsafe-eval';
            style-src 'self' 'unsafe-inline';
            img-src 'self' data: https: http:;
            font-src 'self' data:;
            connect-src 'self' http: https:;
            frame-src 'none';
            frame-ancestors 'none';
            object-src 'none';
            base-uri 'self';
            form-action 'self';
            manifest-src 'self';
            media-src 'self';
            worker-src 'self' blob:;
            style-src-elem 'self' 'unsafe-inline' http: https:;
            style-src-attr 'unsafe-inline';
        ");

        // X-Content-Type-Options
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-Frame-Options
        $response->headers->set('X-Frame-Options', 'DENY');

        // X-XSS-Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy (formerly Feature-Policy)
        $response->headers->set('Permissions-Policy', 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()');

        // Strict-Transport-Security (HSTS)
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}