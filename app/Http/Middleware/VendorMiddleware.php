<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VendorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->isVendor()) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}