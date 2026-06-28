<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentAttachmentRequest;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentAttachmentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService
    ) {}

    public function store(StoreDocumentAttachmentRequest $request, Document $document): RedirectResponse
    {
        try {
            $this->documentService->addAttachment($document, $request->file('file'));

            return back()->with('success', 'Attachment uploaded successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => $e->getMessage()]);
        }
    }

    public function destroy(Document $document, Media $media): RedirectResponse
    {
        Gate::authorize('update', $document);

        if ($media->model_id !== $document->id || $media->model_type !== Document::class) {
            abort(404);
        }

        $this->documentService->removeAttachment($document, $media);

        return back()->with('success', 'Attachment removed successfully.');
    }

    public function download(Document $document, Media $media): BinaryFileResponse
    {
        Gate::authorize('view', $document);

        if ($media->model_id !== $document->id || $media->model_type !== Document::class) {
            abort(404);
        }

        return response()->download($media->getPath(), $media->file_name);
    }
}
