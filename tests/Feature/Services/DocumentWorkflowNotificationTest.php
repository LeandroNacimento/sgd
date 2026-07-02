<?php

use App\Enums\DocumentStateName;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\Role as RoleModel;
use App\Models\User;
use App\Notifications\DocumentAssignedNotification;
use App\Notifications\DocumentStateChangedNotification;
use App\Services\DocumentService;
use App\Services\DocumentWorkflowService;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->adminRole = RoleModel::firstOrCreate(['name' => Role::Administrator->value]);
    $this->operatorRole = RoleModel::firstOrCreate(['name' => Role::Operator->value]);

    $this->admin1 = User::factory()->create(['role_id' => $this->adminRole->id]);
    $this->admin2 = User::factory()->create(['role_id' => $this->adminRole->id]);
    $this->operator = User::factory()->create(['role_id' => $this->operatorRole->id]);

    $this->category = Category::factory()->create();

    $this->draftState = DocumentState::firstOrCreate(['name' => DocumentStateName::Draft->value]);
    $this->reviewState = DocumentState::firstOrCreate(['name' => DocumentStateName::InReview->value]);
    $this->publishedState = DocumentState::firstOrCreate(['name' => DocumentStateName::Published->value]);

    Notification::fake();
});

test('document creation sends assignment notification to responsible user', function () {
    $service = app(DocumentService::class);

    $this->actingAs($this->operator);

    $document = $service->create([
        'title' => 'Test',
        'priority' => 'low',
        'category_id' => $this->category->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    Notification::assertSentTo(
        [$this->operator], DocumentAssignedNotification::class
    );
});

test('submitting for review notifies all admins', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->draftState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $service = app(DocumentWorkflowService::class);

    $this->actingAs($this->operator);
    $service->submitForReview($document);

    Notification::assertSentTo(
        [$this->admin1, $this->admin2], DocumentStateChangedNotification::class
    );

    // Operator should not be notified when submitting for review
    Notification::assertNotSentTo(
        [$this->operator], DocumentStateChangedNotification::class
    );
});

test('publishing notifies the responsible operator', function () {
    $document = Document::factory()->create([
        'category_id' => $this->category->id,
        'document_state_id' => $this->reviewState->id,
        'responsible_user_id' => $this->operator->id,
    ]);

    $service = app(DocumentWorkflowService::class);

    $this->actingAs($this->admin1);
    $service->publish($document);

    Notification::assertSentTo(
        [$this->operator], DocumentStateChangedNotification::class
    );

    // Admins shouldn't be notified when it's published
    Notification::assertNotSentTo(
        [$this->admin1, $this->admin2], DocumentStateChangedNotification::class
    );
});
