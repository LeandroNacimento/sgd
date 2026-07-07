<?php

namespace App\Listeners;

use App\Contracts\AuditLoggerInterface;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

class LogUserAuthentication
{
    public function __construct(private readonly AuditLoggerInterface $auditLogger) {}

    public function handleLogin(Login $event): void
    {
        /** @var User|null $user */
        $user = $event->user;
        if ($user instanceof User) {
            $this->auditLogger->logUserLogin($user);
        }
    }

    public function handleLogout(Logout $event): void
    {
        /** @var User|null $user */
        $user = $event->user;
        if ($user instanceof User) {
            $this->auditLogger->logUserLogout($user);
        }
    }

    public function handleFailed(Failed $event): void
    {
        $email = $event->credentials['email'] ?? 'unknown';
        $this->auditLogger->logFailedLogin($email);
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
        ];
    }
}
