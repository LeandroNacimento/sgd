<?php

use App\Models\Document;
use App\Models\DocumentState;

test('it can be created', function () {
    $state = DocumentState::factory()->create(['name' => 'Published']);
    expect($state->name)->toBe('Published');
    $this->assertDatabaseHas('document_states', ['name' => 'Published']);
});

test('it has many documents', function () {
    $state = DocumentState::factory()->create();

    Document::factory()->count(3)->create([
        'document_state_id' => $state->id,
    ]);

    expect($state->documents)->toHaveCount(3);
});
