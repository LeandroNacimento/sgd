<?php

use App\Enums\DocumentStateName;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->operatorRole = Role::firstOrCreate(['name' => App\Enums\Role::Operator->value]);
    $this->user = User::factory()->create(['role_id' => $this->operatorRole->id]);

    $this->category = Category::factory()->create();

    $this->draftState = DocumentState::firstOrCreate(['name' => DocumentStateName::Draft->value]);
    $this->reviewState = DocumentState::firstOrCreate(['name' => DocumentStateName::InReview->value]);
    $this->publishedState = DocumentState::firstOrCreate(['name' => DocumentStateName::Published->value]);
});

test('dashboard loads for authenticated users when empty', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('total_documents', 0)
        ->assertViewHas('recent_documents', fn ($v) => $v->isEmpty())
        ->assertViewHas('recent_activities', fn ($v) => $v->isEmpty());
});

test('dashboard displays correct document metrics', function () {
    // Create 3 Drafts
    Document::factory()->count(3)->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'responsible_user_id' => $this->user->id,
    ]);

    // Create 2 In Review
    Document::factory()->count(2)->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->reviewState->id,
        'responsible_user_id' => $this->user->id,
    ]);

    // Create 1 Published
    Document::factory()->count(1)->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->publishedState->id,
        'responsible_user_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('total_documents', 6)
        ->assertViewHas('documents_by_state', function ($states) {
            return $states[DocumentStateName::Draft->value]['count'] === 3
                && $states[DocumentStateName::InReview->value]['count'] === 2
                && $states[DocumentStateName::Published->value]['count'] === 1;
        });
});

test('dashboard links point to correctly filtered document list', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('documents.index', ['document_state_id' => $this->reviewState->id]));
});

test('dashboard displays recent activities with document data', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'responsible_user_id' => $this->user->id,
    ]);

    activity()
        ->causedBy($this->user)
        ->performedOn($document->currentVersion)
        ->event('workflow.transition')
        ->withProperties([
            'from_state' => DocumentStateName::Draft->value,
            'to_state' => DocumentStateName::InReview->value,
        ])
        ->log('Workflow transitioned');

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('recent_activities', fn ($activities) => $activities->isNotEmpty())
        ->assertSee($document->code)
        ->assertSee(DocumentStateName::Draft->value)
        ->assertSee(DocumentStateName::InReview->value);
});
