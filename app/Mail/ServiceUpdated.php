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
    protected $original;
    protected $changes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Service $service, array $data)
    {
        parent::__construct($user, $service, $data);
        $this->original = $this->data['original'];
        $this->changes = $service->getDirty();

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

        return $this->subject('Änderungen am Gottesdienst vom '.$this->original->day->date->format('d.m.Y').', '.$this->original->timeText().' ('.$this->original->locationText().')')
            ->view('mail.notifications.service-update')->with([
            'service' => $this->service,
            'original' => $this->original,
            'changes' => $this->changes,
            'data' => $this->data,
            'user' => $this->user,
        ])->attachData($ics, $icsTitle, [
                'mime' => 'text/calendar',
            ]);
    }
}
