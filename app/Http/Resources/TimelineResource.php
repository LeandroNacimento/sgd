<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimelineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'timestamp' => $this->timestamp,
            'actor' => $this->actor,
            'actor_email' => $this->actorEmail,
            'metadata' => $this->metadata,
        ];
    }
}
