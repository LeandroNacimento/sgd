<?php

namespace App\Services;

use App\Models\Document;

class DocumentCodeGenerator
{
    /**
     * Generates a unique document code like DOC-YYYY-XXXXXX
     * Must be called within a database transaction.
     */
    public function generate(): string
    {
        $year = date('Y');

        // We use lockForUpdate() to prevent race conditions within the transaction
        $lastDocument = Document::withTrashed()
            ->where('code', 'like', "DOC-{$year}-%")
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $sequence = 1;

        if ($lastDocument) {
            $parts = explode('-', $lastDocument->code);
            $sequence = (int) end($parts) + 1;
        }

        return sprintf('DOC-%s-%06d', $year, $sequence);
    }
}
