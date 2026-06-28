<?php

namespace App\Services;

use App\Enums\DocumentStateName;
use App\Exceptions\InvalidDocumentTransitionException;
use App\Models\Document;
use App\Models\DocumentState;

class DocumentWorkflowService
{
    public function submitForReview(Document $document): void
    {
        if (! $document->isDraft()) {
            throw new InvalidDocumentTransitionException($document->documentState->name, DocumentStateName::InReview->value);
        }

        $this->transitionTo($document, DocumentStateName::InReview);
    }

    public function publish(Document $document): void
    {
        if (! $document->isInReview()) {
            throw new InvalidDocumentTransitionException($document->documentState->name, DocumentStateName::Published->value);
        }

        $this->transitionTo($document, DocumentStateName::Published);
    }

    public function reject(Document $document): void
    {
        if (! $document->isInReview()) {
            throw new InvalidDocumentTransitionException($document->documentState->name, DocumentStateName::Draft->value);
        }

        $this->transitionTo($document, DocumentStateName::Draft);
    }

    public function archive(Document $document): void
    {
        if (! $document->isPublished()) {
            throw new InvalidDocumentTransitionException($document->documentState->name, DocumentStateName::Archived->value);
        }

        $this->transitionTo($document, DocumentStateName::Archived);
    }

    private function transitionTo(Document $document, DocumentStateName $stateName): void
    {
        $state = DocumentState::where('name', $stateName->value)->firstOrFail();

        $document->update([
            'document_state_id' => $state->id,
        ]);
    }
}
