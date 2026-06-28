<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidDocumentTransitionException;
use App\Models\Document;
use App\Services\DocumentVersionWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class DocumentWorkflowController extends Controller
{
    public function __construct(
        private readonly DocumentVersionWorkflowService $workflowService
    ) {}

    public function submitForReview(Document $document): RedirectResponse
    {
        Gate::authorize('submitForReview', $document);

        try {
            $this->workflowService->submitForReview($document->currentVersion);

            return back()->with('success', __('documents.flash_submitted'));
        } catch (InvalidDocumentTransitionException $e) {
            return back()->withErrors(['workflow' => $e->getMessage()]);
        }
    }

    public function publish(Document $document): RedirectResponse
    {
        Gate::authorize('publish', $document);

        try {
            $this->workflowService->publish($document->currentVersion);

            return back()->with('success', __('documents.flash_published'));
        } catch (InvalidDocumentTransitionException $e) {
            return back()->withErrors(['workflow' => $e->getMessage()]);
        }
    }

    public function reject(Document $document): RedirectResponse
    {
        Gate::authorize('reject', $document);

        try {
            $this->workflowService->reject($document->currentVersion);

            return back()->with('success', __('documents.flash_rejected'));
        } catch (InvalidDocumentTransitionException $e) {
            return back()->withErrors(['workflow' => $e->getMessage()]);
        }
    }

    public function archive(Document $document): RedirectResponse
    {
        Gate::authorize('archive', $document);

        try {
            $this->workflowService->archive($document->currentVersion);

            return back()->with('success', __('documents.flash_archived'));
        } catch (InvalidDocumentTransitionException $e) {
            return back()->withErrors(['workflow' => $e->getMessage()]);
        }
    }
}
