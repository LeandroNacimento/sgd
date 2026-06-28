<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('is-admin', fn (User $user): bool => $user->hasRole(Role::Administrator)
        );

        Gate::define('is-operator', fn (User $user): bool => $user->hasRole(Role::Operator) || $user->hasRole(Role::Administrator)
        );
    }
}
