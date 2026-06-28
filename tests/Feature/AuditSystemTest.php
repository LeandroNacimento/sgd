<?php

use App\Enums\DocumentPriority;
use App\Enums\DocumentStateName;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => App\Enums\Role::Administrator->value]);
    $this->operatorRole = Role::firstOrCreate(['name' => App\Enums\Role::Operator->value]);

    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
    $this->operator = User::factory()->create(['role_id' => $this->operatorRole->id]);

    $this->category = Category::factory()->create();

    // Ensure states exist
    $this->draftState = DocumentState::firstOrCreate(['name' => DocumentStateName::Draft->value]);
    $this->reviewState = DocumentState::firstOrCreate(['name' => DocumentStateName::InReview->value]);
    $this->publishedState = DocumentState::firstOrCreate(['name' => DocumentStateName::Published->value]);

    Storage::fake('local');
});

test('document creation logs activity', function () {
    $this->actingAs($this->operator)
        ->post(route('documents.store'), [
            'title' => 'Test Document',
            'description' => 'A description',
            'category_id' => $this->category->id,
            'priority' => DocumentPriority::Low->value,
        ])
        ->assertRedirect();

    $document = Document::first();

    $activity = Activity::forSubject($document->currentVersion)->where('event', 'document.created')->first();
    expect($activity)->not->toBeNull()
        ->and($activity->causer_id)->toBe($this->operator->id)
        ->and($activity->causer_type)->toBe(User::class);
});

test('document update logs activity with dirty properties', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'responsible_user_id' => $this->operator->id,
        'title' => 'Old Title',
    ]);

    $this->actingAs($this->operator)
        ->put(route('documents.update', $document), [
            'title' => 'New Title',
            'description' => $document->currentVersion->description,
            'category_id' => $document->category_id,
            'priority' => $document->priority->value,
        ])
        ->assertRedirect();

    $activity = Activity::forSubject($document->currentVersion)->where('event', 'document.updated')->first();

    expect($activity)->not->toBeNull()
        ->and($activity->properties->toArray())->toHaveKey('title', 'New Title');
});

test('attachment upload and delete logs activity', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

    $this->actingAs($this->operator)
        ->post(route('documents.attachments.store', $document), ['file' => $file])
        ->assertRedirect();

    $activityUpload = Activity::forSubject($document->currentVersion)->where('event', 'attachment.uploaded')->first();
    expect($activityUpload)->not->toBeNull()
        ->and($activityUpload->properties['filename'])->toBe('test.pdf');

    $media = $document->currentVersion->getMedia('attachments')->first();

    $this->actingAs($this->operator)
        ->delete(route('documents.attachments.destroy', [$document, $media]))
        ->assertRedirect();

    $activityDelete = Activity::forSubject($document->currentVersion)->where('event', 'attachment.deleted')->first();
    expect($activityDelete)->not->toBeNull()
        ->and($activityDelete->properties['filename'])->toBe('test.pdf');
});

test('workflow transition logs activity', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $this->actingAs($this->operator)
        ->post(route('documents.workflow.submitForReview', $document))
        ->assertRedirect();

    $activity = Activity::forSubject($document->currentVersion)->where('event', 'workflow.transition')->first();

    expect($activity)->not->toBeNull()
        ->and($activity->properties['from_state'])->toBe(DocumentStateName::Draft->value)
        ->and($activity->properties['to_state'])->toBe(DocumentStateName::InReview->value);
});

test('admin can see audit trail but operator cannot', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    // Create dummy activity
    activity()->causedBy($this->operator)->performedOn($document->currentVersion)->event('document.created')->log('created');

    // Operators should NOT see the audit section heading
    $this->actingAs($this->operator)
        ->get(route('documents.show', $document))
        ->assertOk()
        ->assertDontSee(__('documents.audit_trail'));

    // Admins should see the heading and the activity entry
    $this->actingAs($this->admin)
        ->get(route('documents.show', $document))
        ->assertOk()
        ->assertSee(__('documents.audit_trail'))
        ->assertViewHas('activities', fn ($activities) => $activities->isNotEmpty());
});
