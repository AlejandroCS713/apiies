<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('products', function ($request) {
            if ($request->user()?->role === 'admin') {
                return Limit::none();
            }
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip())->response(function (){
                return response()->json([
                    'status' => 429,
                    'message' => 'Too many requests'
                ], 429);
            });
        });
    }
}
