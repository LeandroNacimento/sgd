<?php

namespace App\Providers;

use App\Contracts\AuditLoggerInterface;
use App\Listeners\LogUserAuthentication;
use App\Services\SpatieAuditLogger;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
    }
}
