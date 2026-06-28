<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentVersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'version_number' => $this->version_number,
            'semantic_version' => $this->semantic_version,
            'title' => $this->title,
            'description' => $this->description,
            'state' => $this->documentState->name->label(),
            'created_at' => $this->created_at,
        ];
    }
}
