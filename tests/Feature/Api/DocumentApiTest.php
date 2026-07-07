<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Document;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('requires authentication to access documents api', function () {
    $response = $this->getJson('/api/v1/documents');
    $response->assertStatus(401);
});

it('lists documents for authenticated users', function () {
    $user = User::factory()->create();
    $user->roles()->attach(Role::factory()->create(['name' => 'Operator']));

    Sanctum::actingAs($user, ['*']);

    $category = Category::factory()->create();
    Document::factory()->count(3)->create(['category_id' => $category->id]);

    $response = $this->getJson('/api/v1/documents');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'code',
                'category',
                'priority',
                'created_at',
            ],
        ],
        'meta',
        'links',
    ]);
});

it('returns document details', function () {
    $user = User::factory()->create();
    $user->roles()->attach(Role::factory()->create(['name' => 'Operator']));

    Sanctum::actingAs($user, ['*']);

    $category = Category::factory()->create();
    $document = Document::factory()->create(['category_id' => $category->id]);

    $response = $this->getJson("/api/v1/documents/{$document->id}");

    $response->assertStatus(200);
    $response->assertJsonPath('data.id', $document->id);
    $response->assertJsonPath('data.code', $document->code);
});
