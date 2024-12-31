<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LoginThrottle
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->throttleKey($request);

        if ($this->limiter->tooManyAttempts($key, 8)) {
            return $this->buildResponse($key);
        }

        $response = $next($request);

        if (Auth::check()) {
            $this->limiter->clear($key);
        } else {
            $this->limiter->hit($key, 40 * 60); // 1 hour
        }

        return $response;
    }

    protected function throttleKey(Request $request): string
    {
        return 'login|' . $request->ip();
    }

    protected function buildResponse($key): Response
    {
        $seconds = $this->limiter->availableIn($key);
        $minutes = ceil($seconds / 60);
        
        return response()->view('auth.throttle', [
            'minutes' => $minutes
        ], 429);
    }
}
