<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Define temporary routes for testing the middleware
    Route::get('/_test/admin', function () {
        return 'ok';
    })->middleware(['auth', 'can:is-admin']);

    Route::get('/_test/operator', function () {
        return 'ok';
    })->middleware(['auth', 'can:is-operator']);
});

test('administrator passes is-admin and is-operator gates', function () {
    $admin = User::factory()->asAdmin()->create();

    $this->actingAs($admin)
        ->get('/_test/admin')
        ->assertStatus(200);

    $this->actingAs($admin)
        ->get('/_test/operator')
        ->assertStatus(200);
});

test('operator passes is-operator but fails is-admin gate', function () {
    $operator = User::factory()->asOperator()->create();

    $this->actingAs($operator)
        ->get('/_test/admin')
        ->assertStatus(403);

    $this->actingAs($operator)
        ->get('/_test/operator')
        ->assertStatus(200);
});

test('viewer fails both is-admin and is-operator gates', function () {
    $viewer = User::factory()->asViewer()->create();

    $this->actingAs($viewer)
        ->get('/_test/admin')
        ->assertStatus(403);

    $this->actingAs($viewer)
        ->get('/_test/operator')
        ->assertStatus(403);
});

test('unauthenticated users are redirected by auth middleware before gates', function () {
    $this->get('/_test/admin')
        ->assertRedirect('/login');

    $this->get('/_test/operator')
        ->assertRedirect('/login');
});
