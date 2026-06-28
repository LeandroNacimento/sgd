<?php

use App\Models\DocumentState;
use App\Models\DocumentVersion;

test('it can be created', function () {
    $state = DocumentState::factory()->create(['name' => 'Published']);
    expect($state->name)->toBe('Published');
    $this->assertDatabaseHas('document_states', ['name' => 'Published']);
});

test('it has many document versions', function () {
    $state = DocumentState::factory()->create();

    DocumentVersion::factory()->count(3)->create([
        'document_state_id' => $state->id,
    ]);

    expect($state->documentVersions)->toHaveCount(3);
});
