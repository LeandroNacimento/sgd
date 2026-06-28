<?php

namespace App\Models;

use App\Enums\DocumentPriority;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    public ?string $_temp_title = null;

    public ?string $_temp_description = null;

    public ?int $_temp_document_state_id = null;

    protected $fillable = [
        'code',
        'priority',
        'category_id',
        'responsible_user_id',
        'current_version_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Document $document) {
            // Temporary workaround for factories and tests that pass legacy columns.
            // We store them in a temporary property for DocumentFactory to read,
            // and unset them from attributes so MySQL doesn't fail.
            $document->_temp_title = $document->title ?? null;
            $document->_temp_description = $document->description ?? null;
            $document->_temp_document_state_id = $document->document_state_id ?? null;

            unset($document->title);
            unset($document->description);
            unset($document->document_state_id);
        });
    }

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

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('currentVersion', function ($q2) use ($search) {
                        $q2->where('title', 'like', "%{$search}%");
                    });
            });
        })
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($filters['document_state_id'] ?? null, function ($query, $stateId) {
                $query->whereHas('currentVersion', function ($q) use ($stateId) {
                    $q->where('document_state_id', $stateId);
                });
            })
            ->when($filters['priority'] ?? null, function ($query, $priority) {
                $query->where('priority', $priority);
            });
    }

    // Convenience delegates for the current version's state
    public function isDraft(): bool
    {
        return $this->currentVersion && $this->currentVersion->isDraft();
    }

    public function isInReview(): bool
    {
        return $this->currentVersion && $this->currentVersion->isInReview();
    }

    public function isPublished(): bool
    {
        return $this->currentVersion && $this->currentVersion->isPublished();
    }

    public function isArchived(): bool
    {
        return $this->currentVersion && $this->currentVersion->isArchived();
    }
}
