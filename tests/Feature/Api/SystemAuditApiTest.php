<?php

namespace Tests\Feature\Api;

use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Activitylog\Models\Activity;

it('forbids operators from viewing system audit api', function () {
    $operator = User::factory()->create();
    $operator->roles()->attach(Role::factory()->create(['name' => 'Operator']));

    Sanctum::actingAs($operator, ['*']);

    $response = $this->getJson('/api/v1/audit-logs');

    $response->assertStatus(403);
});

it('allows administrators to view system audit api', function () {
    $admin = User::factory()->create();
    $admin->roles()->attach(Role::factory()->create(['name' => 'Administrator']));

    Sanctum::actingAs($admin, ['*']);

    Activity::create([
        'log_name' => 'default',
        'description' => 'Test event api',
        'subject_type' => User::class,
        'subject_id' => $admin->id,
        'causer_type' => User::class,
        'causer_id' => $admin->id,
    ]);

    $response = $this->getJson('/api/v1/audit-logs');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'event',
                'description',
                'subject_type',
                'subject_id',
                'causer',
                'properties',
                'created_at',
            ],
        ],
        'meta',
        'links',
    ]);
});
