<?php

use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => App\Enums\Role::Administrator->value]);
    $this->operatorRole = Role::firstOrCreate(['name' => App\Enums\Role::Operator->value]);
    $this->viewerRole = Role::firstOrCreate(['name' => App\Enums\Role::Viewer->value]);

    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
    $this->operator = User::factory()->create(['role_id' => $this->operatorRole->id]);
    $this->viewer = User::factory()->create(['role_id' => $this->viewerRole->id]);

    $this->category = Category::factory()->create();
    $this->state = DocumentState::factory()->create(['name' => 'Draft']);
    $this->archivedState = DocumentState::factory()->create(['name' => 'Archived']);

    Storage::fake('local');
});

test('operator can upload valid attachment', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->state->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

    $this->actingAs($this->operator)
        ->post(route('documents.attachments.store', $document), ['file' => $file])
        ->assertRedirect();

    expect($document->currentVersion->getMedia('attachments'))->toHaveCount(1);

    $media = $document->currentVersion->getMedia('attachments')->first();
    expect($media->file_name)->toBe('document.pdf');
});

test('viewer cannot upload attachment', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->state->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

    $this->actingAs($this->viewer)
        ->post(route('documents.attachments.store', $document), ['file' => $file])
        ->assertForbidden();

    expect($document->currentVersion->getMedia('attachments'))->toHaveCount(0);
});

test('cannot upload attachment to archived document', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->archivedState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

    $this->actingAs($this->operator)
        ->post(route('documents.attachments.store', $document), ['file' => $file])
        ->assertForbidden();
});

test('cannot upload invalid file type', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->state->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $file = UploadedFile::fake()->create('malicious.exe', 1024, 'application/x-msdownload');

    $this->actingAs($this->operator)
        ->post(route('documents.attachments.store', $document), ['file' => $file])
        ->assertSessionHasErrors('file');

    expect($document->currentVersion->getMedia('attachments'))->toHaveCount(0);
});

test('authorized user can download attachment', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->state->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
    $document->currentVersion->addMedia($file)->toMediaCollection('attachments');
    $media = $document->currentVersion->getMedia('attachments')->first();

    $this->actingAs($this->viewer)
        ->get(route('documents.attachments.download', [$document, $media]))
        ->assertSuccessful();
});

test('cannot access attachment belonging to another document', function () {
    $doc1 = Document::factory()->create(['category_id' => $this->category->id, 'document_state_id' => $this->state->id]);
    $doc2 = Document::factory()->create(['category_id' => $this->category->id, 'document_state_id' => $this->state->id]);

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
    $doc1->currentVersion->addMedia($file)->toMediaCollection('attachments');
    $media = $doc1->currentVersion->getMedia('attachments')->first();

    // Try to access doc1's media through doc2's route
    $this->actingAs($this->admin)
        ->get(route('documents.attachments.download', [$doc2, $media]))
        ->assertNotFound();

    $this->actingAs($this->admin)
        ->delete(route('documents.attachments.destroy', [$doc2, $media]))
        ->assertNotFound();
});

test('authorized user can delete attachment', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->state->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
    $document->currentVersion->addMedia($file)->toMediaCollection('attachments');
    $media = $document->currentVersion->getMedia('attachments')->first();

    $this->actingAs($this->operator)
        ->delete(route('documents.attachments.destroy', [$document, $media]))
        ->assertRedirect();

    expect($document->refresh()->currentVersion->getMedia('attachments'))->toHaveCount(0);
});

test('maximum of 5 attachments enforced', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->state->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    // Add 5 fake media items
    for ($i = 0; $i < 5; $i++) {
        $file = UploadedFile::fake()->create("test{$i}.pdf", 100, 'application/pdf');
        $document->currentVersion->addMedia($file)->toMediaCollection('attachments');
    }

    // Attempt to add 6th
    $file6 = UploadedFile::fake()->create('test6.pdf', 100, 'application/pdf');

    $this->actingAs($this->operator)
        ->post(route('documents.attachments.store', $document), ['file' => $file6])
        ->assertSessionHasErrors('file');

    expect($document->refresh()->currentVersion->getMedia('attachments'))->toHaveCount(5);
});
