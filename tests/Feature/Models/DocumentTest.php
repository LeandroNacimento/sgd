<?php

use App\Enums\DocumentPriority;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\User;
use Illuminate\Database\QueryException;

test('it can be created and cast priority to enum', function () {
    $document = Document::factory()->create([
        'code' => 'DOC-TEST-001',
        'priority' => DocumentPriority::High->value,
    ]);

    expect($document->code)->toBe('DOC-TEST-001')
        ->and($document->priority)->toBeInstanceOf(DocumentPriority::class)
        ->and($document->priority)->toBe(DocumentPriority::High);
});

test('it soft deletes', function () {
    $document = Document::factory()->create();
    $document->delete();

    $this->assertSoftDeleted($document);
});

test('it belongs to a category', function () {
    $category = Category::factory()->create();
    $document = Document::factory()->create(['category_id' => $category->id]);

    expect($document->category->id)->toBe($category->id);
});

test('it belongs to a document state', function () {
    $state = DocumentState::factory()->create();
    $document = Document::factory()->create(['document_state_id' => $state->id]);

    expect($document->documentState->id)->toBe($state->id);
});

test('it belongs to a responsible user', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create(['responsible_user_id' => $user->id]);

    expect($document->responsibleUser->id)->toBe($user->id);
});

test('it enforces unique document code', function () {
    Document::factory()->create(['code' => 'DOC-UNIQUE']);

    $this->expectException(QueryException::class);
    Document::factory()->create(['code' => 'DOC-UNIQUE']);
});

test('it enforces foreign key integrity for category', function () {
    $this->expectException(QueryException::class);
    Document::factory()->create(['category_id' => 99999]);
});
