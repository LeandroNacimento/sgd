<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'human_readable_size' => $this->human_readable_size,
            'created_at' => $this->created_at,
            'url' => route('api.v1.documents.attachments.download', [
                'document' => $this->model_id,
                'media' => $this->id
            ]),
        ];
    }
}
