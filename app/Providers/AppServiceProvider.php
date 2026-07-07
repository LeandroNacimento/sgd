<?php

namespace App\Providers;

use App\Contracts\AuditLoggerInterface;
use App\Listeners\LogUserAuthentication;
use App\Services\SpatieAuditLogger;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuditLoggerInterface::class,
            SpatieAuditLogger::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::subscribe(LogUserAuthentication::class);

        // Security Hardening: Strict Eloquent mode in non-production environments
        Model::shouldBeStrict(! app()->isProduction());

        // Security Hardening: Strong Password Defaults (without uncompromised)
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        // Security Hardening: API Rate Limiting (60 requests per minute per user/ip)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
