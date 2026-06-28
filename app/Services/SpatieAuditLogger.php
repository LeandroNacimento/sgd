<?php

namespace App\Services;

use App\Contracts\AuditLoggerInterface;
use App\Models\Document;
use App\Models\User;

class SpatieAuditLogger implements AuditLoggerInterface
{
    public function logDocumentCreated(Document $document, User $user): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($document)
            ->event('document.created')
            ->log('Document was created');
    }

    public function logDocumentUpdated(Document $document, User $user, array $changes): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($document)
            ->event('document.updated')
            ->withProperties($changes)
            ->log('Document was updated');
    }

    public function logDocumentDeleted(Document $document, User $user): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($document)
            ->event('document.deleted')
            ->log('Document was deleted');
    }

    public function logAttachmentUploaded(Document $document, User $user, string $filename): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($document)
            ->event('attachment.uploaded')
            ->withProperties(['filename' => $filename])
            ->log("Attachment uploaded: {$filename}");
    }

    public function logAttachmentDeleted(Document $document, User $user, string $filename): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($document)
            ->event('attachment.deleted')
            ->withProperties(['filename' => $filename])
            ->log("Attachment deleted: {$filename}");
    }

    public function logWorkflowTransition(Document $document, User $user, string $fromState, string $toState): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($document)
            ->event('workflow.transition')
            ->withProperties([
                'from_state' => $fromState,
                'to_state' => $toState,
            ])
            ->log("Workflow transitioned from {$fromState} to {$toState}");
    }
}
