<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentAttachmentRequest;
use App\Http\Resources\MediaResource;
use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DocumentAttachmentController extends Controller
{
    public function index(Document $document)
    {
        $this->authorize('view', $document);

        return MediaResource::collection($document->currentVersion->getMedia('attachments'));
    }

    public function store(StoreDocumentAttachmentRequest $request, Document $document)
    {
        $this->authorize('update', $document);

        $file = $request->file('file');
        
        $media = $document->currentVersion->addMedia($file)
            ->toMediaCollection('attachments');

        return new MediaResource($media);
    }

    public function download(Document $document, Media $media)
    {
        $this->authorize('view', $document);

        if ($media->model_type !== DocumentVersion::class || 
            !in_array($media->model_id, $document->versions()->pluck('id')->toArray())) {
            abort(404);
        }

        return response()->download($media->getPath(), $media->file_name);
    }

    public function destroy(Document $document, Media $media)
    {
        $this->authorize('update', $document);

        if ($media->model_type !== DocumentVersion::class || $media->model_id !== $document->current_version_id) {
            abort(404);
        }

        $media->delete();

        return response()->noContent();
    }
}
