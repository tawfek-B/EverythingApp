<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimitServiceProvider extends ServiceProvider
{
    public function boot()
    {
        RateLimiter::for('screenshots', function (Request $request) {
            return Limit::perSecond(5)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    if ($request->user()) {
                    }

                    return response()->json([
                        'message' => 'Account banned for excessive screenshots',
                        'retry_after' => $headers['Retry-After'] ?? null,
                    ], 429);
                });
        });
    }
}