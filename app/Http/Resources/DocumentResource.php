<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'priority' => $this->priority->label(),
            'responsible' => new UserResource($this->whenLoaded('responsibleUser')),
            'current_version' => new DocumentVersionResource($this->whenLoaded('currentVersion')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
