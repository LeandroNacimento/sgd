<?php

use App\Enums\DocumentStateName;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\Role as RoleModel;
use App\Models\User;

beforeEach(function () {
    $this->adminRole = RoleModel::firstOrCreate(['name' => Role::Administrator->value]);
    $this->operatorRole = RoleModel::firstOrCreate(['name' => Role::Operator->value]);
    $this->viewerRole = RoleModel::firstOrCreate(['name' => Role::Viewer->value]);

    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
    $this->operator = User::factory()->create(['role_id' => $this->operatorRole->id]);
    $this->viewer = User::factory()->create(['role_id' => $this->viewerRole->id]);

    $this->category = Category::factory()->create();

    // Create necessary states
    $this->draftState = DocumentState::firstOrCreate(['name' => DocumentStateName::Draft->value]);
    $this->publishedState = DocumentState::firstOrCreate(['name' => DocumentStateName::Published->value]);
});

test('operator can create a new version from a published document', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->publishedState->id,
        'title' => 'Original Title',
    ]);

    // Ensure it's published initially
    expect($document->isPublished())->toBeTrue();
    expect($document->versions()->count())->toBe(1);

    $response = $this->actingAs($this->operator)
        ->post(route('documents.versions.store', $document));

    $response->assertRedirect(route('documents.show', $document));

    $document->refresh();

    // The document should now be pointing to the new Draft version
    expect($document->versions()->count())->toBe(2);
    expect($document->isDraft())->toBeTrue();
    expect($document->currentVersion->version_number)->toBe(2);
    expect($document->currentVersion->title)->toBe('Original Title');
});

test('operator cannot create a new version from a draft document', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'title' => 'Draft Title',
    ]);

    $response = $this->actingAs($this->operator)
        ->post(route('documents.versions.store', $document));

    $response->assertForbidden();
    $document->refresh();

    expect($document->versions()->count())->toBe(1);
});

test('viewer cannot create new version', function () {
    $document = Document::factory()->create([
        'document_state_id' => $this->publishedState->id,
    ]);

    $this->actingAs($this->viewer)
        ->post(route('documents.versions.store', $document))
        ->assertForbidden();
});

test('operator can revert to a previous version', function () {
    // Start with a published document (v1.0)
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->publishedState->id,
        'title' => 'Version 1',
    ]);
    $version1 = $document->currentVersion;

    // Create a new version (v2.0) which is draft
    $this->actingAs($this->operator)
        ->post(route('documents.versions.store', $document));

    $document->refresh();
    $version2 = $document->currentVersion;

    // Change title of v2.0
    $version2->update(['title' => 'Version 2 with errors']);

    // Now revert to v1.0
    $response = $this->actingAs($this->operator)
        ->post(route('documents.versions.revert', ['document' => $document, 'version' => $version1]));

    $response->assertRedirect(route('documents.show', $document));
    $response->assertSessionHas('success');

    $document->refresh();

    // Document should now be at v3.0 (Draft) with v1.0 data
    expect($document->versions()->count())->toBe(3);
    expect($document->currentVersion->version_number)->toBe(3);
    expect($document->isDraft())->toBeTrue();
    expect($document->currentVersion->title)->toBe('Version 1');
});
