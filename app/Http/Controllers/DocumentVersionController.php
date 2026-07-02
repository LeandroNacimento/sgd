<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;
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
        // Only operators can create new versions, and it must be a published document.
        Gate::authorize('createVersion', $document);

        try {
            $this->versionService->createNewVersion($document);

            return redirect()->route('documents.show', $document)->with('success', __('documents.flash_new_version'));
        } catch (\Exception $e) {
            return back()->withErrors(['versioning' => $e->getMessage()]);
        }
    }

    public function revert(Document $document, DocumentVersion $version): RedirectResponse
    {
        Gate::authorize('revertVersion', $document);

        try {
            $this->versionService->revertToVersion($document, $version);

            return redirect()->route('documents.show', $document)->with('success', __('documents.flash_reverted'));
        } catch (\Exception $e) {
            return back()->withErrors(['versioning' => $e->getMessage()]);
        }
    }
}
