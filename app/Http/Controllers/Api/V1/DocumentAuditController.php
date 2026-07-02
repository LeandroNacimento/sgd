<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\AuditLoggerInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\TimelineResource;
use App\Models\Document;

class DocumentAuditController extends Controller
{
    public function __construct(private readonly AuditLoggerInterface $auditLogger) {}

    public function index(Document $document)
    {
        $this->authorize('view', $document);

        $timeline = collect($this->auditLogger->getDocumentTimeline($document));

        return TimelineResource::collection($timeline);
    }
}
