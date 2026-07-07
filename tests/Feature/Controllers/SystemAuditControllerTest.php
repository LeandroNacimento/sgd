<?php

namespace Tests\Feature\Controllers;

use App\Models\Role;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

it('allows administrators to view system audit logs', function () {
    $admin = User::factory()->create();
    $admin->roles()->attach(Role::factory()->create(['name' => 'Administrator']));

    Activity::create([
        'log_name' => 'default',
        'description' => 'Test event',
        'subject_type' => User::class,
        'subject_id' => $admin->id,
        'causer_type' => User::class,
        'causer_id' => $admin->id,
    ]);

    $response = $this->actingAs($admin)->get(route('audit-logs.index'));

    $response->assertStatus(200);
    $response->assertSee('Test event');
});

it('forbids operators from viewing system audit logs', function () {
    $operator = User::factory()->create();
    $operator->roles()->attach(Role::factory()->create(['name' => 'Operator']));

    $response = $this->actingAs($operator)->get(route('audit-logs.index'));

    $response->assertStatus(403);
});
