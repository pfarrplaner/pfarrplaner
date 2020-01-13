<?php

namespace App\Listeners;

use App\Events\AbsenceApproved;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendApprovalNotification
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
     * @param  \App\Events\OrderShipped  $event
     * @return void
     */
    public function handle(AbsenceApproved $event)
    {
        $users = clone $event->absence->user->approvers;
        $users->push($event->absence->user);

        if (env('THIS_IS_MY_DEV_HOST')) {
            foreach($users as $user) $user->email = 'chris@toph.de';
        }

        Log::debug('AbsenceApproved listener: Sende AbsenceApproved an '.$users->pluck('email')->unique->join(', '));
        Mail::to($users)->send(new \App\Mail\AbsenceApproved($event->absence, $event->approval));

    }
}