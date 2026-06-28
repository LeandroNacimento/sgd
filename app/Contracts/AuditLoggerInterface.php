<?php

namespace App\Contracts;

use App\Models\Document;
use App\Models\User;

interface AuditLoggerInterface
{
    public function logDocumentCreated(Document $document, User $user): void;

    public function logDocumentUpdated(Document $document, User $user, array $changes): void;

    public function logDocumentDeleted(Document $document, User $user): void;

    public function logAttachmentUploaded(Document $document, User $user, string $filename): void;

    public function logAttachmentDeleted(Document $document, User $user, string $filename): void;

    public function logWorkflowTransition(Document $document, User $user, string $fromState, string $toState): void;
}
