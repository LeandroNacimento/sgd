<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\DB;

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
}
