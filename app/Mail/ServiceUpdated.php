<?php

namespace App\Mail;

use App\Service;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServiceUpdated extends AbstractServiceMailable
{

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Service $service, array $data)
    {
        parent::__construct($user, $service, $data);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $this->service->load(['location', 'day']);

        $ics = view('ical/ical', ['services' => [$this->service], 'token' => null, 'action' => null, 'key' => null])->render();
        $icsTitle = 'GD '.$this->service->day->date->format('Ymd').' '.$this->service->timeText(false).' '.$this->service->locationText().'.ics';

        $diff = $this->service->diff();

        if (!is_object($diff['original']->day)) {
            $diff['original']->day = $this->service->day;
        }
        if (!is_object($diff['changed']->day)) {
            $diff['changed']->day = $this->service->day;
        }

        return $this->subject('Änderungen am Gottesdienst vom '.$diff['original']->day->date->format('d.m.Y').', '.$diff['original']->timeText().' ('.$diff['original']->locationText().')')
            ->view('mail.notifications.service-update')->with([
            'service' => $this->service,
            'original' => $diff['original'],
            'changed' => $diff['changed'],
            'changes' => $diff,
            'user' => $this->user,
        ])->attachData($ics, $icsTitle, [
                'mime' => 'text/calendar',
            ]);
    }
}
