<?php

namespace App\Listeners;

use App\Events\DocumentAssigned;
use App\Notifications\DocumentAssignedNotification;

class SendDocumentAssignedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DocumentAssigned $event): void
    {
        if ($event->document->responsibleUser) {
            $event->document->responsibleUser->notify(
                new DocumentAssignedNotification($event->document)
            );
        }
    }
}
