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

    $response->assertSessionHasErrors(['title', 'priority', 'category_id']);
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

test('document code generator continues sequentially from existing document', function () {
    // Manually create a document with a specific sequence
    $year = date('Y');
    Document::factory()->create([
        'code' => "DOC-{$year}-000123",
        'document_state_id' => $this->state->id,
    ]);

    // Create a new document via the controller
    $this->actingAs($this->operator)
        ->post(route('documents.store'), [
            'title' => 'Sequential Document',
            'description' => 'Test',
            'priority' => DocumentPriority::Low->value,
            'category_id' => $this->category->id,
            'document_state_id' => $this->state->id,
        ]);

    // Check that the next document gets the next sequence number
    $latestDocument = Document::latest('id')->first();
    expect($latestDocument->code)->toBe("DOC-{$year}-000124");
});

test('documents can be filtered by search, category, state, and priority', function () {
    $category2 = Category::factory()->create();
    $state2 = DocumentState::factory()->create(['name' => 'Published']);

    $doc1 = Document::factory()->create([
        'title' => 'Alpha Document',
        'code' => 'DOC-2026-100001',
        'category_id' => $this->category->id,
        'document_state_id' => $this->state->id,
        'priority' => DocumentPriority::High->value,
    ]);

    $doc2 = Document::factory()->create([
        'title' => 'Beta Record',
        'code' => 'DOC-2026-100002',
        'category_id' => $category2->id,
        'document_state_id' => $state2->id,
        'priority' => DocumentPriority::Low->value,
    ]);

    // Test text search (matches doc1 title)
    $this->actingAs($this->admin)
        ->get(route('documents.index', ['search' => 'Alpha']))
        ->assertSee($doc1->title)
        ->assertDontSee($doc2->title);

    // Test text search (matches doc2 code)
    $this->actingAs($this->admin)
        ->get(route('documents.index', ['search' => '100002']))
        ->assertSee($doc2->title)
        ->assertDontSee($doc1->title);

    // Test category filter
    $this->actingAs($this->admin)
        ->get(route('documents.index', ['category_id' => $category2->id]))
        ->assertSee($doc2->title)
        ->assertDontSee($doc1->title);

    // Test priority filter
    $this->actingAs($this->admin)
        ->get(route('documents.index', ['priority' => DocumentPriority::High->value]))
        ->assertSee($doc1->title)
        ->assertDontSee($doc2->title);

    // Test combined filters (match none)
    $this->actingAs($this->admin)
        ->get(route('documents.index', [
            'category_id' => $this->category->id,
            'priority' => DocumentPriority::Low->value, // doc1 is High, doc2 is category2
        ]))
        ->assertDontSee($doc1->title)
        ->assertDontSee($doc2->title);

    // Test combined filters (match doc1)
    $this->actingAs($this->admin)
        ->get(route('documents.index', [
            'search' => 'Alpha',
            'document_state_id' => $this->state->id,
        ]))
        ->assertSee($doc1->title)
        ->assertDontSee($doc2->title);
});
