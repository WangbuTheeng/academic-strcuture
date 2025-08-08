<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class APIRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $key = $this->generateRateLimitKey($request, $user);
        $limit = $this->getRateLimit($user);
        $window = 60; // 1 minute window

        $current = Cache::get($key, 0);

        if ($current >= $limit) {
            $this->logRateLimitExceeded($request, $user);

            return response()->json([
                'error' => 'Rate limit exceeded',
                'limit' => $limit,
                'window' => $window
            ], 429);
        }

        Cache::put($key, $current + 1, $window);

        // Add rate limit headers to response
        $response = $next($request);
        $this->addRateLimitHeaders($response, $limit, $current + 1, $window);

        return $response;
    }

    private function generateRateLimitKey(Request $request, $user): string
    {
        return 'rate_limit:' . $user->id . ':' . $request->ip();
    }

    private function getRateLimit($user): int
    {
        // Different limits based on user type
        if ($user->hasRole('super-admin')) {
            return 1000; // Higher limit for super admins
        }

        return 100; // Default limit
    }

    private function addRateLimitHeaders($response, int $limit, int $used, int $window): void
    {
        $response->headers->set('X-RateLimit-Limit', $limit);
        $response->headers->set('X-RateLimit-Remaining', max(0, $limit - $used));
        $response->headers->set('X-RateLimit-Reset', now()->addSeconds($window)->timestamp);
    }

    private function logRateLimitExceeded(Request $request, $user): void
    {
        Log::warning('API rate limit exceeded', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'endpoint' => $request->path(),
            'method' => $request->method()
        ]);
    }
}
