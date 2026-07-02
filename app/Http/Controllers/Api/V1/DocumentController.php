<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Services\DocumentService;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentService $documentService) {}

    public function index(IndexDocumentRequest $request)
    {
        $this->authorize('viewAny', Document::class);

        $documents = Document::query()
            ->with(['category', 'responsibleUser', 'currentVersion.documentState'])
            ->filter($request->validated())
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return DocumentResource::collection($documents);
    }

    public function store(StoreDocumentRequest $request)
    {
        $this->authorize('create', Document::class);

        $data = $request->validated();
        $data['responsible_user_id'] = $request->user()->id;

        $document = $this->documentService->create($data);

        return new DocumentResource($document->load(['category', 'responsibleUser', 'currentVersion.documentState']));
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['category', 'responsibleUser', 'currentVersion.documentState']);

        return new DocumentResource($document);
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $this->authorize('update', $document);

        $this->documentService->update($document, $request->validated());

        return new DocumentResource($document->refresh()->load(['category', 'responsibleUser', 'currentVersion.documentState']));
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        $this->documentService->delete($document);

        return response()->noContent();
    }
}
