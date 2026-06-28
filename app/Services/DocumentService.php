<?php

namespace App\Services;

use App\Contracts\AuditLoggerInterface;
use App\Models\Document;
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

            $document = Document::create($data);

            if (auth()->check()) {
                $this->auditLogger->logDocumentCreated($document, auth()->user());
            }

            return $document;
        });
    }

    public function update(Document $document, array $data): Document
    {
        $document->fill($data);

        $changes = $document->getDirty();

        if (! empty($changes)) {
            $document->save();

            if (auth()->check()) {
                $this->auditLogger->logDocumentUpdated($document, auth()->user(), $changes);
            }
        }

        return $document;
    }

    public function delete(Document $document): void
    {
        $document->delete();

        if (auth()->check()) {
            $this->auditLogger->logDocumentDeleted($document, auth()->user());
        }
    }

    public function addAttachment(Document $document, UploadedFile $file): void
    {
        if ($document->getMedia('attachments')->count() >= 5) {
            throw new \Exception('Maximum of 5 attachments allowed per document.');
        }

        $media = $document->addMedia($file)
            ->toMediaCollection('attachments');

        if (auth()->check()) {
            $this->auditLogger->logAttachmentUploaded($document, auth()->user(), $media->file_name);
        }
    }

    public function removeAttachment(Document $document, Media $media): void
    {
        $filename = $media->file_name;
        $media->delete();

        if (auth()->check()) {
            $this->auditLogger->logAttachmentDeleted($document, auth()->user(), $filename);
        }
    }
}
