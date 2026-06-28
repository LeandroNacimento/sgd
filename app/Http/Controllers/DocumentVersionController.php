<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentVersionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class DocumentVersionController extends Controller
{
    public function __construct(
        private readonly DocumentVersionService $versionService
    ) {}

    public function store(Document $document): RedirectResponse
    {
        // Only operators (or those who can update the document) can create new versions.
        // It must also be a published document.
        Gate::authorize('update', $document);

        try {
            $this->versionService->createNewVersion($document);

            return redirect()->route('documents.show', $document)->with('success', 'New draft version created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['versioning' => $e->getMessage()]);
        }
    }
}
