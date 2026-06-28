<?php

namespace App\Contracts;

use App\Models\DocumentVersion;
use App\Models\User;

interface AuditLoggerInterface
{
    public function logDocumentCreated(DocumentVersion $version, User $user): void;

    public function logDocumentUpdated(DocumentVersion $version, User $user, array $changes): void;

    public function logDocumentDeleted(DocumentVersion $version, User $user): void;

    public function logAttachmentUploaded(DocumentVersion $version, User $user, string $filename): void;

    public function logAttachmentDeleted(DocumentVersion $version, User $user, string $filename): void;

    public function logWorkflowTransition(DocumentVersion $version, User $user, string $fromState, string $toState): void;
}
