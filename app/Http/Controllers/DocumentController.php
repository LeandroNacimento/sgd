<?php

namespace App\Http\Controllers;

use App\Contracts\AuditLoggerInterface;
use App\DTOs\TimelineItemData;
use App\Enums\DocumentPriority;
use App\Enums\DocumentStateName;
use App\Http\Requests\IndexDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function index(IndexDocumentRequest $request): View
    {
        Gate::authorize('viewAny', Document::class);

        $documents = Document::query()
            ->with(['category', 'responsibleUser', 'currentVersion.documentState'])
            ->filter($request->validated())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::getCachedAll();
        $states = DocumentState::getCachedAll();
        $priorities = DocumentPriority::cases();

        return view('documents.index', compact('documents', 'categories', 'states', 'priorities'));
    }

    public function create(): View
    {
        Gate::authorize('create', Document::class);

        $categories = Category::getCachedAll();
        $priorities = DocumentPriority::cases();

        return view('documents.create', compact('categories', 'priorities'));
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['responsible_user_id'] = auth()->id();

        $this->documentService->create($data);

        return redirect()->route('documents.index')->with('success', __('documents.flash_created'));
    }

    public function show(Document $document): View
    {
        Gate::authorize('view', $document);

        $document->load(['category', 'responsibleUser', 'versions.documentState']);

        $versionId = request('version_id');
        $version = $versionId
            ? $document->versions()->findOrFail($versionId)
            : $document->currentVersion;

        $activities = $this->auditLogger->getDocumentTimeline($document);

        $timelineItems = $activities->map(function ($activity) {
            return $this->mapActivityToTimelineItemData($activity);
        });

        return view('documents.show', compact('document', 'version', 'timelineItems'));
    }

    private function mapActivityToTimelineItemData(Activity $activity): TimelineItemData
    {
        $metadata = [];

        $title = match ($activity->event) {
            'document.created' => __('documents.audit_event_created'),
            'document.updated' => __('documents.audit_event_updated'),
            'document.deleted' => __('documents.audit_event_deleted'),
            'attachment.uploaded' => __('documents.audit_event_attachment_uploaded', ['filename' => $activity->properties['filename'] ?? '']),
            'attachment.deleted' => __('documents.audit_event_attachment_deleted', ['filename' => $activity->properties['filename'] ?? '']),
            'workflow.transition' => __('documents.audit_event_workflow_transition'),
            default => $activity->description
        };

        if ($activity->event === 'document.updated' && $activity->properties->count() > 0) {
            $changes = [];
            foreach ($activity->properties as $key => $value) {
                $changes[] = __('documents.audit_field_updated', ['field' => ucfirst($key)]);
            }
            $metadata[__('documents.audit_changes')] = $changes;
        }

        if ($activity->event === 'workflow.transition') {
            $fromKey = $activity->properties['from_state'] ?? '?';
            $toKey = $activity->properties['to_state'] ?? '?';

            $from = DocumentStateName::tryFrom($fromKey)?->label() ?? $fromKey;
            $to = DocumentStateName::tryFrom($toKey)?->label() ?? $toKey;

            $metadata['transition'] = __('documents.audit_transition', [
                'from' => $from,
                'to' => $to,
            ]);
        }

        return new TimelineItemData(
            type: 'audit',
            title: $title,
            timestamp: $activity->created_at,
            actor: $activity->causer->name ?? __('documents.audit_causer'),
            description: null,
            metadata: $metadata,
            url: null
        );
    }

    public function edit(Document $document): View
    {
        Gate::authorize('update', $document);

        $categories = Category::getCachedAll();
        $priorities = DocumentPriority::cases();

        $document->load('currentVersion');

        return view('documents.edit', compact('document', 'categories', 'priorities'));
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->documentService->update($document, $request->validated());

        return redirect()->route('documents.index')->with('success', __('documents.flash_updated'));
    }

    public function destroy(Document $document): RedirectResponse
    {
        Gate::authorize('delete', $document);

        $this->documentService->delete($document);

        return redirect()->route('documents.index')->with('success', __('documents.flash_deleted'));
    }
}
