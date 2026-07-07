<?php

namespace App\Contracts;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface AuditLoggerInterface
{
    public function logDocumentCreated(DocumentVersion $version, User $user): void;

    public function logDocumentUpdated(DocumentVersion $version, User $user, array $changes): void;

    public function logDocumentDeleted(DocumentVersion $version, User $user): void;

    public function logAttachmentUploaded(DocumentVersion $version, User $user, string $filename): void;

    public function logAttachmentDeleted(DocumentVersion $version, User $user, string $filename): void;

    public function logWorkflowTransition(DocumentVersion $version, User $user, string $fromState, string $toState): void;

    public function logDocumentReverted(DocumentVersion $version, User $user, int $revertedFromVersionNumber): void;

    /**
     * Get the full timeline of activities for all versions of a document.
     *
     * @return Collection
     */
    public function getDocumentTimeline(Document $document);

    public function logUserLogin(User $user): void;

    public function logUserLogout(User $user): void;

    public function logFailedLogin(string $email): void;
}
