<?php

namespace App\Services;

use App\Contracts\AuditLoggerInterface;
use App\Enums\DocumentStateName;
use App\Exceptions\InvalidDocumentTransitionException;
use App\Models\DocumentState;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\DB;

class DocumentVersionWorkflowService
{
    public function __construct(
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function submitForReview(DocumentVersion $version): void
    {
        if (! $version->isDraft()) {
            throw new InvalidDocumentTransitionException($version->documentState->name, DocumentStateName::InReview->value);
        }

        $this->transitionTo($version, DocumentStateName::InReview);
    }

    public function publish(DocumentVersion $version): void
    {
        if (! $version->isInReview()) {
            throw new InvalidDocumentTransitionException($version->documentState->name, DocumentStateName::Published->value);
        }

        DB::transaction(function () use ($version) {
            $this->transitionTo($version, DocumentStateName::Published);

            // Archive all previously published versions for this document
            $archivedState = DocumentState::where('name', DocumentStateName::Archived->value)->firstOrFail();

            $previousPublishedVersions = DocumentVersion::where('document_id', $version->document_id)
                ->where('id', '!=', $version->id)
                ->whereHas('documentState', function ($q) {
                    $q->where('name', DocumentStateName::Published->value);
                })
                ->get();

            foreach ($previousPublishedVersions as $prevVersion) {
                $oldStateName = $prevVersion->documentState->name;
                $prevVersion->update(['document_state_id' => $archivedState->id]);

                if (auth()->check()) {
                    $this->auditLogger->logWorkflowTransition($prevVersion, auth()->user(), $oldStateName, DocumentStateName::Archived->value);
                }
            }

            // Ensure the Document's current_version_id is updated if needed
            $document = $version->document;
            if ($document->current_version_id !== $version->id) {
                $document->update(['current_version_id' => $version->id]);
            }
        });
    }

    public function reject(DocumentVersion $version): void
    {
        if (! $version->isInReview()) {
            throw new InvalidDocumentTransitionException($version->documentState->name, DocumentStateName::Draft->value);
        }

        $this->transitionTo($version, DocumentStateName::Draft);
    }

    public function archive(DocumentVersion $version): void
    {
        if (! $version->isPublished()) {
            throw new InvalidDocumentTransitionException($version->documentState->name, DocumentStateName::Archived->value);
        }

        $this->transitionTo($version, DocumentStateName::Archived);
    }

    private function transitionTo(DocumentVersion $version, DocumentStateName $stateName): void
    {
        $oldStateName = $version->documentState->name;

        $state = DocumentState::where('name', $stateName->value)->firstOrFail();

        $version->update([
            'document_state_id' => $state->id,
        ]);

        if (auth()->check()) {
            $this->auditLogger->logWorkflowTransition($version, auth()->user(), $oldStateName, $stateName->value);
        }
    }
}
