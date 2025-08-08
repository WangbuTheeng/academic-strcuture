<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super-admin bypass
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Ensure user has school context
        if (!$user->school_id || !session('school_context')) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'login' => 'Invalid school context. Please login again.'
            ]);
        }

        // Verify school context matches user's school
        if (session('school_context') !== $user->school_id) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'login' => 'School context mismatch. Please login again.'
            ]);
        }

        return $next($request);
    }
}
