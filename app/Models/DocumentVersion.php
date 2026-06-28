<?php

namespace App\Models;

use App\Enums\DocumentStateName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DocumentVersion extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'document_id',
        'version_number',
        'title',
        'description',
        'document_state_id',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function documentState(): BelongsTo
    {
        return $this->belongsTo(DocumentState::class);
    }

    public function getSemanticVersionAttribute(): string
    {
        return "v{$this->version_number}.0";
    }

    public function isDraft(): bool
    {
        return $this->documentState->name === DocumentStateName::Draft->value;
    }

    public function isInReview(): bool
    {
        return $this->documentState->name === DocumentStateName::InReview->value;
    }

    public function isPublished(): bool
    {
        return $this->documentState->name === DocumentStateName::Published->value;
    }

    public function isArchived(): bool
    {
        return $this->documentState->name === DocumentStateName::Archived->value;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('local'); // Force private storage
    }
}
