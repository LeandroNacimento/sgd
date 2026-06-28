<?php

namespace App\Services;

use App\Contracts\AuditLoggerInterface;
use App\Models\DocumentVersion;
use App\Models\User;

class SpatieAuditLogger implements AuditLoggerInterface
{
    public function logDocumentCreated(DocumentVersion $version, User $user): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($version)
            ->event('document.created')
            ->log('Document version was created');
    }

    public function logDocumentUpdated(DocumentVersion $version, User $user, array $changes): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($version)
            ->event('document.updated')
            ->withProperties($changes)
            ->log('Document version was updated');
    }

    public function logDocumentDeleted(DocumentVersion $version, User $user): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($version)
            ->event('document.deleted')
            ->log('Document was deleted');
    }

    public function logAttachmentUploaded(DocumentVersion $version, User $user, string $filename): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($version)
            ->event('attachment.uploaded')
            ->withProperties(['filename' => $filename])
            ->log("Attachment uploaded: {$filename}");
    }

    public function logAttachmentDeleted(DocumentVersion $version, User $user, string $filename): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($version)
            ->event('attachment.deleted')
            ->withProperties(['filename' => $filename])
            ->log("Attachment deleted: {$filename}");
    }

    public function logWorkflowTransition(DocumentVersion $version, User $user, string $fromState, string $toState): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($version)
            ->event('workflow.transition')
            ->withProperties([
                'from_state' => $fromState,
                'to_state' => $toState,
            ])
            ->log("Workflow transitioned from {$fromState} to {$toState}");
    }
}
