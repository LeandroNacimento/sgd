<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('is-operator');
    }

    public function update(User $user, Document $document): bool
    {
        if (! $user->can('is-operator')) {
            return false;
        }

        // Rule: Documents can only be updated while in Draft
        return $document->isDraft();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->can('is-admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return $user->can('is-admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return $user->can('is-admin');
    }

    // Workflow Authorizations

    public function submitForReview(User $user, Document $document): bool
    {
        return $user->can('is-operator') && $document->isDraft();
    }

    public function publish(User $user, Document $document): bool
    {
        return $user->can('is-admin') && $document->isInReview();
    }

    public function reject(User $user, Document $document): bool
    {
        return $user->can('is-admin') && $document->isInReview();
    }

    public function archive(User $user, Document $document): bool
    {
        return $user->can('is-admin') && $document->isPublished();
    }
}
