<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUnverifiedPgpKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user->pgpKey || $user->pgpKey->verified) {
            return redirect()->route('profile')->with('info', 'You do not have an unverified PGP key to confirm.');
        }

        return $next($request);
    }
}