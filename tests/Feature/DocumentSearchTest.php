<?php

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters documents using scout search', function () {
    $doc1 = Document::factory()->create(['code' => 'DOC-001']);
    $version1 = DocumentVersion::factory()->create([
        'document_id' => $doc1->id,
        'title' => 'Financial Report 2026',
        'extracted_text' => 'Azure revenues increased by 20%',
    ]);
    $doc1->update(['current_version_id' => $version1->id]);

    $doc2 = Document::factory()->create(['code' => 'DOC-002']);
    $version2 = DocumentVersion::factory()->create([
        'document_id' => $doc2->id,
        'title' => 'HR Policies',
        'extracted_text' => 'Vacation days are important',
    ]);
    $doc2->update(['current_version_id' => $version2->id]);

    $results1 = Document::filter(['search' => 'Azure'])->get();
    expect($results1)->toHaveCount(1)
        ->and($results1->first()->id)->toBe($doc1->id);

    $results2 = Document::filter(['search' => 'HR Policies'])->get();
    expect($results2)->toHaveCount(1)
        ->and($results2->first()->id)->toBe($doc2->id);
});
