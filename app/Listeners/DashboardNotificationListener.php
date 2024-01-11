<?php

namespace App\Listeners;

use App\Notifications\DashboardNotification;
use App\User;
use Illuminate\Support\Facades\Notification;

class DashboardNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $admins = User::whereHas('role', function ($query) {
            $query->where('role_id', 1);
        })->get();

        Notification::send($admins, new DashboardNotification($event->user));
    }
}
