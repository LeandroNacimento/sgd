<?php

namespace App\Services;

use App\Enums\DocumentStateName;
use App\Models\Document;
use App\Models\DocumentState;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\DB;

class DocumentVersionService
{
    public function __construct(private SpatieAuditLogger $auditLogger) {}

    /**
     * Clones the given published document version into a new Draft version.
     */
    public function createNewVersion(Document $document): DocumentVersion
    {
        $currentVersion = $document->currentVersion;

        if (! $currentVersion || ! $currentVersion->isPublished()) {
            throw new \Exception('Only published documents can generate new versions.');
        }

        return DB::transaction(function () use ($document, $currentVersion) {
            $draftState = DocumentState::where('name', DocumentStateName::Draft->value)->firstOrFail();

            $nextVersionNumber = $document->versions()->max('version_number') + 1;

            $newVersion = DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $nextVersionNumber,
                'title' => $currentVersion->title,
                'description' => $currentVersion->description,
                'document_state_id' => $draftState->id,
            ]);

            // Duplicate Media references without duplicating physical files
            foreach ($currentVersion->getMedia('attachments') as $media) {
                $newMedia = $media->replicate();
                $newMedia->model_type = DocumentVersion::class;
                $newMedia->model_id = $newVersion->id;

                $customProperties = $newMedia->custom_properties;
                $customProperties['original_media_id'] = $media->getCustomProperty('original_media_id', $media->id);
                $newMedia->custom_properties = $customProperties;

                $newMedia->save();
            }

            // Update the document's current_version_id to the new Draft version
            // so it appears as the active working version in lists and dashboards.
            $document->update(['current_version_id' => $newVersion->id]);

            $this->auditLogger->logDocumentCreated($newVersion, auth()->user());

            return $newVersion;
        });
    }
}
