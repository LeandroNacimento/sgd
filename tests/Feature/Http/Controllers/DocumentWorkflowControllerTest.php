<?php

use App\Enums\DocumentStateName;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => App\Enums\Role::Administrator->value]);
    $this->operatorRole = Role::firstOrCreate(['name' => App\Enums\Role::Operator->value]);
    $this->viewerRole = Role::firstOrCreate(['name' => App\Enums\Role::Viewer->value]);

    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
    $this->operator = User::factory()->create(['role_id' => $this->operatorRole->id]);
    $this->viewer = User::factory()->create(['role_id' => $this->viewerRole->id]);

    $this->category = Category::factory()->create();

    // Ensure states exist
    $this->draftState = DocumentState::firstOrCreate(['name' => DocumentStateName::Draft->value]);
    $this->reviewState = DocumentState::firstOrCreate(['name' => DocumentStateName::InReview->value]);
    $this->publishedState = DocumentState::firstOrCreate(['name' => DocumentStateName::Published->value]);
    $this->archivedState = DocumentState::firstOrCreate(['name' => DocumentStateName::Archived->value]);
});

test('operator can submit draft for review', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $this->actingAs($this->operator)
        ->post(route('documents.workflow.submitForReview', $document))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($document->refresh()->isInReview())->toBeTrue();
});

test('admin can submit draft for review', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'responsible_user_id' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
        ->post(route('documents.workflow.submitForReview', $document))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($document->refresh()->isInReview())->toBeTrue();
});

test('operator cannot publish', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->reviewState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $this->actingAs($this->operator)
        ->post(route('documents.workflow.publish', $document))
        ->assertForbidden();
});

test('admin can publish document in review', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->reviewState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $this->actingAs($this->admin)
        ->post(route('documents.workflow.publish', $document))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($document->refresh()->isPublished())->toBeTrue();
});

test('admin can reject document in review', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->reviewState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $this->actingAs($this->admin)
        ->post(route('documents.workflow.reject', $document))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($document->refresh()->isDraft())->toBeTrue();
});

test('admin can archive published document', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->publishedState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $this->actingAs($this->admin)
        ->post(route('documents.workflow.archive', $document))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($document->refresh()->isArchived())->toBeTrue();
});

test('cannot archive document that is not published', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    // Authorized by admin, but should fail domain logic (caught by policy first though, because Policy requires isPublished)
    $this->actingAs($this->admin)
        ->post(route('documents.workflow.archive', $document))
        ->assertForbidden();
});

test('cannot submit published document for review', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->publishedState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $this->actingAs($this->operator)
        ->post(route('documents.workflow.submitForReview', $document))
        ->assertForbidden();
});
