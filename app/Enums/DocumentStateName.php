<?php

namespace App\Enums;

enum DocumentStateName: string
{
    case Draft = 'Draft';
    case InReview = 'In Review';
    case Published = 'Published';
    case Archived = 'Archived';

    /**
     * Returns the deterministic translation key for the documents.states.* group.
     */
    public function translationKey(): string
    {
        return match ($this) {
            self::Draft => 'documents.states.draft',
            self::InReview => 'documents.states.in_review',
            self::Published => 'documents.states.published',
            self::Archived => 'documents.states.archived',
        };
    }

    /**
     * Returns the translated display label for the current locale.
     */
    public function label(): string
    {
        return __($this->translationKey());
    }
}
