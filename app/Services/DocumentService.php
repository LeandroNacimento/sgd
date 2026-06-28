<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DocumentService
{
    public function __construct(
        private readonly DocumentCodeGenerator $codeGenerator
    ) {}

    public function create(array $data): Document
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = $this->codeGenerator->generate();

            return Document::create($data);
        });
    }

    public function update(Document $document, array $data): Document
    {
        $document->update($data);

        return $document;
    }

    public function delete(Document $document): void
    {
        $document->delete();
    }

    public function addAttachment(Document $document, UploadedFile $file): void
    {
        if ($document->getMedia('attachments')->count() >= 5) {
            throw new \Exception('Maximum of 5 attachments allowed per document.');
        }

        $document->addMedia($file)
            ->toMediaCollection('attachments');
    }

    public function removeAttachment(Document $document, Media $media): void
    {
        // Spatie handles the DB and filesystem deletion.
        $media->delete();
    }
}
