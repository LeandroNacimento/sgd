<?php

namespace App\Models;

use App\Enums\DocumentPriority;
use App\Enums\DocumentStateName;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'description',
        'priority',
        'category_id',
        'document_state_id',
        'responsible_user_id',
    ];

    protected function casts(): array
    {
        return [
            'priority' => DocumentPriority::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function documentState(): BelongsTo
    {
        return $this->belongsTo(DocumentState::class);
    }

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        })
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($filters['document_state_id'] ?? null, function ($query, $stateId) {
                $query->where('document_state_id', $stateId);
            })
            ->when($filters['priority'] ?? null, function ($query, $priority) {
                $query->where('priority', $priority);
            });
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
