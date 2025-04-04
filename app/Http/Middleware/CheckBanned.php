<?php

// app/Http/Middleware/CheckBanned.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('banned')) {
            return $next($request);
        }
    
        if (auth()->check() && auth()->user()->isBanned()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('banned');
        }
    
        return $next($request);
    }    
}
