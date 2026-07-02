<?php

namespace App\DTOs;

use Illuminate\Support\Carbon;

class TimelineItemData
{
    public function __construct(
        public string $type, // 'audit' | 'version'
        public string $title,
        public Carbon $timestamp,
        public ?string $actor = null,
        public ?string $description = null,
        public array $metadata = [], // e.g., ['state' => 'published', 'changes' => [...]]
        public ?string $url = null
    ) {}
}
