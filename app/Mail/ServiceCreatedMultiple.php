<?php

namespace App\Mail;

use App\Service;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\View;

class ServiceCreatedMultiple extends AbstractServiceMailable
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->service->load(['location', 'day']);

        $ics = view('ical/ical', ['services' => $this->data['services'], 'token' => null, 'action' => null, 'key' => null])->render();
        $icsTitle = 'Gottesdienste.ics';

        return $this->subject('Mehrere neue Gottesdienste angelegt')
            ->view('mail.notifications.service-created-multiple')->with([
                'service' => $this->service,
                'user' => $this->user,
                'data' => $this->data,
            ])->attachData($ics, $icsTitle, [
                'mime' => 'text/calendar',
            ]);
    }
}
