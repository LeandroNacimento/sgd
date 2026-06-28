<?php

namespace App\Services;

use App\Contracts\AuditLoggerInterface;
use App\Enums\DocumentStateName;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\DocumentVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DocumentService
{
    public function __construct(
        private readonly DocumentCodeGenerator $codeGenerator,
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function create(array $data): Document
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = $this->codeGenerator->generate();

            $document = Document::create([
                'code' => $data['code'],
                'priority' => $data['priority'],
                'category_id' => $data['category_id'],
                'responsible_user_id' => $data['responsible_user_id'],
            ]);

            $draftState = DocumentState::where('name', DocumentStateName::Draft->value)->firstOrFail();

            $version = DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => 1,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'document_state_id' => $draftState->id,
            ]);

            $document->update(['current_version_id' => $version->id]);

            if (auth()->check()) {
                $this->auditLogger->logDocumentCreated($version, auth()->user());
            }

            return $document;
        });
    }

    public function update(Document $document, array $data): Document
    {
        return DB::transaction(function () use ($document, $data) {
            // Document attributes
            $document->fill([
                'priority' => $data['priority'] ?? $document->priority,
                'category_id' => $data['category_id'] ?? $document->category_id,
            ]);

            if ($document->isDirty()) {
                $document->save();
            }

            // Version attributes
            $currentVersion = $document->currentVersion;
            if ($currentVersion && $currentVersion->isDraft()) {
                $currentVersion->fill([
                    'title' => $data['title'] ?? $currentVersion->title,
                    'description' => array_key_exists('description', $data) ? $data['description'] : $currentVersion->description,
                ]);

                $changes = $currentVersion->getDirty();

                if (! empty($changes)) {
                    $currentVersion->save();

                    if (auth()->check()) {
                        $this->auditLogger->logDocumentUpdated($currentVersion, auth()->user(), $changes);
                    }
                }
            }

            return $document;
        });
    }

    public function delete(Document $document): void
    {
        // Deleting the document cascades to its versions due to foreign key constraints,
        // but we want to let eloquent soft-delete them if soft deletes are configured.
        $document->versions()->delete();
        $document->delete();

        // The audit log typically logs on the version, but deleting the whole document is an event.
        // We log it against the current version.
        if (auth()->check() && $document->currentVersion) {
            $this->auditLogger->logDocumentDeleted($document->currentVersion, auth()->user());
        }
    }

    public function addAttachment(Document $document, UploadedFile $file): void
    {
        $version = $document->currentVersion;

        if ($version->getMedia('attachments')->count() >= 5) {
            throw new \Exception('Maximum of 5 attachments allowed per document version.');
        }

        $media = $version->addMedia($file)
            ->toMediaCollection('attachments');

        if (auth()->check()) {
            $this->auditLogger->logAttachmentUploaded($version, auth()->user(), $media->file_name);
        }
    }

    public function removeAttachment(Document $document, Media $media): void
    {
        $version = $document->currentVersion;
        $filename = $media->file_name;

        $media->delete();

        if (auth()->check()) {
            $this->auditLogger->logAttachmentDeleted($version, auth()->user(), $filename);
        }
    }
}
