<?php

namespace App\Mail;

use App\Service;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServiceCreated extends Mailable
{
    use Queueable, SerializesModels;


    /** @var Service $service */
    protected $service;

    /** @var User $user */
    protected $user;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Service $service, User $user)
    {
        $this->service = $service;
        $this->user = $user;
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

        return $this->subject('Neuer Gottesdienst am '.$this->service->day->date->format('d.m.Y').', '.$this->service->timeText().' ('.$this->service->locationText().')')
            ->view('mail.notifications.service-created')->with([
                'service' => $this->service,
                'user' => $this->user,
            ])->attachData($ics, $icsTitle, [
                'mime' => 'text/calendar',
            ]);
    }
}
