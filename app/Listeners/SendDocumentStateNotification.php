<?php

namespace App\Listeners;

use App\Enums\DocumentStateName;
use App\Enums\Role;
use App\Events\DocumentStateChanged;
use App\Models\User;
use App\Notifications\DocumentStateChangedNotification;
use Illuminate\Support\Facades\Notification;

class SendDocumentStateNotification
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
    public function handle(DocumentStateChanged $event): void
    {
        $notification = new DocumentStateChangedNotification(
            $event->document,
            $event->oldStateName,
            $event->newStateName
        );

        if ($event->newStateName === DocumentStateName::InReview->value) {
            // Notify all admins
            $admins = User::whereHas('role', function ($query) {
                $query->where('name', Role::Administrator->value);
            })->get();

            Notification::send($admins, $notification);
        } else {
            // Notify responsible user
            if ($event->document->responsibleUser) {
                $event->document->responsibleUser->notify($notification);
            }
        }
    }
}
