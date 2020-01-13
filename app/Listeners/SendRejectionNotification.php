<?php

namespace App\Listeners;

use App\Events\AbsenceRejected;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRejectionNotification
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
     * @param  AbsenceRejected  $event
     * @return void
     */
    public function handle(AbsenceRejected $event)
    {
        $users = clone $event->absence->user->approvers;
        $users->push($event->absence->user);

        if (env('THIS_IS_MY_DEV_HOST')) {
            foreach($users as $user) $user->email = 'dev@peregrinus.de';
        }

        Mail::to($users)->send(new \App\Mail\AbsenceRejected($event->absence));
    }
}
