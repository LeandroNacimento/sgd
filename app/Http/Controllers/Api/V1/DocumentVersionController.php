<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentVersionResource;
use App\Models\Document;
use App\Models\DocumentVersion;

class DocumentVersionController extends Controller
{
    public function index(Document $document)
    {
        $this->authorize('view', $document);

        $versions = $document->versions()->with('documentState')->orderByDesc('version_number')->get();

        return DocumentVersionResource::collection($versions);
    }

    public function show(Document $document, DocumentVersion $version)
    {
        $this->authorize('view', $document);

        if ($version->document_id !== $document->id) {
            abort(404);
        }

        $version->load('documentState');

        return new DocumentVersionResource($version);
    }
}
