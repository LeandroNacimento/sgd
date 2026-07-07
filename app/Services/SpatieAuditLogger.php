<?php

namespace App\Services;

use App\Contracts\AuditLoggerInterface;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

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

    public function logDocumentReverted(DocumentVersion $version, User $user, int $revertedFromVersionNumber): void
    {
        activity()
            ->causedBy($user)
            ->performedOn($version)
            ->event('document.reverted')
            ->withProperties(['reverted_from_version_number' => $revertedFromVersionNumber])
            ->log("Reverted from version v{$revertedFromVersionNumber}.0");
    }

    public function getDocumentTimeline(Document $document)
    {
        $versionIds = $document->versions()->pluck('id');

        return Activity::where('subject_type', DocumentVersion::class)
            ->whereIn('subject_id', $versionIds)
            ->with('causer')
            ->latest()
            ->get();
    }

    public function logUserLogin(User $user): void
    {
        activity()
            ->causedBy($user)
            ->event('user.login')
            ->log('User logged in');
    }

    public function logUserLogout(User $user): void
    {
        activity()
            ->causedBy($user)
            ->event('user.logout')
            ->log('User logged out');
    }

    public function logFailedLogin(string $email): void
    {
        activity()
            ->event('user.failed_login')
            ->withProperties(['email' => $email])
            ->log('Failed login attempt');
    }
}
