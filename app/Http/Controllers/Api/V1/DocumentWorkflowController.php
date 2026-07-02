<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Services\DocumentVersionService;
use App\Services\DocumentWorkflowService;
use Illuminate\Http\Request;

class DocumentWorkflowController extends Controller
{
    public function __construct(
        private readonly DocumentWorkflowService $workflowService,
        private readonly DocumentVersionService $versionService
    ) {}

    public function submit(Request $request, Document $document)
    {
        $this->authorize('submit', $document);
        $this->workflowService->submitForReview($document);

        return new DocumentResource($document->refresh());
    }

    public function publish(Request $request, Document $document)
    {
        $this->authorize('publish', $document);
        $this->workflowService->publish($document);

        return new DocumentResource($document->refresh());
    }

    public function reject(Request $request, Document $document)
    {
        $this->authorize('reject', $document);
        $this->workflowService->reject($document);

        return new DocumentResource($document->refresh());
    }

    public function archive(Request $request, Document $document)
    {
        $this->authorize('archive', $document);
        $this->workflowService->archive($document);

        return new DocumentResource($document->refresh());
    }

    public function newVersion(Request $request, Document $document)
    {
        $this->authorize('createNewVersion', $document);
        $this->versionService->createNewVersion($document);

        return new DocumentResource($document->refresh());
    }
}
