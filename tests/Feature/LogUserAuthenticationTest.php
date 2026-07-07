<?php

namespace Tests\Feature;

use App\Contracts\AuditLoggerInterface;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Mockery;

it('logs user login event', function () {
    $user = User::factory()->create();

    $auditLogger = Mockery::mock(AuditLoggerInterface::class);
    $auditLogger->shouldReceive('logUserLogin')->once()->with($user);

    $this->app->instance(AuditLoggerInterface::class, $auditLogger);

    event(new Login('web', $user, false));
});

it('logs user logout event', function () {
    $user = User::factory()->create();

    $auditLogger = Mockery::mock(AuditLoggerInterface::class);
    $auditLogger->shouldReceive('logUserLogout')->once()->with($user);

    $this->app->instance(AuditLoggerInterface::class, $auditLogger);

    event(new Logout('web', $user));
});

it('logs failed login event', function () {
    $auditLogger = Mockery::mock(AuditLoggerInterface::class);
    $auditLogger->shouldReceive('logFailedLogin')->once()->with('test@example.com');

    $this->app->instance(AuditLoggerInterface::class, $auditLogger);

    event(new Failed('web', null, ['email' => 'test@example.com']));
});
