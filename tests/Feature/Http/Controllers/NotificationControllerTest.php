<?php

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

test('index shows notifications and paginates', function () {
    $user = User::factory()->create();

    // Create a mock notification in the database
    DatabaseNotification::create([
        'id' => Str::uuid(),
        'type' => 'App\Notifications\TestNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => ['message' => 'Test message'],
        'read_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Test message');
});

test('mark as read marks a single notification as read', function () {
    $user = User::factory()->create();

    $notification = DatabaseNotification::create([
        'id' => Str::uuid(),
        'type' => 'App\Notifications\TestNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => ['message' => 'Test message'],
        'read_at' => null,
    ]);

    expect($notification->read_at)->toBeNull();

    $this->actingAs($user)
        ->post(route('notifications.markRead', $notification->id))
        ->assertRedirect();

    $notification->refresh();
    expect($notification->read_at)->not->toBeNull();
});

test('mark all as read marks all unread notifications as read', function () {
    $user = User::factory()->create();

    DatabaseNotification::create([
        'id' => Str::uuid(),
        'type' => 'App\Notifications\TestNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => ['message' => 'First message'],
        'read_at' => null,
    ]);

    DatabaseNotification::create([
        'id' => Str::uuid(),
        'type' => 'App\Notifications\TestNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => ['message' => 'Second message'],
        'read_at' => null,
    ]);

    expect($user->unreadNotifications()->count())->toBe(2);

    $this->actingAs($user)
        ->post(route('notifications.markAllRead'))
        ->assertRedirect();

    expect($user->unreadNotifications()->count())->toBe(0);
});
