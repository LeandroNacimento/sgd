<?php

use App\Enums\DocumentPriority;
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
    $this->state = DocumentState::factory()->create(['name' => 'Draft']);
    $this->archivedState = DocumentState::factory()->create(['name' => 'Archived']);
});

test('guest is redirected to login', function () {
    $this->get(route('documents.index'))->assertRedirect(route('login'));
    $this->get(route('documents.create'))->assertRedirect(route('login'));
    $this->post(route('documents.store'))->assertRedirect(route('login'));
});

test('viewer cannot create document', function () {
    $this->actingAs($this->viewer)
        ->get(route('documents.create'))
        ->assertForbidden();

    $this->actingAs($this->viewer)
        ->post(route('documents.store'), [])
        ->assertForbidden();
});

test('operator can view create form and store document', function () {
    $this->actingAs($this->operator)
        ->get(route('documents.create'))
        ->assertOk();

    $response = $this->actingAs($this->operator)
        ->post(route('documents.store'), [
            'title' => 'Test Document',
            'description' => 'Test Description',
            'priority' => DocumentPriority::Medium->value,
            'category_id' => $this->category->id,
            'document_state_id' => $this->state->id,
        ]);

    $response->assertRedirect(route('documents.index'));
    $this->assertDatabaseHas('documents', [
        'title' => 'Test Document',
        'priority' => DocumentPriority::Medium->value,
    ]);

    // Check code generation format
    $document = Document::first();
    expect($document->code)->toMatch('/^DOC-'.date('Y').'-\d{6}$/');
});

test('validation failures on store', function () {
    $response = $this->actingAs($this->operator)
        ->post(route('documents.store'), [
            'title' => '', // required
            'priority' => 'invalid_priority',
            'category_id' => 999, // does not exist
            'document_state_id' => 999,
        ]);

    $response->assertSessionHasErrors(['title', 'priority', 'category_id', 'document_state_id']);
});

test('operator can update active document', function () {
    $document = Document::factory()->create([
        'document_state_id' => $this->state->id,
        'title' => 'Old Title',
    ]);

    $response = $this->actingAs($this->operator)
        ->put(route('documents.update', $document), [
            'title' => 'New Title',
            'description' => 'New Desc',
            'priority' => DocumentPriority::High->value,
            'category_id' => $this->category->id,
            'document_state_id' => $this->state->id,
        ]);

    $response->assertRedirect(route('documents.index'));
    $this->assertDatabaseHas('documents', ['id' => $document->id, 'title' => 'New Title']);
});

test('archived document cannot be updated', function () {
    $document = Document::factory()->create([
        'document_state_id' => $this->archivedState->id,
    ]);

    $this->actingAs($this->admin)
        ->put(route('documents.update', $document), [
            'title' => 'New Title',
            'priority' => DocumentPriority::High->value,
            'category_id' => $this->category->id,
            'document_state_id' => $this->archivedState->id,
        ])
        ->assertForbidden();
});

test('operator cannot delete document', function () {
    $document = Document::factory()->create();

    $this->actingAs($this->operator)
        ->delete(route('documents.destroy', $document))
        ->assertForbidden();
});

test('admin can delete document and it soft deletes', function () {
    $document = Document::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete(route('documents.destroy', $document));

    $response->assertRedirect(route('documents.index'));
    $this->assertSoftDeleted($document);
});
