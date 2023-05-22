<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecruiterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role === 'recruiter') {
            return $next($request);
        }

        return response()->json(['status' => 'failed', 'error' => 'You must be a recruiter to access this resource.'], Response::HTTP_UNAUTHORIZED);
    }
}
